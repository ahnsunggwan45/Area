<?php

namespace ojy\area\command\area;

use crush\advancedCommand\command\ACommand;
use crush\advancedCommand\command\ACommandParameter;
use ojy\area\Area;
use ojy\area\AreaPlugin;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;
use pocketmine\Server;

class AreaMoveCommand extends Command
{

    public function __construct()
    {
        parent::__construct("땅 이동", "다른 땅으로 이동합니다.", "/땅 이동 [번호]", []);

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            if (isset($args[0])) {
                if (is_numeric($args[0])) {
                    $area = $am->getAreaById($sender->getLevel()->getFolderName(), $args[0]);
                    if ($area instanceof Area) {
                        $area->moveToArea($sender);
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$args[0]}번 땅을 찾을 수 없습니다.");
                    }
                } else {
                    $player = Server::getInstance()->getPlayer($args[0]) ?? $args[0];
                    if ($player instanceof Player)
                        $player = $player->getName();
                    $ids = implode(", ", $am->getPlayerAreaIds($sender->level->getFolderName(), $player));
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$player}님이 {$sender->level->getFolderName()} 월드에서 보유중인 땅: {$ids}");
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "/땅 이동 [땅번호]");
            }
        }
    }
}