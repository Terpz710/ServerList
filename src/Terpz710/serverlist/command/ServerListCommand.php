<?php

declare(strict_types=1);

namespace Terpz710\serverlist\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

use Terpz710\serverlist\ServerList;

class ServerListCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct(ServerList $plugin) {
        parent::__construct("servergui", "Open the server GUI", "/servergui");
        $this->setPermission("serverlist.cmd");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game");
            return;
        }
        $this->plugin->openServerGUI($sender);
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }
}