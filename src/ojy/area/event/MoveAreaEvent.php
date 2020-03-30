<?php

namespace ojy\area\event;

use ojy\area\Area;
use pocketmine\event\Event;
use pocketmine\Player;

class MoveAreaEvent extends Event
{

    public static $handlerList = null;

    /** @var Player */
    protected $player;

    /** @var Area */
    protected $area;

    /** @var bool */
    protected $result;

    /**
     * MoveAreaEvent constructor.
     * @param Player $player
     * @param Area $area
     * @param bool $result
     */
    public function __construct(Player $player, Area $area, bool $result)
    {
        $this->player = $player;
        $this->area = $area;
        $this->result = $result;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}