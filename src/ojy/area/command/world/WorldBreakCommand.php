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

class WorldBreakCommand extends Command
{

    public function __construct()
    {
        parent::__construct('월드 부숨', '월드의 블럭 파괴를 허용/비허용 합니다.', '/월드 부숨');
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
            $wp->setCanBreak(($v = !$wp->get(PropertyTypes::TYPE_CAN_BREAK)));
            if ($v) {
                $sender->sendMessage(AreaPlugin::PREFIX . '해당 월드에서의 블럭 부수기를 가능하게 하였습니다.');
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '해당 월드에서의 블럭 부수기를 불가능하게 하였습니다.');
            }
        }
    }
}