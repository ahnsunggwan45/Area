<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class WorldDoorCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 문", "월드의 문 사용을 허용/비허용 합니다.", "/월드 문");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
            $wp->setCanOpenDoor(($v = !$wp->get(PropertyTypes::TYPE_OPEN_DOOR)));
            if ($v) {
                $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 문 열기를 가능하게 하였습니다.");
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 문 열기를 불가능하게 하였습니다.");
            }
        }
    }
}