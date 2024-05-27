<?php

namespace Application;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use jojoe77777\FormAPI\CustomForm;


class YTApplicationCommand extends Command {

    public function __construct(){
        parent::__construct("ytapplication", "Register your yt application for partnership", "/ytapplication", ["ytpartner"]);
        $this->setPermission("pocketmine.group.user");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($sender instanceof Player) {
            $this->applicationForm($sender);
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
    }


    public function applicationForm(Player $player) {
        $form = new CustomForm(function (Player $player, ?array $data = null) {
            if($data === null) return;
            $config = new Config(Main::getMain()->getDataFolder() . "/ytapplication/" . $player->getName() . ".yml", Config::YAML);
            $config->set("ytapplication",["Channel: " . $data[0], "Discord: . $data[1]", "Subscribers: " . $data[2], "Views: " . $data[3]]);
            $config->save();
            $player->sendMessage("§cYouTube §aApplication Received");

            // Send log to Discord
            $this->sendDiscordLog($data[0], $data[1], $data[2], $data[3]);
        });
        $form->setTitle("§cYouTube §rApplication");
        $form->addInput("§8* §cEnter Channel Name Below\n§8* §cThen Select Number of Subscribers\n§8* §cThen Select Number of Views\n\n§cNote: §aIf You Write Offensive Content You Will Be Banned\n\n§cNote: §cIf You Can't Select Your Subscribers or Views, Try to Select the Nearest One", "§7Enter Channel Name Here");
        $form->addInput("§8* §cEnter Discord Username Below");
        $form->addSlider("Subscribers", 1, 1000);
        $form->addSlider("Views", 1, 1000);
        $player->sendForm($form);
    }

    // Function to send log to Discord
    private function sendDiscordLog(string $channel, string $discord, int $subscribers, int $views) {
        $msg = new Message();
        $msg->setUsername(Main::getMain()->config->get("webhook_name")); // optional
        $msg->setAvatarURL(Main::getMain()->config->get("webhook_logo")); // optional
        $embed = new Embed();
        $embed->setTitle(Main::getMain()->config->get("embed_title"));
        $embed->setDescription("Channel: $channel, Discord: $discord. Subscribers: $subscribers, Views: $views");
        $msg->addEmbed($embed);
        Main::getMain()->webhook->send($msg);
    }
}