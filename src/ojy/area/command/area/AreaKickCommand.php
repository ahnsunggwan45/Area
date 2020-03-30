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

class AreaKickCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 추방", "공유된 플레이어를 추방합니다.", "/땅 추방 [닉네임]", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            if (isset($args[0])) {
                AreaUtil::kick($sender, $am->getAreaByPlayer($sender), implode(" ", $args));
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "/땅 추방 [닉네임]");
            }
        }
    }
}