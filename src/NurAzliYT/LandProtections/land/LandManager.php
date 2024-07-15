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
            if ($this->isWithinLand($position, $land["position"], $land["size"]) && $land
