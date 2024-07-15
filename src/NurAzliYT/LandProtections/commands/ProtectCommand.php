<?php

namespace NurAzliYT\LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use NurAzliYT\LandProtections\Main;

class ProtectCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setPermission("landprotections.command.protect");
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return;
        }

        $player = $sender;
        $economyAPI = $this->getOwningPlugin()->getEconomyAPI();
        $cost = $this->getOwningPlugin()->getConfig()->getNested("land.protection-cost");

        $economyAPI->getPlayerBalance($player->getName(), function(float $balance) use ($player, $cost) {
            if ($balance < $cost) {
                $player->sendMessage(TextFormat::RED . "You do not have enough money to protect this land");
                return;
            }

            if ($this->getOwningPlugin()->getLandManager()->isLandProtected($player->getPosition())) {
                $player->sendMessage(TextFormat::RED . "This land is already protected");
                return;
            }

            $this->getOwningPlugin()->getEconomyAPI()->subtractFromPlayerBalance($player->getName(), $cost, function(bool $success) use ($player) {
                if ($success) {
                    $this->getOwningPlugin()->getLandManager()->protectLand($player->getPosition(), $player->getName());
                    $player->sendMessage(TextFormat::GREEN . "Land protected successfully!");
                } else {
                    $player->sendMessage(TextFormat::RED . "Failed to subtract money for land protection");
                }
            });
        });
    }
}
