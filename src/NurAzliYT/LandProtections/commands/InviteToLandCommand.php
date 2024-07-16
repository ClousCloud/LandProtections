<?php

namespace LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use LandProtections\land\LandManager;

class InviteToLandCommand extends BaseCommand
{
    private LandManager $landManager;

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->landManager = $landManager;
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player", true));
        $this->setPermission("landprotections.command.invite");
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

        $playerName = $args["player"];
        $position = $sender->getPosition();

        if (!$this->landManager->isOwner($position, $sender->getName())) {
            $sender->sendMessage("You do not own this land.");
            return;
        }

        $this->landManager->addInvite($position, $playerName);
        $sender->sendMessage("Player $playerName has been invited to your land.");
    }
}
