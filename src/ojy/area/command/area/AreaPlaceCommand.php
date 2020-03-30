<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaPlaceCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 설치", "땅에서의 블럭 설치를 허용/비허용 합니다.", "/땅 설치", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::place($sender, $am->getAreaByPlayer($sender));
        }
    }
}