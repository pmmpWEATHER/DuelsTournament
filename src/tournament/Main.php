<?php

namespace tournament;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Main extends PluginBase {

    private $gameManager;

    public function onEnable() {
        $this->saveDefaultConfig();
        $this->gameManager = new GameManager($this->getConfig()->get("max_players"), $this->getConfig()->get("tournament_name"));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this->gameManager), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "tournament") {
            if (empty($args)) {
                $sender->sendMessage($this->getConfig()->get("help_message"));
                return true;
            }
            $subcommand = strtolower(array_shift($args));
            switch ($subcommand) {
                case "join":
                    if ($sender instanceof Player) {
                        $this->gameManager->addPlayer($sender);
                    } else {
                        $sender->sendMessage($this->getConfig()->get("must_be_player_message"));
                    }
                    break;
                case "leave":
                    if ($sender instanceof Player) {
                        $this->gameManager->leaveTournament($sender);
                    } else {
                        $sender->sendMessage($this->getConfig()->get("must_be_player_message"));
                    }
                    break;
                case "setmap":
                    if (count($args) === 3) {
                        $this->gameManager->setMap($args[0], (int) $args[1], (int) $args[2]);
                    } else {
                        $sender->sendMessage($this->getConfig()->get("setmap_usage_message"));
                    }
                    break;
                case "start":
                    $this->gameManager->startTournament();
                    break;
                default:
                    $sender->sendMessage($this->getConfig()->get("help_message"));
                    break;
            }
            return true;
        }
        return false;
    }
}
