<?php

namespace NurAzliYT\LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use NurAzliYT\LandProtections\Main;

class ProtectCommand extends BaseCommand {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "protect", "Protect a piece of land");
        $this->plugin = $plugin;
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
        $economyAPI = $this->plugin->getEconomyAPI();
        $cost = $this->plugin->getConfig()->getNested("land.protection-cost");

        $balance = $economyAPI->getPlayerBalance($player->getName());
        if ($balance < $cost) {
            $player->sendMessage(TextFormat::RED . "You do not have enough money to protect this land");
            return;
        }

        if ($this->plugin->getLandManager()->isLandProtected($player->getPosition())) {
            $player->sendMessage(TextFormat::RED . "This land is already protected");
            return;
        }

        $economyAPI->subtractFromPlayerBalance($player->getName(), $cost);
        $this->plugin->getLandManager()->protectLand($player->getPosition(), $player->getName());

        $player->sendMessage(TextFormat::GREEN . "Land protected successfully!");
    }
}
