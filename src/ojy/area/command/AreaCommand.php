<?php

namespace ojy\area\command;

use crush\advancedCommand\command\ACommandParameter;
use ojy\area\Area;
use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;

class AreaCommand extends Command
{

    /**
     * AreaCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('땅', '땅 설정 명령어입니다.', '/땅', ['area']);

    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            //$queue = AreaPlugin::getInstance()->getQueue();
            //$am = AreaPlugin::getInstance()->getAreaManager();
            if (!isset($args[0]))
                $args[0] = 'x';

            switch ($args[0]) {

                /*case "공유":
                    if (isset($args[1])) {
                        unset($args[0]);
                        AreaUtil::share($sender, $am->getAreaByPlayer($sender), implode(" ", $args));
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/땅 공유 [닉네임]");
                    }
                    break;

                case "추방":
                    if (isset($args[1])) {
                        unset($args[0]);
                        AreaUtil::kick($sender, $am->getAreaByPlayer($sender), implode(" ", $args));
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/땅 추방 [닉네임]");
                    }
                    break;

                case "양도":
                    if (isset($args[1])) {
                        unset($args[0]);
                        AreaUtil::transfer($sender, $am->getAreaByPlayer($sender), implode(" ", $args));
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/땅 양도 [닉네임]");
                    }
                    break;

                case "접근":
                    AreaUtil::access($sender, $am->getAreaByPlayer($sender));
                    break;

                case "전투":
                    AreaUtil::pvp($sender, $am->getAreaByPlayer($sender));
                    break;

                case "문":
                    AreaUtil::door($sender, $am->getAreaByPlayer($sender));
                    break;

                case "설치":
                    AreaUtil::place($sender, $am->getAreaByPlayer($sender));
                    break;

                case "부숨":
                    AreaUtil::break($sender, $am->getAreaByPlayer($sender));
                    break;

                case "이동":
                    if (isset($args[1]) && is_numeric($args[1])) {
                        $area = $am->getAreaById($sender->getLevel()->getFolderName(), $args[1]);
                        if ($area instanceof Area) {
                            $area->moveToArea($sender);
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$args[1]}번 땅을 찾을 수 없습니다.");
                        }
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "/땅 이동 [땅번호]");
                    }
                    break;

                case "생성":
                    if (AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->level->getFolderName())->get(PropertyTypes::WORLD_AUTO_CREATE) || $sender->isOp()) {
                        $area = $am->addArea($sender->getLevel()->getFolderName(), $am->positionData($sender->x - 15, $sender->z - 15, $sender->x + 15, $sender->z + 15));
                        if ($area instanceof Area) {
                            $area->getProperties()->setOwner($sender->getName());
                            $sender->sendMessage(AreaPlugin::PREFIX . "{$area->getId()}번 땅을 생성했습니다.");
                        } elseif (is_int($area)) {
                            $sender->sendMessage(AreaPlugin::PREFIX . "생성하려는 땅이 {$area}번 땅과 겹칩니다.");
                        } else {
                            $sender->sendMessage(AreaPlugin::PREFIX . "땅을 생성할 수 없습니다.");
                        }
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "땅을 생성할 수 없는 월드입니다.");
                    }
                    break;

                case "구매":
                    AreaUtil::purchase($sender, $am->getAreaByPlayer($sender));
                    break;

                case "판매":
                    AreaUtil::sell($sender, $am->getAreaByPlayer($sender));
                    break;

                case "정보":
                    AreaUtil::information($sender, $am->getAreaByPlayer($sender));
                    break;

                case "목록":
                    AreaUtil::list($sender);
                    break;

                case "나가기":
                    AreaUtil::goOut($sender, $am->getAreaByPlayer($sender));
                    break;
                case "삭제":
                    AreaUtil::remove($sender, $am->getAreaByPlayer($sender));
                    break;
                */
                case '2':
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 접근');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 전투');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 문');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 설치');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 부숨');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 생성');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 수동생성');
                    break;
                default:
                    // TODO: 수동생성
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 구매');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 판매');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 삭제');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 나가기');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 이동 [땅번호]');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 공유 [닉네임]');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 추방 [닉네임]');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 양도 [닉네임]');
                    $sender->sendMessage(AreaPlugin::PREFIX . '/땅 2 | 명령어 2번째 페이지를 확인합니다.');
                    break;
            }
        }
    }
}