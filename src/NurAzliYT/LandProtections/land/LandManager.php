<?php

namespace NurAzliYT\LandProtections\land;

use NurAzliYT\LandProtections\Main;
use pocketmine\world\Position;

class LandManager {

    private Main $plugin;
    private array $protectedLands = [];
    private int $landCounter = 1;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function protectLand(Position $position, string $owner): void {
        $size = $this->plugin->getConfig()->getNested("land.protection-size");
        $landID = $this->landCounter++;
        $this->protectedLands[$landID] = [
            "owner" => $owner,
            "position" => $position,
            "size" => $size,
            "invited" => []
        ];
    }

    public function isLandProtected(Position $position): bool {
        foreach ($this->protectedLands as $land) {
            if ($this->isWithinLand($position, $land["position"], $land["size"])) {
                return true;
            }
        }
        return false;
    }

    public function isOwner(Position $position, string $playerName): bool {
        foreach ($this->protectedLands as $land) {
            if ($this->isWithinLand($position, $land["position"], $land["size"]) && $land["owner"] === $playerName) {
                return true;
            }
        }
        return false;
    }

    public function addInvite(Position $position, string $playerName): void {
        foreach ($this->protectedLands as &$land) {
            if ($this->isWithinLand($position, $land["position"], $land["size"])) {
                $land["invited"][] = $playerName;
                return;
            }
        }
    }

    public function isInvited(Position $position, string $playerName): bool {
        foreach ($this->protectedLands as $land) {
            if ($this->isWithinLand($position, $land["position"], $land["size"]) && in_array($playerName, $land["invited"])) {
                return true;
            }
        }
        return false;
    }

    private function isWithinLand(Position $pos, Position $landPos, int $size): bool {
        return abs($pos->getX() - $landPos->getX()) <= $size &&
               abs($pos->getZ() - $landPos->getZ()) <= $size;
    }
}
