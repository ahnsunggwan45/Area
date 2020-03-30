<?php

namespace ojy\area\command\custom;

use ojy\area\AreaPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

class CustomListCommand extends Command
{

    /** @var Level|null */
    protected $world;

    public function __construct(string $name)
    {
        $this->world = Server::getInstance()->getLevelByName($name);
        parent::__construct("{$name} 목록", "{$name}월드에 있는 땅 목록을 확인합니다.", "/{$name} 목록", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $am = AreaPlugin::getInstance()->getAreaManager();
            $ids = $am->getPlayerAreaIds($this->world->getFolderName(), $sender->getName());
            $sender->sendMessage(AreaPlugin::PREFIX . "{$this->world->getFolderName()} 월드에 보유중인 땅 목록: " . implode(', ', $ids));
        }
    }
}