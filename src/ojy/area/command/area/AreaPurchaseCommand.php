<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaPurchaseCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 구매", "현재 위치한 땅을 구매합니다.", "/땅 구매", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::purchase($sender, $am->getAreaByPlayer($sender));
        }
    }
}