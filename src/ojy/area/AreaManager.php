<?php

namespace ojy\area;

use ojy\area\command\AreaCustomCommand;
use ojy\area\command\custom\CustomListCommand;
use ojy\area\command\custom\CustomMoveCommand;
use ojy\area\command\custom\CustomPurchaseCommand;
use ojy\area\event\AddAreaEvent;
use ojy\area\exception\InvalidPositionDataException;
use ojy\area\generator\FlainGenerator;
use ojy\area\generator\IslandGenerator;
use ojy\area\generator\SkyLandGenerator;
use pocketmine\block\Block;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class AreaManager
{
    /** @var array */
    public $playerPosition = [];
    /** @var array */
    protected $pd = [];
    /** @var array */
    protected $areas = [];
    /** @var string */
    protected $dataFolder;

    /**
     * AreaManager constructor.
     */
    public function __construct()
    {
        $this->dataFolder = AreaPlugin::getInstance()->getDataFolder();
        Server::getInstance()->getLogger()->info('§e======= [ AreaManager ] =======');
        foreach (Server::getInstance()->getLevels() as $world) {
            if (self::load($world->getFolderName())) {
                Server::getInstance()->getLogger()->info("§e{$world->getFolderName()}의 땅을 로드했습니다.");
            }
        }
        Server::getInstance()->getLogger()->info('§e===============================');
    }

    /**
     * @param Player $player
     * @param Area $area
     */
    public function setPlayerPosition(Player $player, Area $area)
    {
        $this->playerPosition[$player->getName()] = $area;
    }

    /**
     * @param Player $player
     */
    public function unsetPlayerPosition(Player $player)
    {
        if (isset($this->playerPosition[$player->getName()]))
            unset($this->playerPosition[$player->getName()]);
    }

    /**
     * @param string $worldName
     * @return array
     */
    public function getAllArea(string $worldName): array
    {
        return isset($this->areas[$worldName]) ? $this->areas[$worldName] : [];
    }

    /**
     * @param string $worldName
     * @param string $playerName
     * @return array
     */
    public function getPlayerAreaIds(string $worldName, string $playerName): array
    {
        $res = [];
        if (isset($this->areas[$worldName])) {
            foreach ($this->areas[$worldName] as $id => $area) {
                if ($area instanceof Area) {
                    if ($area->getProperties()->getOwner() === strtolower($playerName)) {
                        $res[] = $id;
                    }
                }
            }
            unset($id, $area);
            return $res;
        }
        return $res;
    }

    /**
     * @param string $worldName
     * @param string $playerName
     * @return Area[]
     */
    public function getPlayerAreas(string $worldName, string $playerName): array
    {
        $res = [];
        if (isset($this->areas[$worldName])) {
            foreach ($this->areas[$worldName] as $id => $area) {
                if ($area instanceof Area) {
                    if ($area->getProperties()->getOwner() === strtolower($playerName)) {
                        $res[] = $area;
                    }
                }
            }
            unset($id, $area);
            return $res;
        }
        return $res;
    }

    /**
     * @param string $worldName
     * @param string $playerName
     * @return Area[]
     */
    public function getInResidenceArea(string $worldName, string $playerName): array
    {
        $res = [];
        if (isset($this->areas[$worldName])) {
            foreach ($this->areas[$worldName] as $id => $area) {
                if ($area instanceof Area) {
                    if ($area->getProperties()->isResident($playerName)) {
                        $res[] = $area;
                    }
                }
            }
            unset($id, $area);
            return $res;
        }
        return $res;
    }

    /**
     * @param string $worldName
     * @param int $x
     * @param int $z
     * @return Area|null
     */
    public function getAreaByXZ(string $worldName, int $x, int $z): ?Area
    {
        if (!isset($this->areas[$worldName])) return null;
        if (count($this->areas[$worldName]) < 1) return null;
        //if (isset($this->pd["{$worldName}:{$x}:{$z}"]))
        //return $this->pd["{$worldName}:{$x}:{$z}"];
        foreach ($this->areas[$worldName] as $index => $area) {
            if ($area !== null) {
                $pd = $area->getPositionData();
                if ($pd['startX'] > $x) continue;
                if ($pd['endX'] < $x) continue;
                if ($pd['startZ'] > $z) continue;
                if ($pd['endZ'] < $z) continue;
                if ($this->getAreaById($worldName, $area->getId()) === null)
                    return null;
                return $area;
                /*if ($pd['startX'] <= $x && $pd['endX'] >= $x && $pd['startZ'] <= $z && $pd['endZ'] >= $z) {
                    //$this->pd["{$worldName}:{$x}:{$z}"] = $area;
                    return $area;
                }*/
            }
        }

        return null;
    }

    /**
     * @param Player $player
     * @return Area|null
     */
    public function getAreaByPlayer(Player $player): ?Area
    {
        $area = isset($this->playerPosition[$player->getName()]) ? $this->playerPosition[$player->getName()] : $this->getAreaByXZ($player->getLevel()->getFolderName(), ceil($player->getPosition()->x), ceil($player->getPosition()->z));
        if ($area instanceof Area) {
            return $this->getAreaById($player->level->getFolderName(), $area->getId());
        }
        return null;
    }

    /**
     * @param string $worldName
     * @param int $id
     * @return Area|null
     */
    public function getAreaById(string $worldName, int $id): ?Area
    {
        return isset($this->areas[$worldName]) && isset($this->areas[$worldName][$id]) ? $this->areas[$worldName][$id] : null;
    }

    /**
     * @param int $startX
     * @param int $startZ
     * @param int $endX
     * @param int $endZ
     * @return array
     */
    public function positionData(int $startX, int $startZ, int $endX, int $endZ)
    {
        return ['startX' => $startX, 'startZ' => $startZ, 'endX' => $endX, 'endZ' => $endZ];
    }

    /**
     * @param string $worldName
     * @param int $index
     * @return int
     */
    public function getNextId(string $worldName, int $index = -1): int
    {
        if ($index !== -1) {
            $c = count($this->areas[$worldName]);
            if (!isset($this->areas[$worldName][$c]))
                return $c;
            else
                return $this->getNextId($worldName, $c);
        } else {
            if (!isset($this->areas[$worldName][$index + 1]))
                return $index + 1;
            else
                return $this->getNextId($worldName, $index + 1);

        }
    }

    /**
     * @param int $a
     * @param int $b
     * @param int $c
     * @return bool
     */
    public function in(int $a, int $b, int $c): bool
    {
        return $a <= $c && $b >= $c;
    }

    /**
     * @param $world
     * @param array $positionData
     * @return Area|null
     */
    public function checkOverlap($world, array $positionData/*, int $startX, int $startZ, int $endX, int $endZ*/): ?Area
    {
        if ($world instanceof Level)
            $world = $world->getFolderName();
        if (!isset($this->areas[$world]))
            return null;
        $startX = $positionData['startX'];
        $startZ = $positionData['startZ'];
        $endX = $positionData['endX'];
        $endZ = $positionData['endZ'];
        foreach ($this->areas[$world] as $id => $area) {
            if ($area instanceof Area) {
                $position = $area->getPositionData();
                if ($this->in($position['startX'], $position['endX'], $startX)) {
                    if ($this->in($position['startZ'], $position['endZ'], $startZ) || $this->in($position['startZ'], $position['endZ'], $endZ)) {
                        return $area;
                    }
                } elseif ($this->in($position['startX'], $position['endX'], $endX)) {
                    if ($this->in($position['startZ'], $position['endZ'], $startZ) || $this->in($position['startZ'], $position['endZ'], $endZ)) {
                        return $area;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $worldName
     * @return Area[]
     */
    public function canAvailablePurchaseAreas(string $worldName): array
    {
        $res = [];
        if (isset($this->areas[$worldName])) {
            foreach ($this->areas[$worldName] as $id => $area) {
                if ($area instanceof Area && $area->getProperties()->getOwner() === '') {
                    $res[] = $area;
                }
            }
        }
        return $res;
    }

    /**
     * @param Block $block
     * @param Block $block2
     * @return bool|int|Area|null
     * @throws InvalidPositionDataException
     */
    public function addAreaByBlock(Block $block, Block $block2)
    {
        $bx = $block->getX();
        $bbx = $block2->getX();
        $bz = $block->getZ();
        $bbz = $block->getZ();
        $pd = $this->positionData(min($bx, $bbx), min($bz, $bbz), max($bx, $bbx), max($bz, $bbz));
        return $this->addArea($block->getLevel()->getFolderName(), $pd);
    }

    /**
     * @param string $worldName
     * @param array $positionData
     * @return bool|int|Area|null
     * @throws InvalidPositionDataException
     */
    public function addArea(string $worldName, array $positionData)
    {
        if (($area = $this->checkOverlap($worldName, $positionData)) instanceof Area) {
            return $area->getId();
        }
        //try {
        if (isset($this->areas[$worldName])) {
            $this->areas[$worldName][] = $area = new Area($this->getNextId($worldName), $worldName, $positionData);
            (new AddAreaEvent($area))->call();
            return $area;
        } else {
            if ($this->load($worldName)) {
                $this->areas[$worldName][] = $area = new Area($this->getNextId($worldName), $worldName, $positionData);
                (new AddAreaEvent($area))->call();
                return $area;
            }
        }
        /*} catch (InvalidPositionDataException $e) {
            Server::getInstance()->getLogger()->critical($e->getMessage());
        }*/
        return false;
    }

    /**
     * @param string $worldName
     * @param int $areaId
     * @return bool
     */
    public function removeArea(string $worldName, int $areaId): bool
    {
        if (isset($this->areas[$worldName][$areaId])) {
            $this->areas[$worldName][$areaId] = null;
            return true;
        }
        return false;
    }

    /**
     * @param string $worldName
     * @return bool
     * @throws InvalidPositionDataException
     */
    public function load(string $worldName): bool
    {
        if (Server::getInstance()->isLevelLoaded($worldName) && !isset($this->areas[$worldName])) {
            $config = new Config(AreaPlugin::getInstance()->getDataFolder() . "{$worldName}/areas.yml", Config::YAML, []);
            $data = $config->getAll();
            $this->areas[$worldName] = [];
            foreach ($data as $id => $v) {
                //try {
                if ($v !== null) {
                    $this->areas[$worldName][$id] = new Area($v['id'], $v['worldName'], $v['positionData'], AreaProperties::deserialize($v['properties']));
                } else {
                    $this->areas[$worldName][$id] = null;
                }
                //} catch (InvalidPositionDataException $e) {
                //  Server::getInstance()->getLogger()->critical($e->getMessage());
                //}
            }
            $generator = GeneratorManager::getGenerator(Server::getInstance()->getLevelByName($worldName)->getProvider()->getGenerator());
            if ($generator === IslandGenerator::class || $generator === SkyLandGenerator::class || $generator === FlainGenerator::class) {
                Server::getInstance()->getCommandMap()->register('Area', new CustomPurchaseCommand($worldName));
                Server::getInstance()->getCommandMap()->register('Area', new CustomListCommand($worldName));
                Server::getInstance()->getCommandMap()->register('Area', new CustomMoveCommand($worldName));
            }
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function save()
    {
        foreach (array_keys($this->areas) as $worldName) {
            $config = new Config($this->dataFolder . "{$worldName}/areas.yml", Config::YAML, []);
            $data = $config->getAll();
            foreach ($this->areas[$worldName] as $id => $area) {
                if ($area instanceof Area) {
                    $data[$id] = $area->serialize();
                } else {
                    $data[$id] = null;
                }
            }
            $config->setAll($data);
            $config->save();
        }
    }
}