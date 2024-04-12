<?php

namespace Application;

use pocketmine\command\{CommandSender, Command};
use pocketmine\plugin\{PluginBase, Plugin};
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\form\Form;
use pocketmine\form\CustomForm;
use pocketmine\Server;

class Main extends PluginBase implements Listener{

    private $discordWebhookURL; // Discord webhook URL

    public function onEnable(): void {
        $this->getLogger()->info("YouTube Application Plugin Activated");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder() . "ytapplication" . DIRECTORY_SEPARATOR);

        // Add Discord webhook URL here
        $this->discordWebhookURL = "https://discord.com/api/webhooks/1228196948872532018/tEyDfeIUI9RHZNIahDc118yEhD6ctWXfUqI1cOJ0qCsiml2ZVEMf2I3Dql4kSME8pPiD";
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() == "ytapplication") {
            if($sender instanceof Player) {
                $this->applicationForm($sender);
            } else {
                $sender->sendMessage("This command can only be used in-game.");
            }
        }
        return true;
    }

    public function applicationForm(Player $player) {
        $form = new CustomForm(function (Player $player, ?array $data = null) {
            if($data === null) return;
            $config = new Config($this->getDataFolder() . "/ytapplication/" . $player->getName() . ".yml", Config::YAML);
            $config->set("ytapplication",["Channel: " . $data[0], "Subscribers: " . $data[1], "Views: " . $data[2]]);
            $config->save();
            $player->sendMessage("§cYouTube §aApplication Received");

            // Send log to Discord
            $this->sendDiscordLog($data[0], $data[1], $data[2]);
        });
        $form->setTitle("§cYouTube §rApplication");
        $form->addInput("§8* §cEnter Channel Name Below\n§8* §cThen Select Number of Subscribers\n§8* §cThen Select Number of Views\n\n§cNote: §aIf You Write Offensive Content You Will Be Banned\n\n§cNote: §cIf You Can't Select Your Subscribers or Views, Try to Select the Nearest One", "§7Enter Channel Name Here");
        $form->addSlider("Subscribers", 1, 1000);
        $form->addSlider("Views", 1, 1000);
        $form->addLabel("");
        $player->sendForm($form);
    }

    // Function to send log to Discord
    private function sendDiscordLog(string $channel, int $subscribers, int $views) {
        $data = [
            "username" => "YouTube Application Bot", // Sender's name
            "content" => "Channel: $channel, Subscribers: $subscribers, Views: $views" // Message to be sent
        ];
        $options = [
            "http" => [
                "header" => "Content-Type: application/json",
                "method" => "POST",
                "content" => json_encode($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($this->discordWebhookURL, false, $context);
        if($result === false){
            $this->getLogger()->error("An error occurred while sending log to Discord.");
        }
    }
}
