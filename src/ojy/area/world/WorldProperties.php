<?php

namespace ojy\area\world;

class WorldProperties
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
    protected $auto_create = false;
    /** @var boolean */
    protected $manual_create = false;
    /** @var int */
    protected $max_area_count;
    /** @var int */
    protected $area_price;
    /** @var bool */
    protected $inventory_save = true;

    /** @var string|null */
    protected $preset = null;

    /**
     * WorldProperties constructor.
     * @param bool $pvp
     * @param bool $open_door
     * @param bool $break
     * @param bool $place
     * @param bool $auto_create
     * @param bool $manual_create
     * @param int $max_area_count
     * @param int $area_price
     * @param bool $inventory_save
     * @param string $preset
     */
    public function __construct(bool $pvp = false, bool $open_door = true, bool $break = false, bool $place = false,
                                bool $auto_create = false, bool $manual_create = false, int $max_area_count = 5,
                                int $area_price = 5000, bool $inventory_save = true, ?string $preset = null)
    {
        $this->pvp = $pvp;
        $this->open_door = $open_door;
        $this->break = $break;
        $this->place = $place;
        $this->auto_create = $auto_create;
        $this->manual_create = $manual_create;
        $this->max_area_count = $max_area_count;
        $this->area_price = $area_price;
        $this->inventory_save = $inventory_save;
        $this->preset = $preset;
    }

    /**
     * @param bool $pvp
     */
    public function setPvp(bool $pvp)
    {
        $this->pvp = $pvp;
    }

    /**
     * @param bool $open_door
     */
    public function setCanOpenDoor(bool $open_door)
    {
        $this->open_door = $open_door;
    }

    /**
     * @param bool $break
     */
    public function setCanBreak(bool $break)
    {
        $this->break = $break;
    }

    /**
     * @param bool $place
     */
    public function setCanPlace(bool $place)
    {
        $this->place = $place;
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
     * @return array
     */
    public function serialize(): array
    {
        return [$this->pvp, $this->open_door, $this->break, $this->place, $this->auto_create,
            $this->manual_create, $this->max_area_count, $this->area_price,
            $this->inventory_save, $this->preset];
    }

    /**
     * @param array $data
     * @return WorldProperties
     */
    public static function deserialize(array $data): self
    {
        return new self(...$data);
    }

    /**
     * @return bool
     */
    public function isAutoCreate(): bool
    {
        return $this->auto_create;
    }

    /**
     * @param bool $auto_create
     */
    public function setAutoCreate(bool $auto_create): void
    {
        $this->auto_create = $auto_create;
    }

    /**
     * @return bool
     */
    public function isManualCreate(): bool
    {
        return $this->manual_create;
    }

    /**
     * @param bool $manual_create
     */
    public function setManualCreate(bool $manual_create): void
    {
        $this->manual_create = $manual_create;
    }

    /**
     * @return int
     */
    public function getMaxAreaCount(): int
    {
        return $this->max_area_count;
    }

    /**
     * @param int $max_area_count
     */
    public function setMaxAreaCount(int $max_area_count): void
    {
        $this->max_area_count = $max_area_count;
    }

    /**
     * @return int
     */
    public function getAreaPrice(): int
    {
        return $this->area_price;
    }

    /**
     * @param int $area_price
     */
    public function setAreaPrice(int $area_price): void
    {
        $this->area_price = $area_price;
    }

    /**
     * @return bool
     */
    public function isInventorySave(): bool
    {
        return $this->inventory_save;
    }

    /**
     * @param bool $inventory_save
     */
    public function setInventorySave(bool $inventory_save): void
    {
        $this->inventory_save = $inventory_save;
    }

    /**
     * @return string|null
     */
    public function getPreset(): ?string
    {
        return $this->preset;
    }

    /**
     * @param string|null $preset
     */
    public function setPreset(?string $preset): void
    {
        $this->preset = $preset;
    }
}