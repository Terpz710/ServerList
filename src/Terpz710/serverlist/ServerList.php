<?php

declare(strict_types=1);

namespace Terpz710\serverlist;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\TransferPacket;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

use Terpz710\serverlist\command\ServerListCommand;

class ServerList extends PluginBase {

    private $serversConfig;

    public function onEnable(): void {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->saveDefaultConfig();
        $this->serversConfig = new Config($this->getDataFolder() . "servers.yml", Config::YAML);

        $this->getServer()->getCommandMap()->register("ServerList", new ServerListCommand($this));
    }

    public function getServersConfig(): Config {
        return $this->serversConfig;
    }

    public function openServerGUI(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("Server List");
        $inventory = $menu->getInventory();

        $servers = $this->serversConfig->getAll();
        $slot = 0;
        foreach ($servers as $serverName => $serverData) {
            if ($slot > 26) break;
            $item = VanillaItems::PAPER();
            $item->setCustomName($serverName);
            $item->setLore([$serverData["address"], "Click to join"]);
            $inventory->setItem($slot, $item);
            $slot++;
        }

        $menu->setListener(function(Player $player, Item $itemClicked) : bool {
            $serverName = $itemClicked->getName();
            $serverData = $this->serversConfig->get($serverName);
            if ($serverData !== null) {
                $this->transferPlayerToServer($player, $serverData["address"], $serverData["port"] ?? 19132);
            }
            return true;
        });

        $menu->send($player);
    }

    private function transferPlayerToServer(Player $player, string $address, int $port): void {
        $pk = new TransferPacket();
        $pk->address = $address;
        $pk->port = $port;
        $player->getNetworkSession()->sendDataPacket($pk);
        $player->sendMessage("Transferring to server: " . $address . ":" . $port);
    }
}
