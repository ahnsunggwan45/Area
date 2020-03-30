<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class WorldMaxAreaCountCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 땅최대보유수", "월드의 최대 보유 가능한 땅의 개수를 설정합니다.", "/월드 땅최대보유수 [개수]");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[0]) && is_numeric($args[0])) {
                $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                $wp->setMaxAreaCount($args[0]);
                $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드의 땅 최대 보유수를 {$args[0]}개로 설정하였습니다!");
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "/월드 땅최대보유수 [개수]");
            }
        }
    }
}