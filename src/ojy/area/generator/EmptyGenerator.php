<?php

namespace ojy\area\generator;

use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class EmptyGenerator extends Generator
{

    /**
     * EmptyGenerator constructor.
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
        return "Empty";
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3
    {
        return new Vector3(125, 65, 125);
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
        //
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ): void
    {
        //
    }
}