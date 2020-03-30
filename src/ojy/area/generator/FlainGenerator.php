<?php

namespace ojy\area\generator;

use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class FlainGenerator extends Generator
{

    /** @var array */
    const BASE_LAYER = [
        [7, 0],
        [7, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [1, 0],
        [3, 0],
        [3, 0],
        [3, 0],
        [3, 0]
    ];
    /** @var array */
    const ROAD_BLOCK = [5, 0];
    /** @var array */
    const LAND_EDGE_BLOCK = [43, 0];
    /** @var array */
    const LAND_BLOCK = [2, 0];
    /** @var int */
    const ROAD_FLAG = 1;
    /** @var int */
    const LAND_EDGE_FLAG = 2;
    /** @var int */
    const LAND_FLAG = 3;

    /**
     * FlainGenerator constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'flain';
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * @param ChunkManager $level
     * @param Random $random
     */
    public function init(ChunkManager $level, Random $random): void
    {
        $this->level = $level;
        $this->random = $random;
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ): void
    {
        $xOrder = array_pad([
            self::ROAD_FLAG,
            self::ROAD_FLAG,
            self::ROAD_FLAG,
            self::LAND_EDGE_FLAG
        ], 16, self::LAND_FLAG);
        $zOrder = array_pad([
            self::ROAD_FLAG,
            self::ROAD_FLAG,
            self::ROAD_FLAG,
            self::LAND_EDGE_FLAG
        ], 16, self::LAND_FLAG);

        if ($chunkX % 2 != 0) {
            $xOrder = array_reverse($xOrder);
        }
        if ($chunkZ % 2 != 0) {
            $zOrder = array_reverse($zOrder);
        }

        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        // Create Chunk
        for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                // Create base layer
                $y = 0;
                foreach (self::BASE_LAYER as $block) {
                    $chunk->setBlock($x, $y, $z, ...$block);
                    $y++;
                }

                if ($xOrder[$x] == self::ROAD_FLAG || $zOrder[$z] == self::ROAD_FLAG) {
                    $chunk->setBlock($x, $y, $z, self::ROAD_BLOCK[0], self::ROAD_BLOCK[1]);
                } else if ($xOrder[$x] == self::LAND_EDGE_FLAG || $zOrder[$z] == self::LAND_EDGE_FLAG) {
                    $chunk->setBlock($x, $y, $z, self::LAND_EDGE_BLOCK[0], self::LAND_EDGE_BLOCK[1]);
                } else {
                    $chunk->setBlock($x, $y, $z, self::LAND_BLOCK[0], self::LAND_BLOCK[1]);
                }
            }
        }
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ): void
    {
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3
    {
        return new Vector3(127.5, count(self::BASE_LAYER) + 1, 127.5);
    }
}