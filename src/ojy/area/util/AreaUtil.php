<?php

namespace ojy\area\util;

use ojy\area\Area;
use ojy\area\AreaPlugin;
use ojy\area\PropertyTypes;
use ojy\coin\Coin;
use pocketmine\Player;
use pocketmine\Server;

/**
 * @param $player
 * @return string|null
 */
function online($player): ?string
{
    if (($p = Server::getInstance()->getPlayer($player)) instanceof Player)
        return $p->getName();
    return null;
}

class AreaUtil
{
    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function break(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setBreak(!$ap->get(PropertyTypes::TYPE_CAN_BREAK));
                if ($ap->get(PropertyTypes::TYPE_CAN_BREAK)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 블럭부수기를 허용하였습니다.');
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 블럭부수기를 비허용하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    public static function setSpawn(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setSpawn($executor->getPosition());
                $executor->sendMessage(AreaPlugin::PREFIX . '스폰지점을 설정했습니다!');
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }


    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function place(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setPlace(!$ap->get(PropertyTypes::TYPE_CAN_PLACE));
                if ($ap->get(PropertyTypes::TYPE_CAN_PLACE)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 블럭설치를 허용하였습니다.');
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 블럭설치를 비허용하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function door(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setOpenDoor(!$ap->get(PropertyTypes::TYPE_OPEN_DOOR));
                if ($ap->get(PropertyTypes::TYPE_OPEN_DOOR)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . '해당 지역에서의 문열기를 허용하였습니다.');
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '해당 지역에서의 문열기를 비허용하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function pvp(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setPvp(!$ap->get(PropertyTypes::TYPE_PVP));
                if ($ap->get(PropertyTypes::TYPE_PVP)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 전투를 허용하였습니다.');
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 전투를 비허용하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }


    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function access(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $ap->setCanAccess(!$ap->get(PropertyTypes::TYPE_CAN_ACCESS));
                if ($ap->get(PropertyTypes::TYPE_CAN_ACCESS)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 접근을 허용하였습니다.');
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '땅 접근을 비허용하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     * @param string $name
     */
    public static function transfer(Player $executor, ?Area $area, string $name): void
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if (strtolower($ap->getOwner()) === strtolower($executor->getName()) || $executor->isOp()) {
                if (online($name) !== null) {
                    $name = online($name);
                    if (AreaPlugin::getInstance()->getQueue()->isQueue($executor, "transfer:{$name}")) {
                        $ap->setOwner($name);
                        $executor->sendMessage(AreaPlugin::PREFIX . "{$name} 님에게 {$area->getId()}번 땅을 양도했습니다.");
                    } else {
                        AreaPlugin::getInstance()->getQueue()->setQueue($executor, "transfer:{$name}");
                        $executor->sendMessage(AreaPlugin::PREFIX . "땅을 {$name}님에게 양도하시려면 명령어를 한번 더 입력해주세요.");
                    }
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . "{$name} 님은 게임에 접속중이지 않습니다.");
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     * @param string $name
     */
    public static function kick(Player $executor, ?Area $area, string $name): void
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                $residentName = online($name) ?? $name;
                if ($ap->unsetResident($residentName)) {
                    $executor->sendMessage(AreaPlugin::PREFIX . "{$residentName} 님을 거주자목록에서 제거하였습니다.");
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . "{$residentName} 님은 거주자목록에 없습니다.");
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     * @param string $name
     */
    public static function share(Player $executor, ?Area $area, string $name): void
    {
        if ($area instanceof Area) {
            $ap = $area->getProperties();
            if ($ap->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                if (online($name) !== null) {
                    $name = online($name);
                    if ($executor->isOp() || $executor->getName() !== $name) {
                        $ap->setResident($name);
                        $executor->sendMessage(AreaPlugin::PREFIX . "{$name} 님을 거주자목록에 추가하였습니다!");
                    } else {
                        $executor->sendMessage(AreaPlugin::PREFIX . '자신에게 공유할 수 없습니다.');
                    }
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . "{$name} 님은 게임에 접속중이지 않습니다.");
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function purchase(Player $executor, ?Area $area)
    {
        $am = AreaPlugin::getInstance()->getAreaManager();
        if ($area instanceof Area) {
            if ($area->getProperties()->getOwner() === "") {
                $area_count = count($am->getPlayerAreas($executor->getLevel()->getFolderName(), $executor->getName()));
                $wp = AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($executor->getLevel()->getFolderName());
                if ($wp->getMaxAreaCount() > $area_count) {
                    $money = Coin::getCoin($executor);
                    if ($wp->getAreaPrice() <= $money) {
                        if (AreaPlugin::getInstance()->getQueue()->isQueue($executor, 'purchase')) {
                            Coin::reduceCoin($executor, $wp->getAreaPrice());
                            $area->getProperties()->setOwner($executor->getName());
                            $executor->sendMessage(AreaPlugin::PREFIX . "{$area->getId()}번 땅을 구매했습니다.");
                        } else {
                            AreaPlugin::getInstance()->getQueue()->setQueue($executor, "purchase");
                            $executor->sendMessage(AreaPlugin::PREFIX . '이 땅을 구매하시려면 명령어를 한번 더 입력해주세요.');
                        }
                    } else {
                        $executor->sendMessage(AreaPlugin::PREFIX . "돈이 부족합니다! 땅 가격: {$wp->getAreaPrice()} 코인");
                    }
                } else {
                    $executor->sendMessage(AreaPlugin::PREFIX . '최대 보유 가능한 땅 수를 초과하였습니다.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '이미 이 땅에는 주인이 있습니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function sell(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            if ($area->getProperties()->getOwner() === strtolower($executor->getName()) || $executor->isOp()) {
                if (AreaPlugin::getInstance()->getQueue()->isQueue($executor, "sell")) {
                    $price = ceil(AreaPlugin::getInstance()->getWorldManager()->getWorldProperties($executor->getLevel()->getFolderName())->getAreaPrice() / 2);
                    $area->getProperties()->setOwner("");
                    foreach ($area->getProperties()->getResidents() as $r)
                        $area->getProperties()->unsetResident($r);
                    Coin::addCoin($executor, $price);
                    $executor->sendMessage(AreaPlugin::PREFIX . "땅을 판매하여 {$price} 코인을 얻었습니다.");
                } else {
                    AreaPlugin::getInstance()->getQueue()->setQueue($executor, "sell");
                    $executor->sendMessage(AreaPlugin::PREFIX . '이 땅을 판매하시려면 명령어를 한번 더 입력해주세요.');
                }
            } else {
                $executor->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 기능입니다.');
            }
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $executor
     * @param Area|null $area
     */
    public static function information(Player $executor, ?Area $area)
    {
        if ($area instanceof Area) {
            $owner = $area->getProperties()->getOwner() === '' ? '없음' : $area->getProperties()->getOwner();
            $residents = implode(", ", $area->getProperties()->getResidents());
            $place = $area->getProperties()->get(PropertyTypes::TYPE_CAN_PLACE) ? '가능' : '불가능';
            $break = $area->getProperties()->get(PropertyTypes::TYPE_CAN_BREAK) ? '가능' : '불가능';
            $executor->sendMessage('§l§b- - - - - - - - - - - - -');
            $executor->sendMessage("땅 번호 §a> §f{$area->getId()}");
            $executor->sendMessage("땅 주인 §a> §f{$owner}");
            $executor->sendMessage("공유 목록 §a> §f{$residents}");
            $executor->sendMessage("블럭 설치 §a> §f{$place}");
            $executor->sendMessage("블럭 부수기 §a> §f{$break}");
            $executor->sendMessage('§l§b- - - - - - - - - - - - -');
        } else {
            $executor->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    /**
     * @param Player $sender
     * @param Area|null $area
     */
    public static function goOut(Player $sender, ?Area $area)
    {
        if ($area instanceof Area) {
            if ($area->getProperties()->isResident($sender->getName())) {
                if ($area->getProperties()->getOwner() === strtolower($sender->getName())) {
                    $sender->sendMessage(AreaPlugin::PREFIX . '주인은 땅에서 나갈 수 없습니다.');
                } else {
                    if (AreaPlugin::getInstance()->getQueue()->isQueue($sender, 'goOut')) {
                        $area->getProperties()->unsetResident($sender->getName());
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$area->getId()}번 땅에서 나갔습니다.");
                    } else {
                        AreaPlugin::getInstance()->getQueue()->setQueue($sender, 'goOut');
                        $sender->sendMessage(AreaPlugin::PREFIX . '이 땅에서 나가시려면 명령어를 한번 더 입력해주세요.');
                    }
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '이 땅에 거주중이지 않습니다.');
            }
        } else {
            $sender->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    public static function remove(Player $sender, ?Area $area)
    {
        $am = AreaPlugin::getInstance()->getAreaManager();
        if ($area instanceof Area) {
            if ($area->getProperties()->getOwner() === strtolower($sender->getName()) || $sender->isOp()) {
                if (AreaPlugin::getInstance()->getQueue()->isQueue($sender, 'remove')) {
                    if ($am->removeArea($sender->getLevel()->getFolderName(), $area->getId())) {
                        $sender->sendMessage(AreaPlugin::PREFIX . "{$area->getId()}번 땅을 삭제했습니다.");
                    } else {
                        $sender->sendMessage(AreaPlugin::PREFIX . '땅을 삭제할 수 없습니다. (알 수 없는 오류)');
                    }
                } else {
                    AreaPlugin::getInstance()->getQueue()->setQueue($sender, 'remove');
                    $sender->sendMessage(AreaPlugin::PREFIX . '이 땅을 삭제하시려면 명령어를 한번 더 입력해주세요.');
                }
            } else {
                $sender->sendMessage(AreaPlugin::PREFIX . '땅 주인만 사용 가능한 명령어입니다.');
            }
        } else {
            $sender->sendMessage(AreaPlugin::PREFIX . '땅 정보를 찾을 수 없습니다.');
        }
    }

    public static function list(Player $sender)
    {
        $am = AreaPlugin::getInstance()->getAreaManager();
        $areas = $am->getInResidenceArea($sender->getLevel()->getFolderName(), $sender->getName());
        if (count($areas) > 0) {
            $nums = [];
            foreach ($areas as $area) {
                if ($area instanceof Area) {
                    $nums[] = $area->getId();
                }
            }
            $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드에서 주인이거나 거주중인 땅 목록을 표시합니다.");
            $sender->sendMessage(AreaPlugin::PREFIX . implode(', ', $nums));
        } else {
            $sender->sendMessage(AreaPlugin::PREFIX . "{$sender->getLevel()->getFolderName()} 월드에서 갖고있는 땅이 없습니다.");
        }
    }
}