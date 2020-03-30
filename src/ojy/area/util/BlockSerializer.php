<?php

namespace ojy\area\util;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\BinaryStream;

class BlockSerializer extends BinaryStream
{
    /** @var Level */
    private $level;

    public function __construct(Level $level)
    {
        parent::__construct();
        $this->level = $level;
    }

    public function serialize(Vector3 $pos1, Vector3 $pos2)
    {
        $x0 = min($pos1->getFloorX(), $pos2->getFloorX());
        $x1 = max($pos1->getFloorX(), $pos2->getFloorX());
        $y0 = min($pos1->getFloorY(), $pos2->getFloorY());
        $y1 = max($pos1->getFloorY(), $pos2->getFloorY());
        $z0 = min($pos1->getFloorZ(), $pos2->getFloorZ());
        $z1 = max($pos1->getFloorZ(), $pos2->getFloorZ());

        $this->putUnsignedVarInt($x1 - $x0);
        $this->putUnsignedVarInt($y1 - $y0);
        $this->putUnsignedVarInt($z1 - $z0);

        for ($y = $y0; $y <= $y1; $y++) {
            for ($z = $z0; $z <= $z1; $z++) {
                for ($x = $x0; $x <= $x1; $x++) {
                    $this->putByte($this->level->getBlockIdAt($x, $y, $z));
                    $this->putByte($this->level->getBlockDataAt($x, $y, $z));
                }
            }
        }
    }
}