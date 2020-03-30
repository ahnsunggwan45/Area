<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class WorldCreateCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 생성", "월드를 생성합니다.", "/월드 생성 [월드이름] [생성자]");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[1])) {
                $genlst = GeneratorManager::getGeneratorList();
                if (in_array($args[1], $genlst)) {
                    Server::getInstance()->generateLevel($args[0], 404, GeneratorManager::getGenerator($args[1]));
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$args[0]} 월드를 생성자 {$args[1]}로 생성했습니다.");
                } else {
                    $sender->sendMessage(AreaPlugin::PREFIX . "생성자를 찾을 수 없습니다.");
                }
            } else {
                $genlst = implode(", ", GeneratorManager::getGeneratorList());
                $sender->sendMessage(AreaPlugin::PREFIX . "/월드 생성 [월드이름] [생성자]");
                $sender->sendMessage(AreaPlugin::PREFIX . "생성자 목록: {$genlst}");
            }
        }
    }
}