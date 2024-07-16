<?php

namespace NurAzliYT\LandProtections;

use pocketmine\plugin\PluginBase;
use NurAzliYT\LandProtections\commands\ProtectCommand;
use NurAzliYT\LandProtections\commands\InviteToLandCommand;
use NurAzliYT\LandProtections\events\BlockEventListener;
use NurAzliYT\LandProtections\land\LandManager;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;

class Main extends PluginBase
{
    private LandManager $landManager;

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->landManager = new LandManager($this);

        $this->getServer()->getCommandMap()->registerAll("landprotections", [
            new ProtectCommand("protect", $this, $this->landManager, BedrockEconomyAPI::getInstance()),
            new InviteToLandCommand("invite", $this->landManager)
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new BlockEventListener($this->landManager), $this);
    }
}
