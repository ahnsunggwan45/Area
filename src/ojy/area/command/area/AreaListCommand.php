<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaListCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 목록", "땅 목록을 확인합니다.", "/땅 목록", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {
            AreaUtil::list($sender);
        }
    }
}