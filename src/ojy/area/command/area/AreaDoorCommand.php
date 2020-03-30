<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaDoorCommand extends Command
{

    public function __construct()
    {
        parent::__construct('땅 문', '땅에서의 문 사용을 허용/비허용 합니다.', '/땅 문', []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::door($sender, $am->getAreaByPlayer($sender));
        }
    }
}