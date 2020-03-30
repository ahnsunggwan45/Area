<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaInformationCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 정보", "현재 위치한 땅의 정보를 확인합니다.", "/땅 정보", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::information($sender, $am->getAreaByPlayer($sender));
        }
    }
}