<?php

namespace ojy\area\generator;

use ojy\area\AreaPlugin;
use ojy\area\preset\PresetManager;
use ojy\area\util\BlockDeserializer;
use ojy\area\util\Sphere;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class IslandGenerator extends Generator
{

    /**
     * IslandGenerator constructor.
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
        return "Island";
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

    public static function onGenerate(ChunkManager $world, int $chunkX, int $chunkZ, ?string $preset = null)
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null)
            $chunk = new Chunk($chunkX, $chunkZ);
        for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                foreach ([7, 1, 1, 1, 1, 1, 1, 12, 8, 8] as $y => $blockId) {

                    $chunk->setBlock($x, $y, $z, $blockId);

                }
            }
        }
        //if ($preset === null) {
        if ($chunkX > 0 and $chunkZ > 0) {
            $islandX = ($chunkX * 16) % 200;
            $islandZ = ($chunkZ * 16) % 200;
            if ($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15) {
                foreach (Sphere::getElements(8, 7, 8, 7) as $el) {
                    $x = $el[0];
                    $y = $el[1];
                    $z = $el[2];
                    if ($y < 7) {
                        $chunk->setBlock($x, $y, $z, 12);
                    } else if ($y < 10) {
                        $chunk->setBlock($x, $y, $z, 1);
                    } else if ($y < 12) {
                        $chunk->setBlock($x, $y, $z, 2);
                    }
                }

            }

        }
        /*} else {
            $presetBuffer = PresetManager::getPresetBuffer($preset);
            var_dump($preset);
            var_dump($presetBuffer);
            $deserializer = new BlockDeserializer($chunk, $presetBuffer);
            $deserializer->deserialize(8, 7, 8);
        }*/
        $world->setChunk($chunkX, $chunkZ, $chunk);
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ): void
    {
        self::onGenerate($this->level, $chunkX, $chunkZ);
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3
    {
        return new Vector3(125, 65, 125);
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ): void
    {
        if ($chunkX > 0 and $chunkZ > 0) {
            $islandX = ($chunkX * 16) % 200;
            $islandZ = ($chunkZ * 16) % 200;
            if ($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15) {
                $chunk = $this->level->getChunk($chunkX, $chunkZ);
                $x = $chunkX * 16 + 8;
                $z = $chunkZ * 16 + 8;
                $y = $chunk->getHighestBlockAt(8, 8);
                Tree::growTree($this->level, $x, $y + 1, $z, $this->random);
            }
        }
        $biome = Biome::getBiome(Biome::OCEAN);
        $biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
    }
}