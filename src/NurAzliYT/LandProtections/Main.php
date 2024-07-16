<?php

namespace NurAzliYT\LandProtections;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use NurAzliYT\LandProtections\commands\ProtectCommand;
use NurAzliYT\LandProtections\commands\InviteToLandCommand;
use NurAzliYT\LandProtections\events\BlockEventListener;
use NurAzliYT\LandProtections\land\LandManager;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use CortexPE\Commando\PacketHooker;

class Main extends PluginBase {
    private BedrockEconomyAPI $economyAPI;
    private LandManager $landManager;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        
        $this->getLogger()->info(TextFormat::GREEN . "LandProtections enabled");

        $plugin = $this->getServer()->getPluginManager()->getPlugin("BedrockEconomy");
        if ($plugin === null || !$plugin->isEnabled()) {
            $this->getLogger()->error("BedrockEconomy is not installed or enabled");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        $this->economyAPI = BedrockEconomyAPI::ASYNC();
        $this->landManager = new LandManager($this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new ProtectCommand($this),
            new InviteToLandCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new BlockEventListener($this), $this);
    }

    public function getEconomyAPI(): BedrockEconomyAPI {
        return $this->economyAPI;
    }

    public function getLandManager(): LandManager {
        return $this->landManager;
    }
}
