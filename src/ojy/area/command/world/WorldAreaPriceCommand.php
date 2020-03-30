<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class WorldAreaPriceCommand extends Command
{

    public function __construct()
    {
        parent::__construct('월드 땅가격', '월드의 땅가격을 설정합니다.', '/월드 땅가격 [가격]');
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[0]) && is_numeric($args[0]) && $args[0] > -1) {
                $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                $wp->setAreaPrice($args[0]);
                $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드의 땅 가격을 {$args[0]} 코인으로 설정하였습니다!");
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '/월드 땅가격 [가격]');
            }
        }
    }
}