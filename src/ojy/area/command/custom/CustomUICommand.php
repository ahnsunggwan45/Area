<?php

namespace ojy\area\command\custom;

use name\uimanager\element\Button;
use name\uimanager\SimpleForm;
use ojy\area\Area;
use ojy\area\AreaPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

class CustomUICommand extends Command implements Listener
{

    /** @var Level|null */
    protected static $world;

    public function __construct(string $name)
    {
        parent::__construct("{$name}", "{$name}월드 명령어입니다.", "/{$name}", []);
        self::$world = Server::getInstance()->getLevelByName($name);

        Server::getInstance()->getPluginManager()->registerEvents($this, AreaPlugin::getInstance());
    }

    public static function sendMainUI(Player $player)
    {
        $world = self::$world;
        $form = new SimpleForm("§l{$world->getFolderName()}", "\n§f이용하실 기능을 선택하세요.\n");
        $form->addButton(new Button("구매하기\n{$world->getFolderName()} 월드에 존재하는 땅을 구매합니다."));
        $form->addButton(new Button("이동하기\n공유 되었거나 소유중인 섬으로 이동"));
        $area = AreaPlugin::getInstance()->getAreaManager()->getAreaByPlayer($player);
        if($area instanceof Area){
            if($area->getProperties()->isResident($player->getName())){
                $form->addButton(new Button("나가기\n땅 공유목록에서 자신을 제거합니다."));
                if(strtolower($area->getProperties()->getOwner()) === strtolower($player->getName())){
                    $form->addButton(new Button("공유하기\n당신의 땅을 다른사람과 공유합니다."));
                    $form->addButton(new Button("추방하기\n땅에 공유된사람을 추방합니다."));
                    $form->addButton(new Button("스폰설정\n땅의 스폰을 설정합니다."));
                }
            }
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

    }
}