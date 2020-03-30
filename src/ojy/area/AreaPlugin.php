<?php

namespace ojy\area;

use ojy\area\command\area\AddAreaPresetCommand;
use ojy\area\command\area\AreaAccessCommand;
use ojy\area\command\area\AreaBreakCommand;
use ojy\area\command\area\AreaCreateCommand;
use ojy\area\command\area\AreaDoorCommand;
use ojy\area\command\area\AreaGoOutCommand;
use ojy\area\command\area\AreaInformationCommand;
use ojy\area\command\area\AreaKickCommand;
use ojy\area\command\area\AreaListCommand;
use ojy\area\command\area\AreaMoveCommand;
use ojy\area\command\area\AreaPlaceCommand;
use ojy\area\command\area\AreaPurchaseCommand;
use ojy\area\command\area\AreaPvpCommand;
use ojy\area\command\area\AreaRemoveCommand;
use ojy\area\command\area\AreaSellCommand;
use ojy\area\command\area\AreaSetSpawnCommand;
use ojy\area\command\area\AreaShareCommand;
use ojy\area\command\area\AreaTransferCommand;
use ojy\area\command\AreaCommand;
use ojy\area\command\world\WorldAllowAutoCreateAreaCommand;
use ojy\area\command\world\WorldAllowManualCreateAreaCommand;
use ojy\area\command\world\WorldAreaPriceCommand;
use ojy\area\command\world\WorldBreakCommand;
use ojy\area\command\world\WorldCreateCommand;
use ojy\area\command\world\WorldDoorCommand;
use ojy\area\command\world\WorldInformationCommand;
use ojy\area\command\world\WorldInventorySaveCommand;
use ojy\area\command\world\WorldMaxAreaCountCommand;
use ojy\area\command\world\WorldMoveCommand;
use ojy\area\command\world\WorldPlaceCommand;
use ojy\area\command\world\WorldPresetCommand;
use ojy\area\command\world\WorldPvpCommand;
use ojy\area\command\WorldCommand;
use ojy\area\command\XYZCommand;
use ojy\area\convert\SimpleAreaConverter;
use ojy\area\generator\EmptyGenerator;
use ojy\area\generator\FlainGenerator;
use ojy\area\generator\IslandGenerator;
use ojy\area\generator\SkyLandGenerator;
use ojy\area\preset\PresetManager;
use ojy\area\queue\Queue;
use ojy\area\task\AutoSaveTask;
use ojy\area\task\CheckPlayerTask;
use ojy\area\world\WorldManager;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\scheduler\ClosureTask;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

function md($path)
{
    if (!file_exists($path))
        mkdir($path);
}

class AreaPlugin extends PluginBase
{
    /** @var string */
    public const PREFIX = '§l§b[알림] §r§7';

    /** @var AreaPlugin */
    private static $i;

    /** @var AreaManager */
    private $areaManager;

    /** @var WorldManager */
    private $worldManager;

    /** @var EventListener */
    private $listener;

    /** @var Queue */
    private $queue;

    public function onLoad()
    {
        self::$i = $this;
    }

    /**
     * @throws exception\InvalidPositionDataException
     */
    public function onEnable()
    {
        GeneratorManager::addGenerator(FlainGenerator::class, 'flain', true);
        GeneratorManager::addGenerator(IslandGenerator::class, 'island', true);
        GeneratorManager::addGenerator(SkyLandGenerator::class, 'skyland', true);
        GeneratorManager::addGenerator(EmptyGenerator::class, 'empty', true);
        ///////////////////////////////////////////////////////////////
        md($this->getDataFolder());
        md($this->getDataFolder() . 'SimpleAreaLegacy/');
        $worlds = array_diff(scandir($this->getServer()->getDataPath() . 'worlds'), ['.', '..']);
        foreach ($worlds as $world) {
            md($this->getDataFolder() . "{$world}/");
            $this->getServer()->loadLevel($world);
        }
        $this->worldManager = new WorldManager();
        $this->areaManager = new AreaManager();

        foreach ($worlds as $world) {
            if (file_exists(Server::getInstance()->getDataPath() . 'worlds/' . $world . '/protects.json')) {
                $c = SimpleAreaConverter::convert($world);
                if ($c > 0) {
                    $this->getLogger()->info("{$world} 월드의 데이터 {$c}개를 변환했습니다.");
                }
            }
        }
        unset($worlds);

        $this->getScheduler()->scheduleDelayedRepeatingTask(new AutoSaveTask(), 1200 * 20, 1200 * 20);
        // $this->getScheduler()->scheduleRepeatingTask(new CheckPlayerTask(), 10);

        foreach (
            [
                //AreaCommand::class,
                AreaShareCommand::class,
                AreaAccessCommand::class,
                AreaBreakCommand::class,
                AreaCreateCommand::class,
                AreaDoorCommand::class,
                AreaGoOutCommand::class,
                AreaInformationCommand::class,
                AreaKickCommand::class,
                AreaListCommand::class,
                AreaMoveCommand::class,
                AreaPlaceCommand::class,
                AreaPurchaseCommand::class,
                AreaPvpCommand::class,
                AreaRemoveCommand::class,
                AreaSellCommand::class,
                AreaTransferCommand::class,
                AreaSetSpawnCommand::class,
                AddAreaPresetCommand::class,
                //////////////////////// AreaCommand
                //WorldCommand::class,
                WorldAllowAutoCreateAreaCommand::class,
                WorldAllowManualCreateAreaCommand::class,
                WorldAreaPriceCommand::class,
                WorldBreakCommand::class,
                WorldCreateCommand::class,
                WorldDoorCommand::class,
                WorldInformationCommand::class,
                WorldInventorySaveCommand::class,
                WorldMaxAreaCountCommand::class,
                WorldMoveCommand::class,
                WorldPlaceCommand::class,
                WorldPvpCommand::class,
                WorldPresetCommand::class,
                /////////////////////////// WorldCommand
                XYZCommand::class
            ] as $c) {
            $this->getServer()->getCommandMap()->register("Area", new $c());
        }
        $this->listener = new EventListener();
        $this->queue = new Queue();
    }

    public function onDisable()
    {
        $this->areaManager->save();
        $this->worldManager->save();
    }

    /**
     * @return AreaPlugin
     */
    public static function getInstance(): self
    {
        return self::$i;
    }

    /**
     * @return EventListener
     */
    public function getEventListener(): EventListener
    {
        return $this->listener;
    }

    /**
     * @return AreaManager
     */
    public function getAreaManager(): AreaManager
    {
        return $this->areaManager;
    }

    /**
     * @return WorldManager
     */
    public function getWorldManager(): WorldManager
    {
        return $this->worldManager;
    }

    /**
     * @return Queue
     */
    public function getQueue(): Queue
    {
        return $this->queue;
    }

}