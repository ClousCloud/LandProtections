<?php

namespace NurAzliYT\LandProtections\events;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use NurAzliYT\LandProtections\Main;
use pocketmine\utils\TextFormat;

class BlockEventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $position = $event->getBlock()->getPosition();

        if ($this->plugin->getLandManager()->isLandProtected($position) &&
            !$this->plugin->getLandManager()->isOwner($position, $player->getName()) &&
            !$this->plugin->getLandManager()->isInvited($position, $player->getName())) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You cannot break blocks in this protected area");
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $position = $event->getBlock()->getPosition();

        if ($this->plugin->getLandManager()->isLandProtected($position) &&
            !$this->plugin->getLandManager()->isOwner($position, $player->getName()) &&
            !$this->plugin->getLandManager()->isInvited($position, $player->getName())) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You cannot place blocks in this protected area");
        }
    }
}
