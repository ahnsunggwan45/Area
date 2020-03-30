<?php

namespace ojy\area\event;

use ojy\area\Area;
use pocketmine\event\Event;
use pocketmine\Player;

class EnterAreaEvent extends Event
{

    public static $handlerList = null;

    /** @var Player */
    protected $player;

    /** @var Area */
    protected $area;

    /**
     * EnterAreaEvent constructor.
     * @param Player $player
     * @param Area $area
     */
    public function __construct(Player $player, Area $area)
    {
        $this->player = $player;
        $this->area = $area;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }
}