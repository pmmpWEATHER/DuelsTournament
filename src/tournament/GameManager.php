<?php

declare(strict_types=1);

namespace tournament;

use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class GameManager{
    private $plugin;
    private $game;

    public function __construct(Tournament $plugin){
        $this->plugin = $plugin;
    }

    public function createGame(string $mapName, Level $level, array $players): void{
        $this->game = new Game($mapName, $level, $players);
        $this->game->startGame();
    }

    public function joinTournament(Player $player): void{
        if (!$this->game->isTournamentOpen()) {
            $player->sendMessage(TF::YELLOW . "There is no tournament currently open.");
            return;
        }

        $this->game->addTournamentPlayer($player);
        $player->sendMessage(TF::GREEN . "You have joined the tournament.");
    }

    public function leaveTournament(Player $player): void{
        if (!$this->game->isTournamentOpen()) {
            $player->sendMessage(TF::YELLOW . "There is no tournament currently open.");
            return;
        }

        if (!$this->game->isTournamentStarted()) {
            $this->game->removeTournamentPlayer($player);
            $player->sendMessage(TF::GREEN . "You have left the tournament.");
            return;
        }

        if ($this->game->isPlayerInTournament($player)) {
            $this->game->removeTournamentPlayer($player);
            $player->sendMessage(TF::YELLOW . "You have been eliminated from the tournament.");
        } else {
            $player->sendMessage(TF::YELLOW . "You are not in the tournament.");
        }
    }

    public function setTournamentMap(string $mapName, Level $level): void{
        $this->game->setTournamentMap($mapName, $level);
        $this->plugin->getServer()->broadcastMessage(TF::GREEN . "Tournament map set to $mapName.");
    }

    public function startTournament(): void{
        if (!$this->game->isTournamentOpen()) {
            $this->plugin->getServer()->broadcastMessage(TF::YELLOW . "There is no tournament currently open.");
            return;
        }

        if (count($this->game->getTournamentPlayers()) < 2) {
            $this->plugin->getServer()->broadcastMessage(TF::YELLOW . "Not enough players to start the tournament.");
            return;
        }

        $this->game->startTournament();
    }

    public function getCurrentRound(): int{
        return $this->game->getCurrentRound();
    }

    public function getWinner(): ?Player{
        return $this->game->getWinner();
    }

}
