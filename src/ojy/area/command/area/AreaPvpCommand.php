<?php

namespace ojy\area\command\area;

use crush\advancedCommand\command\ACommand;
use crush\advancedCommand\command\ACommandParameter;
use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;

class AreaPvpCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 전투", "땅에서의 전투를 허용/비허용 합니다.", "/땅 전투", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            AreaUtil::pvp($sender, $am->getAreaByPlayer($sender));
        }
    }
}