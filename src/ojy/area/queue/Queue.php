<?php

namespace ojy\area\queue;

use pocketmine\Player;

class Queue
{

    /** @var array */
    private $queue = [];

    public function setQueue(Player $player, $data = null)
    {
        $this->queue[$player->getName()] = ['time' => time(), 'position' => $player->asPosition(), 'data' => $data];
    }

    public function unsetQueue(Player $player): bool
    {
        if (isset($this->queue[$player->getName()])) {
            unset($this->queue[$player->getName()]);
            return true;
        }
        return false;
    }

    public function isQueue(Player $player, $data = null)
    {
        return (((isset($this->queue[$player->getName()])
                    && (time() - $this->queue[$player->getName()]['time'] < 15))
                && ($player->distance($this->queue[$player->getName()]['position']) < 15))
            && $data === $this->queue[$player->getName()]['data']);
    }
}