<?php

namespace NurAzliYT\LandProtections\events;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use NurAzliYT\LandProtections\land\LandManager;
use pocketmine\player\Player;

class BlockEventListener implements Listener {
    private LandManager $landManager;

    public function __construct(LandManager $landManager) {
        $this->landManager = $landManager;
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($this->landManager->isLandProtected($block->getPosition()) && !$this->landManager->canBuild($block->getPosition(), $player)) {
            $player->sendMessage("You cannot build here, this land is protected!");
            $event->cancel();
        }
    }
}
