<?php

namespace ojy\area\command\area;

use ojy\area\AreaPlugin;
use ojy\area\preset\PresetManager;
use ojy\area\util\BlockSerializer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;
use ssss\utils\SSSSUtils;

class AddAreaPresetCommand extends Command implements Listener
{

    public function __construct()
    {
        parent::__construct('땅 프리셋추가', '땅 프리셋을 추가합니다.', '/땅 프리셋추가 [프리셋이름]', []);
        $this->setPermission(Permission::DEFAULT_OP);
        Server::getInstance()->getPluginManager()->registerEvents($this, AreaPlugin::getInstance());
    }

    public static $session = [];

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (isset(self::$session[$player->getName()])) {
            static $s = [];
            if (!isset($s[$player->getName()]) || (int)date('s') !== $s[$player->getName()]) {
                $s[$player->getName()] = (int)date('s');
                $data = self::$session[$player->getName()];
                if ($data['step'] === 1) {
                    self::$session[$player->getName()]['step'] = 2;
                    self::$session[$player->getName()]['position1'] = $event->getBlock()->asPosition();
                    SSSSUtils::message($player, '두 번째 지점을 설정해주세요.');
                } else {
                    $position1 = $data['position1'];
                    $presetName = $data['presetName'];
                    $serializer = new BlockSerializer($player->getLevel());
                    $serializer->serialize($position1, $event->getBlock()->asPosition());
                    PresetManager::setPreset($presetName, $serializer->getBuffer());
                    SSSSUtils::message($player, '프리셋 \'' . $presetName . '\'의 설정을 완료했습니다.');
                    unset(self::$session[$player->getName()]);
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        if (isset(self::$session[$event->getPlayer()->getName()]))
            unset(self::$session[$event->getPlayer()->getName()]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player && $sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                if ($args[0] === '중단') {
                    if (isset(self::$session[$sender->getName()]))
                        unset(self::$session[$sender->getName()]);
                    SSSSUtils::message($sender, '땅 프리셋 설정 작업을 중단하였습니다.');
                    return;
                }
                $presetName = implode(" ", $args);
                self::$session[$sender->getName()] = ['step' => 1, 'presetName' => $presetName];
                SSSSUtils::message($sender, '첫 번째 지점을 설정해주세요.');
                SSSSUtils::message($sender, '취소하시려면 \'/땅 프리셋추가 중단\' 을 입력하세요.');
            } else {
                SSSSUtils::message($sender, $this->getUsage());
            }
        }
    }
}