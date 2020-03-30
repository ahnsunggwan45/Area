<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class WorldMoveCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 이동", "월드 이동 명령어입니다.", "/월드 이동 [월드이름]");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                $name = implode(" ", $args);
                Server::getInstance()->loadLevel($name);
                if (($world = Server::getInstance()->getLevelByName($name)) instanceof Level) {
                    $sender->teleport($world->getSafeSpawn());
                } else {
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$name} 월드는 존재하지 않습니다.");
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . "/월드 이동 [월드이름] | 월드 이동 명령어입니다.");
                $sender->sendMessage(AreaPlugin::PREFIX . "월드 목록: " . implode(", ", array_map(function (Level $world) {
                        return $world->getFolderName();
                    }, Server::getInstance()->getLevels())));
            }
        }
    }
}