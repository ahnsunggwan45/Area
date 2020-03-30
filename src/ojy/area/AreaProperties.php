<?php

namespace ojy\area;

use pocketmine\level\Position;
use ssss\utils\SSSSUtils;

class AreaProperties implements PropertyTypes
{

    /** @var boolean */
    protected $pvp = false;
    /** @var boolean */
    protected $open_door = true;
    /** @var boolean */
    protected $break = false;
    /** @var boolean */
    protected $place = false;
    /** @var boolean */
    protected $can_access = true;
    /** @var string */
    protected $owner = "";
    /** @var array */
    protected $residents = [];

    /** @var string|null */
    protected $spawn = null;

    /**
     * AreaProperties constructor.
     * @param bool $pvp
     * @param bool $open_door
     * @param bool $break
     * @param bool $place
     * @param bool $can_access
     * @param string $owner
     * @param array $residents
     * @param string $spawn
     */
    public function __construct(bool $pvp = false, bool $open_door = true, bool $break = false, bool $place = false,
                                bool $can_access = true, string $owner = "", array $residents = [], string $spawn = "")
    {
        $this->pvp = $pvp;
        $this->open_door = $open_door;
        $this->break = $break;
        $this->place = $place;
        $this->can_access = $can_access;
        $this->owner = $owner;
        $this->residents = $residents;
        $this->spawn = $spawn;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        return [$this->pvp, $this->open_door, $this->break, $this->place, $this->can_access, $this->owner, $this->residents, $this->spawn];
    }

    public function getSpawnString(): string
    {
        return $this->spawn;
    }


    public function getSpawn(): Position
    {
        return SSSSUtils::strToPosition($this->spawn);
    }

    /**
     * @param array $data
     * @return AreaProperties
     */
    public static function deserialize(array $data): self
    {
        return new self(...$data);
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @param string $ownerName
     */
    public function setOwner(string $ownerName): void
    {
        $this->owner = strtolower($ownerName);
    }

    /**
     * @return array
     */
    public function getResidents(): array
    {
        return $this->residents;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isResident(string $name): bool
    {
        $name = strtolower($name);
        return $this->owner === $name || in_array($name, $this->residents);
    }

    /**
     * @param string $residentName
     * @return bool
     */
    public function setResident(string $residentName): bool
    {
        $residentName = strtolower($residentName);
        if (!in_array($residentName, $this->residents)) {
            $this->residents[] = $residentName;
            return true;
        }
        return false;
    }

    /**
     * @param string $residentName
     * @return bool
     */
    public function unsetResident(string $residentName): bool
    {
        $residentName = strtolower($residentName);
        if (in_array($residentName, $this->residents)) {
            unset($this->residents[array_search($residentName, $this->residents)]);
            return true;
        }
        return false;
    }

    /**
     * @param string $type
     * @return mixed|null
     */
    public function get(string $type)
    {
        return isset($this->{$type}) ? $this->{$type} : null;
    }


    /**
     * @param bool $pvp
     */
    public function setPvp(bool $pvp): void
    {
        $this->pvp = $pvp;
    }

    /**
     * @param bool $open_door
     */
    public function setOpenDoor(bool $open_door): void
    {
        $this->open_door = $open_door;
    }

    /**
     * @param bool $break
     */
    public function setBreak(bool $break): void
    {
        $this->break = $break;
    }

    /**
     * @param bool $can_access
     */
    public function setCanAccess(bool $can_access): void
    {
        $this->can_access = $can_access;
    }

    /**
     * @param bool $place
     */
    public function setPlace(bool $place): void
    {
        $this->place = $place;
    }

    /**
     * @param Position $spawn
     */
    public function setSpawn(Position $spawn): void
    {
        $this->spawn = SSSSUtils::posToString($spawn);
    }
}