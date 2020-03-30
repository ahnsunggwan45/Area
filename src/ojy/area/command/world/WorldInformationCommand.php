<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class WorldInformationCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 정보", "현재 위치한 월드에대한 정보를 확인합니다.", "/월드 정보");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
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
            $player_list = implode(", ", array_map(function (Player $player) {
                return $player->getName();
            }, $sender->level->getPlayers()));
            $sender->sendMessage("§7월드 유저 수 : {$player_count}명\n§7{$player_list}");
        }
    }
}