<?php

namespace ojy\area\preset;

use ojy\area\AreaPlugin;
use pocketmine\Server;
use pocketmine\utils\Config;

class PresetManager
{

    /** @var Config */
    private static $data;

    /** @var array */
    private static $db = [];

    public function __construct()
    {
        self::$data = new Config(AreaPlugin::getInstance()->getDataFolder() . "PresetData.yml", Config::YAML, []);
        self::$db = self::$data->getAll();
    }

    public static function isExistPreset(string $presetName): bool
    {
        return isset(self::$db[$presetName]) ? true : false;
    }

    public static function getPresetBuffer(string $presetName): ?string
    {
        return self::$db[$presetName] ?? null;
    }

    public static function setPreset(string $presetName, string $buffer): bool
    {
        if (!isset(self::$db[$presetName])) {
            self::$db[$presetName] = $buffer;
            return true;
        }
        return false;
    }

    public static function save()
    {
        self::$data->setAll(self::$db);
        self::$data->save();
    }
}