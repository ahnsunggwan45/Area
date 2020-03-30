<?php

namespace ojy\area\event;

use ojy\area\Area;
use pocketmine\event\Event;

class AddAreaEvent extends Event
{

    public static $handlerList = null;

    /** @var Area */
    protected $area;

    /**
     * AddAreaEvent constructor.
     * @param Area $area
     */
    public function __construct(Area $area)
    {
        $this->area = $area;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }
}