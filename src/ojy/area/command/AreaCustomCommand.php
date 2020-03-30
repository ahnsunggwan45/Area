<?php

namespace ojy\area\command;

use ojy\area\Area;
use ojy\area\AreaManager;
use ojy\area\AreaPlugin;
use ojy\area\generator\IslandGenerator;
use ojy\area\generator\SkyLandGenerator;
use ojy\coin\Coin;
use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class AreaCustomCommand extends Command
{

    /** @var Level|null */
    protected $world;

    /**
     * AreaCustomCommand constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->world = Server::getInstance()->getLevelByName($name);
        parent::__construct($name, "{$name} 에 관한 명령어 입니다.", "/{$name}");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        // 이동, 구매
        if ($sender instanceof Player) {
            $am = AreaPlugin::getInstance()->getAreaManager();
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($this->world->getFolderName());
            if (!isset($args[0]))
                $args[0] = 'x';
            switch ($args[0]) {
                case "목록":
                    $ids = AreaPlugin::getInstance()->getAreaManager()->getPlayerAreaIds($this->world->getFolderName(), $sender->getName());
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()} 월드에 보유중인 땅 목록: " . implode(", ", $ids));
                    break;
                case "구매":
                    $start_time = microtime();
                    $price = $wp->getAreaPrice();
                    if (Coin::getCoin($sender) >= $price || $sender->isOp()) {
                        if ($wp->getMaxAreaCount() > count($am->getPlayerAreas($this->getName(), $sender->getName())) || $sender->isOp()) {
                            $generator = GeneratorManager::getGenerator($this->world->getProvider()->getGenerator());
                            if ($generator === IslandGenerator::class || $generator === SkyLandGenerator::class) {
                                if (count(($areas = AreaPlugin::getInstance()->getAreaManager()->canAvailablePurchaseAreas($this->world->getFolderName()))) >= 1) {
                                    $area = $areas[0];
                                    if ($area instanceof Area) {
                                        $area->getProperties()->setOwner($sender->getName());
                                        $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()} {$area->getId()}번 땅을 구매했습니다.");
                                        return true;
                                    }
                                }
                                $id = $am->getNextId($this->getName());
                                $indexZ = floor($id / 200);
                                $indexX = $id % 200;
                                $center = new Vector2(104 + $indexX * 200 - ($id % 2 === 1 ? 8 : 0), 104 + $indexZ * 200);
                                $area = $am->addArea($this->getName(), $am->positionData($center->x - 90, $center->y - 90, $center->x + 90, $center->y + 90));
                                if ($area instanceof Area) {
                                    $price = $wp->getAreaPrice();
                                    if (Coin::getCoin($sender) >= $price || $sender->isOp()) {
                                        if ($generator === IslandGenerator::class)
                                            IslandGenerator::onGenerate($this->world, $center->x >> 4, $center->y >> 4);
                                        if ($generator === SkyLandGenerator::class)
                                            SkyLandGenerator::onGenerate($this->world, $center->x >> 4, $center->y >> 4);
                                        if (!$sender->isOp())
                                            Coin::reduceCoin($sender, $price);
                                        $area->getProperties()->setOwner($sender->getName());
                                        $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()} {$area->getId()}번 땅을 구매했습니다.");
                                        $end_time = microtime();
                                        $start_sec = explode(" ", $start_time);
                                        $end_sec = explode(" ", $end_time);
                                        $rap_micsec = $end_sec[0] - $start_sec[0];
                                        $rap_sec = $end_sec[1] - $start_sec[1];
                                        $rap = $rap_sec + $rap_micsec;
                                        Server::getInstance()->getLogger()->info("creating island access time: {$rap} second");
                                    } else {
                                        $sender->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다. (구매가: {$price} 코인)");
                                    }
                                } else {
                                    var_dump($area);
                                    $sender->sendMessage(AreaPlugin::PREFIX . "오류가 발생하였습니다.");
                                }
                            } else {
                                $areas = $am->canAvailablePurchaseAreas($this->getName());
                                if (count($areas) > 0) {
                                    if (isset($areas[0])) {
                                        $area = $areas[0];
                                        if ($area instanceof Area) {
                                            $price = $wp->getAreaPrice();
                                            if (Coin::getCoin($sender) >= $price) {
                                                Coin::reduceCoin($sender, $price);
                                                $area->getProperties()->setOwner($sender->getName());
                                                $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()} {$area->getId()}번 땅을 구매했습니다.");
                                            } else {
                                                $sender->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다. (구매가: {$price} 코인)");
                                            }
                                        }
                                    }
                                } else {
                                    $sender->sendMessage(AreaPlugin::PREFIX . "구매 가능한 땅이 없습니다.");
                                }
                            }
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()}에 땅을 최대치로 보유중입니다.");
                        }
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다. (구매가: {$price} 코인)");
                    }
                    break;
                case "이동":
                    if (isset($args[1]) && is_numeric($args[1])) {
                        $area = $am->getAreaById($this->getName(), $args[1]);
                        if ($area instanceof Area) {
                            $area->moveToArea($sender);
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$this->getName()} 월드에서 {$args[1]}번 땅을 찾을 수 없습니다.");
                        }
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/{$this->getName()} 이동 [번호]");
                    }
                    break;
                default:
                    $sender->sendMessage(AreaPlugin::PREFIX . "/{$this->getName()} 목록");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/{$this->getName()} 구매");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/{$this->getName()} 이동 [번호]");
                    break;
            }
        }
    }
}