<?php

namespace Application;

use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\plugin\{PluginBase};
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    
    public static Main $main;
    public Config $config;
    public Webhook $webhook; 

    public function onEnable(): void {
        self::$main = $this;
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->getLogger()->info("YouTube Application Plugin Activated");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder() . "ytapplication" . DIRECTORY_SEPARATOR);
        
        // Add Discord webhook URL here
        $this->webhook = new Webhook($this->config->get("webhook_url")); 
        $this->getServer()->getCommandMap()->register("ytapplication", new YTApplicationCommand());
    }
    
    public static function getMain() : Main {
        return self::$main;
    }
}
