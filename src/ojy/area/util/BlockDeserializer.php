<?php

namespace ojy\area\util;

use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\BinaryStream;

class BlockDeserializer extends BinaryStream
{
    /** @var Chunk */
    private $chunk;

    public function __construct(Chunk $chunk, string $buffer)
    {
        parent::__construct($buffer);
        $this->chunk = $chunk;
    }

    public function deserialize(int $centerX, int $startY, int $centerZ)
    {
        $xlen = $this->getUnsignedVarInt();
        $ylen = $this->getUnsignedVarInt();
        $zlen = $this->getUnsignedVarInt();

        $centerY = $startY + floor($ylen / 2);

        $x0 = $centerX - ceil($xlen / 2);
        $x1 = $centerX + floor($xlen / 2);
        $y0 = $centerY - ceil($ylen / 2);
        $y1 = $centerY + floor($ylen / 2);
        $z0 = $centerZ - ceil($zlen / 2);
        $z1 = $centerZ + floor($zlen / 2);

        for ($y = $y0; $y <= $y1; $y++) {
            for ($z = $z0; $z <= $z1; $z++) {
                for ($x = $x0; $x <= $x1; $x++) {
                    $this->chunk->setBlockId($x, $y, $z, $this->getByte());
                    $this->chunk->setBlockData($x, $y, $z, $this->getByte());
                }
            }
        }

        $this->offset = 0; // reusable
    }
}