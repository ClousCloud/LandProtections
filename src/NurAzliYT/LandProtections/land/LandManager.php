<?php

namespace NurAzliYT\LandProtections\land;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\plugin\PluginBase;

class LandManager {
    private PluginBase $plugin;
    private array $protectedLands = [];

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        // Load protected lands from the configuration or database here
        $this->loadProtectedLands();
    }

    private function loadProtectedLands(): void {
        $data = $this->plugin->getConfig()->get("protectedLands", []);
        foreach ($data as $entry) {
            $position = new Position($entry["x"], $entry["y"], $entry["z"], $this->plugin->getServer()->getWorldManager()->getWorldByName($entry["world"]));
            $this->protectedLands[$position->asVector3()->__toString()] = [
                "owner" => $entry["owner"],
                "invites" => $entry["invites"] ?? []
            ];
        }
    }

    public function isLandProtected(Position $position): bool {
        return isset($this->protectedLands[$position->asVector3()->__toString()]);
    }

    public function isOwner(Position $position, string $playerName): bool {
        return $this->protectedLands[$position->asVector3()->__toString()]["owner"] === $playerName;
    }

    public function canBuild(Position $position, Player $player): bool {
        if (!$this->isLandProtected($position)) {
            return true;
        }

        $landData = $this->protectedLands[$position->asVector3()->__toString()];
        return $landData["owner"] === $player->getName() || in_array($player->getName(), $landData["invites"], true);
    }

    public function protectLand(Position $position, string $owner): void {
        $this->protectedLands[$position->asVector3()->__toString()] = [
            "owner" => $owner,
            "invites" => []
        ];
        $this->saveProtectedLands();
    }

    public function addInvite(Position $position, string $invitee): void {
        $landData = &$this->protectedLands[$position->asVector3()->__toString()];
        if (!in_array($invitee, $landData["invites"], true)) {
            $landData["invites"][] = $invitee;
            $this->saveProtectedLands();
        }
    }

    private function saveProtectedLands(): void {
        $data = [];
        foreach ($this->protectedLands as $positionStr => $landData) {
            $position = Position::fromString($positionStr, $this->plugin->getServer()->getWorldManager());
            $data[] = [
                "x" => $position->getX(),
                "y" => $position->getY(),
                "z" => $position->getZ(),
                "world" => $position->getWorld()->getFolderName(),
                "owner" => $landData["owner"],
                "invites" => $landData["invites"]
            ];
        }
        $this->plugin->getConfig()->set("protectedLands", $data);
        $this->plugin->getConfig()->save();
    }
}
