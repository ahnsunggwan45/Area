<?php

namespace ojy\area\command\world;

use ojy\area\AreaPlugin;
use ojy\area\preset\PresetManager;
use ojy\area\PropertyTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;
use ssss\utils\SSSSUtils;

class WorldPresetCommand extends Command
{

    public function __construct()
    {
        parent::__construct("월드 프리셋", "월드의 프리셋을 설정합니다.", "/월드 프리셋 [프리셋이름]");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                $presetName = implode(" ", $args);
                if (PresetManager::isExistPreset($presetName)) {
                    $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($sender->getLevel()->getFolderName());
                    $wp->setPreset($presetName);
                    SSSSUtils::message($sender, "월드의 프리셋을 설정했습니다: " . $presetName);
                } else {
                    SSSSUtils::message($sender, '존재하지 않는 프리셋이름입니다: ' . $presetName);
                }
            } else {
                SSSSUtils::message($sender, $this->getUsage());
            }
        }
    }
}