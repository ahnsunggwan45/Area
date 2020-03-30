<?php

namespace ojy\area\task;

use ojy\area\AreaPlugin;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class CheckPlayerTask extends Task
{

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        //foreach (Server::getInstance()->getOnlinePlayers() as $p)
        //   AreaPlugin::getInstance()->getEventListener()->checkPlayer($p);
    }
}