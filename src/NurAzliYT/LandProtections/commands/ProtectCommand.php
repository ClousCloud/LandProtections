<?php

namespace NurAzliYT\LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use NurAzliYT\LandProtections\land\LandManager;
use cooldogedev\BedrockEconomy\api\type\AsyncAPI;

class ProtectCommand extends BaseCommand {
    private AsyncAPI $economyAPI;
    private LandManager $landManager;

    public function __construct($plugin, AsyncAPI $economyAPI, LandManager $landManager) {
        parent::__construct($plugin, "protect", "Protect your land");
        $this->economyAPI = $economyAPI;
        $this->landManager = $landManager;
    }

    protected function prepare(): void {
        $this->setPermission("landprotections.command.protect");
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return;
        }

        $player = $sender;
        $cost = $this->getOwningPlugin()->getConfig()->getNested("land.protection-cost");

        $this->economyAPI->getPlayerBalance($player->getName(), function(float $balance) use ($player, $cost) {
            if ($balance < $cost) {
                $player->sendMessage(TextFormat::RED . "You do not have enough money to protect this land");
                return;
            }

            if ($this->landManager->isLandProtected($player->getPosition())) {
                $player->sendMessage(TextFormat::RED . "This land is already protected");
                return;
            }

            $this->economyAPI->subtractFromPlayerBalance($player->getName(), $cost, function(bool $success) use ($player) {
                if ($success) {
                    $this->landManager->protectLand($player->getPosition(), $player->getName());
                    $player->sendMessage(TextFormat::GREEN . "Land protected successfully!");
                } else {
                    $player->sendMessage(TextFormat::RED . "Failed to subtract money for land protection");
                }
            });
        });
    }
}
