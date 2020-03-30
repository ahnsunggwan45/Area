<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class WorldInventorySaveCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 인벤세이브", "현재 위치한 월드의 인벤세이브를 활성화/비활성화 합니다.", "/월드 인벤세이브");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
            $wp->setInventorySave(($v = !$wp->get(PropertyTypes::TYPE_INVENTORY_SAVE)));
            if ($v) {
                $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 인벤세이브를 활성화했습니다.");
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "해당 월드에서의 인벤세이브를 비활성화했습니다.");
            }
        }
    }
}