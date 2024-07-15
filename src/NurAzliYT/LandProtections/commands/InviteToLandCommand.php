<?php

namespace NurAzliYT\LandProtections\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use NurAzliYT\LandProtections\Main;

class InviteToLandCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setPermission("landprotections.command.invitetoland");
        $this->registerArgument(0, new \CortexPE\Commando\args\RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $label, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return;
        }

        if (empty($args["player"])) {
            $sender->sendMessage(TextFormat::RED . "Usage: /invitetoland <player>");
            return;
        }

        $player = $sender;
        $targetName = $args["player"];
        $target = $this->getOwningPlugin()->getServer()->getPlayerByPrefix($targetName);

        if ($target === null || !$target->isOnline()) {
            $player->sendMessage(TextFormat::RED . "Player not found or not online");
            return;
        }

        $position = $player->getPosition();
        if (!$this->getOwningPlugin()->getLandManager()->isLandProtected($position) || !$this->getOwningPlugin()->getLandManager()->isOwner($position, $player->getName())) {
            $player->sendMessage(TextFormat::RED . "You do not own this land or it is not protected");
            return;
        }

        $this->getOwningPlugin()->getLandManager()->addInvite($position, $target->getName());
        $player->sendMessage(TextFormat::GREEN . "Player invited successfully!");
        $target->sendMessage(TextFormat::GREEN . "You have been invited to a protected land by " . $player->getName());
    }
}
