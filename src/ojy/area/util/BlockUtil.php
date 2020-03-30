<?php

namespace ojy\area\util;

use pocketmine\block\Block;

class BlockUtil
{

    public static function getFullId(int $id, int $meta = 0)
    {
        return ($id << 4) | $meta;
    }
}