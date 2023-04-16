<?php

namespace tournament;

use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Game {

    /** @var GameManager */
    private $gameManager;

    /** @var string */
    private $map;

    /** @var int */
    private $currentRound;

    /** @var int */
    private $roundTime;

    /** @var int */
    private $gameTime;

    /** @var int */
    private $totalRounds;

    /** @var bool */
    private $tournamentOpen;

    /** @var Player[] */
    private $players = [];

    /** @var array */
    private $rounds = [];

    /** @var array */
    private $spawnPoints = [];

    /** @var Vector3 */
    private $waitingLobby;

    public function __construct(GameManager $gameManager, array $config) {
        $this->gameManager = $gameManager;
        $this->map = $config["map"];
        $this->currentRound = 0;
        $this->roundTime = $config["round_time"];
        $this->gameTime = $config["game_time"];
        $this->totalRounds = $config["rounds"];
        $this->tournamentOpen = false;
        $this->spawnPoints = $config["spawn_points"];
        $this->waitingLobby = new Vector3($config["waiting_lobby"]["x"], $config["waiting_lobby"]["y"], $config["waiting_lobby"]["z"]);
    }

    public function isTournamentOpen(): bool {
        return $this->tournamentOpen;
    }

    public function openTournament(): void {
        $this->tournamentOpen = true;
    }

    public function closeTournament(): void {
        $this->tournamentOpen = false;
        $this->currentRound = 0;
        $this->players = [];
        $this->rounds = [];
    }

    public function startTournament(): void {
        $this->currentRound = 1;
        $this->startRound();
    }

    private function startRound(): void {
        $this->broadcastMessage(TF::GREEN . "Starting Round " . $this->currentRound);
        $this->gameManager->getScheduler()->scheduleDelayedTask(new Task([$this, "startGame"]), 20 * $this->roundTime);
    }

    public function startGame(): void {
        $round = new Round($this->currentRound, $this->players, $this->gameManager, $this->spawnPoints, $this->waitingLobby);
        $round->start();
        $this->rounds[] = $round;
        $this->currentRound++;

        if ($this->currentRound <= $this->totalRounds) {
            $this->startRound();
        } else {
            $this->endTournament();
        }
    }

    public function endTournament(): void {
        $winner = $this->getWinner();
        $this->broadcastMessage(TF::GREEN . "Tournament Ended. Winner: " . $winner->getName());
        $this->closeTournament();
    }

  public function getWinner(): ?Player {
  $participants = $this->game->getParticipants();
  $numParticipants = count($participants);

  if ($numParticipants === 0) {
    return null;
  }

  if ($numParticipants === 1) {
    return $participants[0];
  }

  if ($this->game->getCurrentRound() === $this->game->getMaxRounds()) {
    $maxPoints = 0;
    $winner = null;
    foreach ($participants as $participant) {
      $points = $this->game->getPlayerPoints($participant);
      if ($points > $maxPoints) {
        $maxPoints = $points;
        $winner = $participant;
      }
    }
    return $winner;
  }

  return null;
}
