<?php

namespace ojy\area\command;

use ojy\area\AreaPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class XYZCommand extends Command
{

    public function __construct()
    {
        parent::__construct('xyz', '현재 좌표를 확인합니다.', '/xyz');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            list($x, $y, $z) = [round($sender->getPosition()->x, 1), round($sender->getPosition()->y, 1), round($sender->getPosition()->z, 1)];
            $sender->sendMessage(AreaPlugin::PREFIX . "좌표: {$x}, {$y}, {$z}");
        }
    }
}