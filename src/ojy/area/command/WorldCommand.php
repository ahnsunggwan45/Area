<?php

namespace ojy\area\command;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class WorldCommand extends Command
{

    /**
     * WorldCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("월드", "월드 명령어입니다.", "/월드");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {

            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 정보");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 이동 [월드이름]");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 인벤세이브");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 설치");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 부숨");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 문");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 전투");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅가격 [가격]");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅최대보유수 [개수]");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅생성허용");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 수동생성허용");
            $sender->sendMessage(AreaPlugin::PREFIX . "/월드 생성 [월드이름] [생성자]");
            return true;
            /*if (!isset($args[0]))
                $args[0] = 'x';

            switch ($args[0]) {
                case "정보":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->level->getFolderName());
                    $sender->sendMessage("§l§b- - - - - §f월드 정보 §b- - - - -");
                    $sender->sendMessage("§7월드 이름 : {$sender->level->getFolderName()}");
                    $invsave = $wp->get(PropertyTypes::TYPE_INVENTORY_SAVE) ? "켜짐" : "꺼짐";
                    $sender->sendMessage("§7인벤세이브 : {$invsave}");
                    $place = $wp->get(PropertyTypes::TYPE_CAN_PLACE) ? "가능" : "불가능";
                    $sender->sendMessage("§7블럭 설치 : {$place}");
                    $break = $wp->get(PropertyTypes::TYPE_CAN_BREAK) ? "가능" : "불가능";
                    $sender->sendMessage("§7블럭 파괴 : {$break}");
                    $pvp = $wp->get(PropertyTypes::TYPE_PVP) ? "허용" : "비허용";
                    $sender->sendMessage("§7전투 : {$pvp}");
                    $max_count = $wp->getMaxAreaCount();
                    $sender->sendMessage("§7땅 최대 보유수 : {$max_count}개");
                    $area_price = $wp->getAreaPrice();
                    $sender->sendMessage("§7땅 가격 : {$area_price}코인");
                    $auto_create = $wp->get(PropertyTypes::WORLD_AUTO_CREATE) ? "허용" : "비허용";
                    $sender->sendMessage("§7땅 생성 : {$auto_create}");
                    $player_count = count($sender->level->getPlayers());
                    $sender->sendMessage("§7월드 유저 수 : {$player_count}");
                    break;
                case "인벤세이브":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setInventorySave(($v = !$wp->get(PropertyTypes::TYPE_INVENTORY_SAVE)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 인벤세이브를 활성화했습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 인벤세이브를 비활성화했습니다.");
                    }
                    break;

                case "이동":
                    if (isset($args[1])) {
                        unset($args[0]);
                        $name = implode(" ", $args);
                        Server::getInstance()->loadLevel($name);
                        if (($world = Server::getInstance()->getLevelByName($name)) instanceof Level) {
                            $sender->teleport($world->getSafeSpawn());
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$name} 월드는 존재하지 않습니다.");
                        }
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/월드 이동 [월드이름] | 월드 이동 명령어입니다.");
                        $sender->sendMessage(AreaPlugin::PREFIX . "월드 목록: " . implode(", ", array_map(function (Level $world) {
                                return $world->getFolderName();
                            }, Server::getInstance()->getLevels())));
                    }
                    break;

                case "설치":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setCanPlace(($v = !$wp->get(PropertyTypes::TYPE_CAN_PLACE)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 블럭 설치를 가능하게 하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 블럭 설치를 불가능하게 하였습니다.");
                    }
                    break;

                case "부숨":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setCanBreak(($v = !$wp->get(PropertyTypes::TYPE_CAN_BREAK)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 블럭 부수기를 가능하게 하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 블럭 부수기를 불가능하게 하였습니다.");
                    }
                    break;

                case "문":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setCanOpenDoor(($v = !$wp->get(PropertyTypes::TYPE_OPEN_DOOR)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 문 열기를 가능하게 하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 문 열기를 불가능하게 하였습니다.");
                    }
                    break;

                case "전투":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setPvp(($v = !$wp->get(PropertyTypes::TYPE_PVP)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 전투를 가능하게 하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 전투를 불가능하게 하였습니다.");
                    }
                    break;

                case "땅최대보유수":
                    if (isset($args[1]) && is_numeric($args[1])) {
                        $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                        $wp->setMaxAreaCount($args[1]);
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드의 땅 최대 보유수를 {$args[1]}개로 설정하였습니다!");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅최대보유수 [개수]");
                    }
                    break;

                case "땅생성허용":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setAutoCreate(($v = !$wp->get(PropertyTypes::WORLD_AUTO_CREATE)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 땅생성을 허용하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 땅생성을 비허용하였습니다.");
                    }
                    break;

                case "수동생성허용":
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setManualCreate(($v = !$wp->get(PropertyTypes::WORLD_MANUAL_CREATE)));
                    if ($v) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 땅 수동생성을 허용하였습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 땅 수동생성을 비허용하였습니다.");
                    }
                    break;

                case "땅가격":
                    if (isset($args[1]) && is_numeric($args[1]) && $args[1] > -1) {
                        $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                        $wp->setAreaPrice($args[1]);
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드의 땅 가격을 {$args[1]} 로 설정하였습니다!");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅가격 [가격]");
                    }
                    break;

                case "생성":
                    if (isset($args[2])) {
                        $genlst = GeneratorManager::getGeneratorList();
                        if (in_array($args[2], $genlst)) {
                            Server::getInstance()->generateLevel($args[1], 404, GeneratorManager::getGenerator($args[2]));
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$args[1]} 월드를 생성자 {$args[2]}로 생성했습니다.");
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "생성자를 찾을 수 없습니다.");
                        }
                    } else {
                        $genlst = implode(", ", GeneratorManager::getGeneratorList());
                        $sender->sendMessage(AreaPlugin::PREFIX . "/월드 생성 [월드이름] [생성자]");
                        $sender->sendMessage(AreaPlugin::PREFIX . "생성자 목록: {$genlst}");
                    }
                    break;

                default:
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 정보");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 이동 [월드이름]");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 인벤세이브");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 설치");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 부숨");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 문");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 전투");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅가격 [가격]");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅최대보유수 [개수]");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅생성허용");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 수동생성허용");
                    $sender->sendMessage(AreaPlugin::PREFIX . "/월드 생성 [월드이름] [생성자]");
                    break;
            }
            */
        } else {
            $sender->sendMessage(AreaPlugin::PREFIX . "이 명령어를 사용할 권한이 없습니다.");
        }
    }
}