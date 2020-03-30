<?php

namespace ojy\area;

use ojy\area\event\MoveAreaEvent;
use ojy\area\exception\InvalidPositionDataException;
use ojy\warp\WarpLoader;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class Area implements PropertyTypes
{

    /** @var AreaProperties */
    protected $properties;

    /** @var array */
    protected $positionData = ['startX' => null, 'startZ' => null, 'endX' => null, 'endZ' => null];

    /** @var string */
    protected $worldName;

    /** @var int */
    protected $id;

    /**
     * Area constructor.
     * @param int $id
     * @param string $worldName
     * @param array $positionData
     * @param AreaProperties|null $properties
     * @throws InvalidPositionDataException
     */
    public function __construct(int $id, string $worldName, array $positionData, AreaProperties $properties = null)
    {
        $this->id = $id;
        $this->worldName = $worldName;
        foreach ($positionData as $k => $v) {
            if (in_array($k, array_keys($this->positionData))) {
                $this->positionData[$k] = $v;
            } else {
                throw new InvalidPositionDataException("find invalid position data, key: {$k}");
            }
        }
        $this->properties = $properties ?? new AreaProperties();
        if ($this->properties->getSpawnString() === "")
            $this->properties->setSpawn($this->getSpawn());
    }

    /**
     * @param Player $player
     */
    public function moveToArea(Player $player): void
    {
        $res = false;
        if ($this->getProperties()->get(PropertyTypes::TYPE_CAN_ACCESS) || $this->getProperties()->isResident($player->getName()) || $player->isOp()) {
            $res = true;
            $player->teleport($this->getSpawn());
        }
        (new MoveAreaEvent($player, $this, $res))->call();

        AreaPlugin::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function (int $currentTick) use ($player): void {
            if ($player instanceof Player && $player->isOnline() && $player->level instanceof Level)
                $player->level->addSound(new EndermanTeleportSound($player->getPosition()));
        }));
    }

    /**
     * @return Position
     */
    public function getSpawn(): Position
    {
        if ($this->properties->getSpawnString() === '') {
            $world = Server::getInstance()->getLevelByName($this->worldName);
            $x = floor(($this->positionData['startX'] + $this->positionData['endX']) / 2);
            $z = floor(($this->positionData['startZ'] + $this->positionData['endZ']) / 2);
            $spawn = new Position($x, $world->getHighestBlockAt($x, $z) + 1, $z, $world);
        } else {
            $spawn = $this->properties->getSpawn();
            if($spawn->y < 2) {
                $world = Server::getInstance()->getLevelByName($this->worldName);
                $spawn->y = $world->getHighestBlockAt($spawn->x, $spawn->z) + 1;
                $this->properties->setSpawn($spawn);
            }
        }
        if ($spawn->level->getHighestBlockAt($spawn->x, $spawn->z) < 2) {
            return $this->getSafeSpawn($spawn);
        }
        return $spawn;
    }

    public function getSafeSpawn(Position $position): Position
    {

        for ($x = -5; $x <= 5; ++$x) {
            for ($z = -5; $z <= 5; ++$z) {
                $h = $position->level->getHighestBlockAt($position->x + $x, $position->z + $z);
                if ($h > 1)
                    return $position->setComponents($position->x + $x, $h + 1, $position->z + $z);
            }
        }
        return $position;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canPvp(Player $player)
    {
        return $this->properties->get(self::TYPE_PVP);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canAccess(Player $player): bool
    {
        return $this->properties->getOwner() === '' || $this->properties->get(self::TYPE_CAN_ACCESS) || $this->properties->isResident($player->getName()) || $player->isOp();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canPlace(Player $player)
    {
        return $this->properties->get(self::TYPE_CAN_PLACE) || $this->properties->isResident($player->getName()) || $player->isOp();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canOpenDoor(Player $player)
    {
        return $this->properties->get(self::TYPE_OPEN_DOOR) || $this->properties->isResident($player->getName()) || $player->isOp();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canBreak(Player $player)
    {
        return $this->properties->get(self::TYPE_CAN_BREAK) || $this->properties->isResident($player->getName()) || $player->isOp();
    }

    /**
     * @param Area $area1
     * @param Area $area2
     * @return bool
     */
    public static function equals(Area $area1, Area $area2): bool
    {
        return ($area1->getWorldName() === $area2->getWorldName()) && ($area1->getId() === $area2->getId());
    }

    /**
     * @return array
     */
    public function getPositionData(): array
    {
        return $this->positionData;
    }

    /**
     * @return string
     */
    public function getWorldName(): string
    {
        return $this->worldName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return AreaProperties
     */
    public function getProperties(): AreaProperties
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        return ['id' => $this->id, 'worldName' => $this->worldName, 'positionData' => $this->positionData,
            'properties' => $this->properties->serialize()];
    }
}