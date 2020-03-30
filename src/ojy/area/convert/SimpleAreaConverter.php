<?php

namespace ojy\area\convert;

use ifteam\SimpleArea\database\area\AreaSection;
use ojy\area\Area;
use ojy\area\AreaPlugin;
use function ojy\area\md;
use pocketmine\Server;
use pocketmine\utils\Config;

class SimpleAreaConverter
{

    /**
     * @param string $worldName
     * @return int
     * @throws \ojy\area\exception\InvalidPositionDataException
     */
    public static function convert(string $worldName): int
    {
        md(AreaPlugin::getInstance()->getDataFolder() . "SimpleAreaLegacy/{$worldName}/");
        $r = 0;
        $path = Server::getInstance()->getDataPath() . "worlds/" . $worldName . "/protects.json";
        $json = (new Config($path, Config::JSON, ['areaIndex' => 0]))->getAll();
        foreach ($json as $id => $areaSection) {

            if (isset($areaSection['startX'])) {
                $area = AreaPlugin::getInstance()->getAreaManager()->addArea($worldName,
                    AreaPlugin::getInstance()->getAreaManager()->positionData($areaSection['startX'], $areaSection['startZ'], $areaSection['endX'], $areaSection['endZ']));

                if ($area instanceof Area) {
                    $area->getProperties()->setCanAccess($areaSection['accessDeny']);
                    $area->getProperties()->setPvp($areaSection['pvpAllow']);
                    $area->getProperties()->setOwner($areaSection['owner']);
                    $area->getProperties()->setBreak(!$areaSection['protect']);
                    $area->getProperties()->setPlace($areaSection['protect']);
                    $residents = array_keys($areaSection['resident']);
                    foreach ($residents as $res) {
                        if ($res !== $areaSection['owner'])
                            $area->getProperties()->setResident($res);
                    }
                    ++$r;

                }
            }
        }

        if (file_exists($path)) {
            copy($path, AreaPlugin::getInstance()->getDataFolder() . "SimpleAreaLegacy/{$worldName}/protects.json");
            unlink($path);
        }

        return $r;
    }
}