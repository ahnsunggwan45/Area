<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaSetSpawnCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 스폰설정", "현재 위치한 땅의 스폰을 설정합니다.", "/땅 스폰설정", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::setSpawn($sender, $am->getAreaByPlayer($sender));
        }
    }
}