<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class WorldAllowAutoCreateAreaCommand extends Command
{

    public function __construct()
    {
        parent::__construct('월드 땅생성허용', '월드의 땅 생성을 허용/비허용 합니다.', '/월드 땅생성허용');
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
            $wp->setAutoCreate(($v = !$wp->get(PropertyTypes::WORLD_AUTO_CREATE)));
            if ($v) {
                $sender->sendMessage(AreaPlugin::PREFIX . '해당 월드에서의 땅생성을 허용하였습니다.');
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '해당 월드에서의 땅생성을 비허용하였습니다.');
            }
        }
    }
}