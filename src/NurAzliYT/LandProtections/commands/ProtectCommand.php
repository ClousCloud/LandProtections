<?php

namespace NurAzliYT\LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use NurAzliYT\LandProtections\land\LandManager;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\plugin\Plugin;
use NurAzliYT\LandProtections\Main;

class ProtectCommand extends BaseCommand
{
    private LandManager $landManager;
    private BedrockEconomyAPI $economyAPI;
    private Main $plugin;

    public function __construct(string $name, Main $plugin, LandManager $landManager, BedrockEconomyAPI $economyAPI)
    {
        parent::__construct($name, "Protect your land", "/protect");
        $this->setPermission("landprotections.command.protect");
        $this->plugin = $plugin;
        $this->landManager = $landManager;
        $this->economyAPI = $economyAPI;
    }

    protected function prepare(): void
    {
        // No arguments needed for this command
    }

    public function onRun(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $position = $sender->getPosition();
        $config = $this->plugin->getConfig();

        if ($this->landManager->isLandProtected($position)) {
            $sender->sendMessage("This land is already protected.");
            return;
        }

        $cost = $config->get("protect-cost", 100);
        $this->economyAPI::ASYNC()->getPlayerBalance($sender->getName())->onCompletion(function($balance) use ($sender, $cost, $position) {
            if ($balance < $cost) {
                $sender->sendMessage("You do not have enough money to protect this land.");
                return;
            }

            $this->economyAPI::ASYNC()->subtractFromPlayerBalance($sender->getName(), $cost)->onCompletion(function() use ($sender, $position) {
                $this->landManager->protectLand($position, $sender->getName());
                $sender->sendMessage("Your land has been protected.");
            });
        });
    }
}
