<?php

namespace ojy\area;

use ojy\area\event\EnterAreaEvent;
use ojy\area\event\MoveAreaEvent;
use ojy\area\generator\FlainGenerator;
use pocketmine\block\Chest;
use pocketmine\block\CraftingTable;
use pocketmine\block\Door;
use pocketmine\block\EnderChest;
use pocketmine\entity\EntityIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\level\ChunkPopulateEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EventListener implements Listener
{

    private $areaManager;

    /** @var Task[] */
    protected $tasks = [];

    /**
     * EventListener constructor.
     */
    public function __construct()
    {
        $this->areaManager = AreaPlugin::getInstance()->getAreaManager();
        Server::getInstance()->getPluginManager()->registerEvents($this, AreaPlugin::getInstance());
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        if (AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($event->getPlayer()->getLevel()->getFolderName())->get(PropertyTypes::TYPE_INVENTORY_SAVE)) {
            $event->setKeepInventory(true);
        } else {
            $event->setKeepInventory(false);
        }
    }

    /**
     * @param MoveAreaEvent $event
     */
    public function onMove(MoveAreaEvent $event)
    {
        $player = $event->getPlayer();
        if ($event->getResult()) {
            $player->sendMessage(AreaPlugin::PREFIX . "{$event->getArea()->getWorldName()} 월드의 {$event->getArea()->getId()}번 땅으로 이동했습니다.");
        } else {
            $player->sendMessage(AreaPlugin::PREFIX . "{$event->getArea()->getId()}번 땅은 접근이 거부되어있습니다.");
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $task = new ClosureTask(function (int $currentTick) use ($player): void {
            $this->checkPlayer($player);
        });
        AreaPlugin::getInstance()->getScheduler()->scheduleRepeatingTask($task, mt_rand(15, 20));
        $this->tasks[$player->getName()] = $task;
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (isset($this->tasks[$player->getName()])) {
            $task = $this->tasks[$player->getName()];
            if ($task instanceof Task) {
                $task->getHandler()->cancel();
                Server::getInstance()->getLogger()->info('Area Task 캔슬 완료');
            }
        }
    }


    /**
     * @param EnterAreaEvent $event
     */
    public function onEnter(EnterAreaEvent $event)
    {
        $player = $event->getPlayer();
        $area = $event->getArea();
        $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($player->getPosition()->level->getFolderName());
        $owner = $area->getProperties()->getOwner() === '' ? "없음\n- 구매가: {$wp->getAreaPrice()}" : $area->getProperties()->getOwner();
        $player->sendPopup("§l§3[ {$area->getId()} 번 땅 ]\n§r§f- 주인: {$owner}\n");
    }

    /**
     * @param Player $player
     */
    public function checkPlayer(Player $player): void
    {
        $am = $this->areaManager;
        $area = $am->getAreaByXZ($player->getLevel()->getFolderName(), (int)$player->getPosition()->x, (int)$player->getPosition()->z);
        if ($area instanceof Area) {
            if (!isset($am->playerPosition[$player->getName()])) {
                if ($area->canAccess($player)) {
                    $am->setPlayerPosition($player, $area);
                    (new EnterAreaEvent($player, $area))->call();
                } else {
                    $player->teleport($player->getPosition()->level->getSafeSpawn());
                    $player->sendMessage(AreaPlugin::PREFIX . '이 땅은 접근할 수 없는 땅 입니다.');
                }
            } else {
                if (!Area::equals($area, $am->playerPosition[$player->getName()])) {
                    if ($area->canAccess($player)) {
                        $am->setPlayerPosition($player, $area);
                        (new EnterAreaEvent($player, $area))->call();
                    } else {
                        $player->teleport($player->getPosition()->level->getSafeSpawn());
                        $am->unsetPlayerPosition($player);
                        $player->sendMessage(AreaPlugin::PREFIX . '이 땅은 접근할 수 없는 땅 입니다.');
                    }
                } else {
                    if (!$area->canAccess($player)) {
                        $player->teleport($player->getPosition()->level->getSafeSpawn());
                        $am->unsetPlayerPosition($player);
                        $player->sendMessage(AreaPlugin::PREFIX . '이 땅은 접근할 수 없는 땅 입니다.');
                    }
                }
            }
        } else {
            $am->unsetPlayerPosition($player);
        }
    }

    public static function canPvp(Player $player): bool
    {
        if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByPlayer($player)) instanceof Area) {
            if (!$area->canPvp($player)) {
                return false;
            }
        } else {
            if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($player->getLevel()->getFolderName())->get(PropertyTypes::TYPE_PVP)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onHit(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            $attacker = $event->getDamager();
            if ($entity instanceof Player && $attacker instanceof Player) {
                if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByPlayer($entity)) instanceof Area) {
                    if (!$area->canPvp($entity)) {
                        $event->setCancelled();
                        //$attacker->sendPopup("§l§b< §r§7전투를 할 수 없는 지역입니다. §l§b>");
                    }
                } else {
                    if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($entity->getLevel()->getFolderName())->get(PropertyTypes::TYPE_PVP)) {
                        $event->setCancelled();
                        //$attacker->sendPopup("§l§b< §r§7전투를 할 수 없는 지역입니다! §l§b>");
                    }
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerBucketEmptyEvent $event
     * @handleCancelled true
     */
    public function onBucketEmpty(PlayerBucketEmptyEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlockClicked();
        if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
            if (!$area->canPlace($player)) {
                $event->setCancelled();
            }
        } else {
            if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_CAN_PLACE) && !$player->isOp()) {
                $event->setCancelled();
            }
        }
    }


    /**
     * @priority LOWEST
     * @param PlayerInteractEvent $event
     * @handleCancelled true
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if ($event->getAction() === $event::RIGHT_CLICK_BLOCK) {
            if ($block instanceof Door) {
                if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
                    if (!$area->canOpenDoor($player))
                        $event->setCancelled();
                } else {
                    if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_OPEN_DOOR) && !$player->isOp()) {
                        $event->setCancelled();
                    }
                }
            } elseif ($block instanceof Chest) {
                if ($block instanceof EnderChest) {
                    $event->setCancelled(false);
                    return;
                }
                if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
                    if (!$area->getProperties()->isResident($player->getName()) && !$player->isOp()) {
                        $event->setCancelled();
                    }
                } else {
                    if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_CAN_PLACE) && !$player->isOp()) {
                        $event->setCancelled();
                    }
                }
            } elseif ($block instanceof EnderChest) {
                $event->setCancelled(false);
            } elseif ($block instanceof CraftingTable) {
                $event->setCancelled(false);
            } else {
                if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
                    if (!$area->getProperties()->isResident($player->getName()))
                        $event->setCancelled();
                } else {
                    if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_CAN_PLACE) && !$player->isOp()) {
                        $event->setCancelled();
                    }
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param BlockBreakEvent $event
     * @handleCancelled true
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
            if (!$area->canBreak($player)) {
                $event->setCancelled();
            }
        } else {
            if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_CAN_BREAK) && !$player->isOp()) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param BlockPlaceEvent $event
     * @handleCancelled true
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (($area = AreaPlugin::getInstance()->getAreaManager()->getAreaByXZ($block->getLevel()->getFolderName(), $block->x, $block->z)) instanceof Area) {
            if (!$area->canPlace($player)) {
                $event->setCancelled();
                //$player->sendPopup("§l§b< §r§7블럭을 설치할 수 없습니다. §l§b>");
            }
        } else {
            if (!AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($block->getLevel()->getFolderName())->get(PropertyTypes::TYPE_CAN_PLACE) && !$player->isOp()) {
                $event->setCancelled();
                //$player->sendPopup("§l§b< §r§7블럭을 설치할 수 없습니다! §l§b>");
            }
        }
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLoadLevel(LevelLoadEvent $event)
    {
        $world = $event->getLevel();
        md(AreaPlugin::getInstance()->getDataFolder() . "{$world->getFolderName()}/");
        if (AreaPlugin::getInstance()->getAreaManager()->load($world->getFolderName())) {
            AreaPlugin::getInstance()->getWorldManager()->load($world->getFolderName());
            Server::getInstance()->getLogger()->info("success load {$world->getFolderName()} world's areas!");
        }
    }

    /**
     * @priority LOWEST
     * @param ChunkPopulateEvent $event
     */
    public function onPopulate(ChunkPopulateEvent $event)
    {
        $world = $event->getLevel();
        if (GeneratorManager::getGenerator($world->getProvider()->getGenerator()) === FlainGenerator::class) {
            $chunk = $event->getChunk();
            if ($chunk->getX() % 2 == 0 && $chunk->getZ() % 2 == 0) {
                $positionData = AreaPlugin::getInstance()->getAreaManager()->positionData(($chunk->getX() * 16) + 3, ($chunk->getZ() * 16) + 3, (($chunk->getX() + 2) * 16) - 4, (($chunk->getZ() + 2) * 16) - 4);
                AreaPlugin::getInstance()->getAreaManager()->addArea($world->getFolderName(), $positionData);
            }
        }
    }
}