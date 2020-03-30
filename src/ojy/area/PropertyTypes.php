<?php

namespace ojy\area;

interface PropertyTypes
{

    /** @var string */
    public const TYPE_PVP = "pvp";

    /** @var string */
    public const TYPE_CAN_BREAK = "break";

    /** @var string */
    public const TYPE_CAN_PLACE = "place";

    /** @var string */
    public const TYPE_OPEN_DOOR = "open_door";

    /** @var string */
    public const TYPE_INVENTORY_SAVE = "inventory_save";

    /** @var string */
    public const TYPE_CAN_ACCESS = "can_access";

    /** @var string */
    public const WORLD_AUTO_CREATE = "auto_create";

    /** @var string */
    public const WORLD_MANUAL_CREATE = "manual_create";
}