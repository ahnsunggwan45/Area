<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaAccessCommand extends Command
{

    public function __construct()
    {
        parent::__construct('땅 접근', '다른 플레이어의 접근을 허용/비허용 합니다.', '/땅 접근', []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::access($sender, $am->getAreaByPlayer($sender));
        }
    }
}