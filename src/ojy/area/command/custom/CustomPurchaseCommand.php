<?php

namespace ojy\area\command\custom;

use ojy\area\Area;
use ojy\area\AreaPlugin;
use ojy\area\generator\IslandGenerator;
use ojy\area\generator\SkyLandGenerator;
use ojy\coin\Coin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\Server;

class CustomPurchaseCommand extends Command
{

    /** @var Level|null */
    protected $world;

    public function __construct(string $name)
    {
        $this->world = Server::getInstance()->getLevelByName($name);
        parent::__construct("{$name} 구매", "{$name}월드에 있는 땅을 구매합니다.", "/{$name} 구매", []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     * @throws \ojy\area\exception\InvalidPositionDataException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $am = AreaPlugin::getInstance()->getAreaManager();
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($this->world->getFolderName());
            $start_time = microtime();
            $price = $wp->getAreaPrice();
            if (Coin::getCoin($sender) >= $price || $sender->isOp()) {
                if ($wp->getMaxAreaCount() > count($am->getPlayerAreas($this->world->getFolderName(), $sender->getName())) || $sender->isOp()) {
                    $generator = GeneratorManager::getGenerator($this->world->getProvider()->getGenerator());
                    if ($generator === IslandGenerator::class || $generator === SkyLandGenerator::class) {
                        if (count(($areas = AreaPlugin::getInstance()->getAreaManager()->canAvailablePurchaseAreas($this->world->getFolderName()))) >= 1) {
                            $area = $areas[0];
                            unset($areas);
                            if ($area instanceof Area) {
                                $area->getProperties()->setOwner($sender->getName());
                                $sender->sendMessage(AreaPlugin::PREFIX . "{$this->world->getFolderName()} {$area->getId()}번 땅을 구매했습니다.");
                                return true;
                            }
                        }
                        $id = $am->getNextId($this->world->getFolderName());
                        $indexZ = floor($id / 200);
                        $indexX = $id % 200;
                        $center = new Vector2(104 + $indexX * 200 - ($id % 2 === 1 ? 8 : 0), 104 + $indexZ * 200);
                        $area = $am->addArea($this->world->getFolderName(), $am->positionData($center->x - 90, $center->y - 90, $center->x + 90, $center->y + 90));
                        if ($area instanceof Area) {
                            $price = $wp->getAreaPrice();
                            if (Coin::getCoin($sender) >= $price || $sender->isOp()) {
                                if ($generator === IslandGenerator::class) {
                                    $preset = $wp->getPreset();
                                    IslandGenerator::onGenerate($this->world, $center->x >> 4, $center->y >> 4, $preset);
                                }
                                if ($generator === SkyLandGenerator::class)
                                    SkyLandGenerator::onGenerate($this->world, $center->x >> 4, $center->y >> 4);
                                if (!$sender->isOp())
                                    Coin::reduceCoin($sender, $price);
                                $area->getProperties()->setOwner($sender->getName());
                                $sender->sendMessage(AreaPlugin::PREFIX . "{$this->world->getFolderName()} {$area->getId()}번 땅을 구매했습니다.");
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
                            $sender->sendMessage(AreaPlugin::PREFIX . '오류가 발생하였습니다.');
                        }
                    } else {
                        $areas = $am->canAvailablePurchaseAreas($this->world->getFolderName());
                        if (count($areas) > 0) {
                            if (isset($areas[0])) {
                                $area = $areas[0];
                                if ($area instanceof Area) {
                                    $price = $wp->getAreaPrice();
                                    if (Coin::getCoin($sender) >= $price) {
                                        Coin::reduceCoin($sender, $price);
                                        $area->getProperties()->setOwner($sender->getName());
                                        $sender->sendMessage(AreaPlugin::PREFIX . "{$this->world->getFolderName()} {$area->getId()}번 땅을 구매했습니다.");
                                    } else {
                                        $sender->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다. (구매가: {$price} 코인)");
                                    }
                                }
                            }
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . '구매 가능한 땅이 없습니다.');
                        }
                        unset($areas);
                    }
                } else {
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$this->world->getFolderName()}에 땅을 최대치로 보유중입니다.");
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다. (구매가: {$price} 코인)");
            }
        }
        return true;
    }
}