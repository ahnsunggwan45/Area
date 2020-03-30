<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaRemoveCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 삭제", "현재 위치한 땅을 삭제합니다. §c(복구불가능)", "/땅 삭제");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::remove($sender, $am->getAreaByPlayer($sender));
        }
    }
}