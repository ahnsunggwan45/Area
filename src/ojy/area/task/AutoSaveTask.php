<?php

namespace ojy\area\task;

use ojy\area\AreaManager;
use ojy\area\AreaPlugin;
use ojy\area\world\WorldManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AutoSaveTask extends Task
{

    public function __construct()
    {
    }

    public function onRun(int $currentTick)
    {
        AreaPlugin::getInstance()->getAreaManager()->save();
        AreaPlugin::getInstance()->getWorldManager()->save();
        AreaPlugin::getInstance()->getLogger()->info('save: Area data, World data');
    }
}