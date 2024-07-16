<?php

namespace NurAzliYT\LandProtections\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use NurAzliYT\LandProtections\land\LandManager;
use pocketmine\plugin\Plugin;

class InviteToLandCommand extends Command
{
    private LandManager $landManager;

    public function __construct(Plugin $plugin, LandManager $landManager)
    {
        parent::__construct("invite", "Invite a player to your land", "/invite <player>");
        $this->setPermission("landprotections.command.invite");
        $this->landManager = $landManager;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage("Usage: /invite <player>");
            return;
        }

        $playerName = $args[0];
        $position = $sender->getPosition();

        if (!$this->landManager->isOwner($position, $sender->getName())) {
            $sender->sendMessage("You do not own this land.");
            return;
        }

        $this->landManager->addInvite($position, $playerName);
        $sender->sendMessage("Player $playerName has been invited to your land.");
    }
}
