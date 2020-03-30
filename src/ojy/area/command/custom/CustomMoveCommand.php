<?php

namespace ojy\area\command\custom;

use ojy\area\Area;
use ojy\area\AreaPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

class CustomMoveCommand extends Command
{

    /** @var Level|null */
    protected $world;

    public function __construct(string $name)
    {
        $this->world = Server::getInstance()->getLevelByName($name);
        parent::__construct("{$name} 이동", "{$name}월드에 있는 땅으로 이동합니다.", "/{$name} 이동 [번호]", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $am = AreaPlugin::getInstance()->getAreaManager();
            if (isset($args[0])) {
                if (is_numeric($args[0])) {
                    $area = $am->getAreaById($this->world->getFolderName(), $args[0]);
                    if ($area instanceof Area) {
                        $area->moveToArea($sender);
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$args[0]}번 땅을 찾을 수 없습니다.");
                    }
                } else {
                    $player = Server::getInstance()->getPlayer($args[0]) ?? $args[0];
                    if ($player instanceof Player)
                        $player = $player->getName();
                    $ids = implode(", ", $am->getPlayerAreaIds($this->world->getFolderName(), $player));
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$player}님이 {$this->world->getFolderName()} 월드에서 보유중인 땅: {$ids}");
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "/{$this->getName()} [번호]");
            }
        }
    }
}