<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaGoOutCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 나가기", "현재 위치한 땅의 소유권을 포기합니다.", "/땅 나가기", ["땅 소유권포기"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::goOut($sender, $am->getAreaByPlayer($sender));
        }
    }
}