<?php

namespace ojy\area\world;

use ojy\area\AreaPlugin;
use pocketmine\Server;
use pocketmine\utils\Config;

class WorldManager
{

    /** @var array */
    private $worlds = [];

    /** @var string */
    private $dataFolder;

    /**
     * WorldManager constructor.
     */
    public function __construct()
    {
        $this->dataFolder = AreaPlugin::getInstance()->getDataFolder();
        foreach (Server::getInstance()->getLevels() as $world) {
            $this->load($world->getFolderName());
        }
    }

    /**
     * @param string $worldName
     * @return WorldProperties|null
     */
    public function getWorldProperties(string $worldName): ?WorldProperties
    {
        return isset($this->worlds[$worldName]) ? $this->worlds[$worldName] : null;
    }

    /**
     * @param string $worldName
     * @return bool
     */
    public function load(string $worldName): bool
    {
        if (Server::getInstance()->isLevelLoaded($worldName) && !isset($this->worlds[$worldName])) {
            $config = new Config(AreaPlugin::getInstance()->getDataFolder() . "{$worldName}/world_data.yml", Config::YAML, []);
            $this->worlds[$worldName] = WorldProperties::deserialize($config->getAll());
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function save()
    {
        foreach ($this->worlds as $worldName => $p) {
            if ($p instanceof WorldProperties) {
                $config = new Config($this->dataFolder . "{$worldName}/world_data.yml", Config::YAML, []);
                $config->setAll($p->serialize());
                $config->save();
            }
        }
    }
}