<?php

namespace ojy\area\command\area;

use ojy\area\Area;
use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use ojy\area\util\AreaUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class AreaCreateCommand extends Command
{

    public function __construct()
    {
        parent::__construct('땅 생성', '땅을 생성합니다.', '/땅 생성');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if ($sender instanceof Player) {

            $am = AreaPlugin::getInstance()->getAreaManager();
            if (AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->level->getFolderName())->get(PropertyTypes::WORLD_AUTO_CREATE) || $sender->isOp()) {
                $area = $am->addArea($sender->getLevel()->getFolderName(), $am->positionData($sender->x - 15, $sender->z - 15, $sender->x + 15, $sender->z + 15));
                if ($area instanceof Area) {
                    $area->getProperties()->setOwner($sender->getName());
                    $sender->sendMessage(AreaPlugin::PREFIX . "{$area->getId()}번 땅을 생성했습니다.");
                } elseif (is_int($area)) {
                    $sender->sendMessage(AreaPlugin::PREFIX . "생성하려는 땅이 {$area}번 땅과 겹칩니다.");
                } else {
                    $sender->sendMessage(AreaPlugin::PREFIX . '땅을 생성할 수 없습니다.');
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '땅을 생성할 수 없는 월드입니다.');
            }
        }
    }
}