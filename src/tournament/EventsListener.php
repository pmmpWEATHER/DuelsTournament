<?php

declare(strict_types=1);

namespace tournament;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class EventListener implements Listener {

    private GameManager $manager;

    public function __construct(GameManager $manager) {
        $this->manager = $manager;
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        $this->manager->leaveTournament($player);
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (!$item->isNull() && $item->getId() === Item::COMPASS) {
            if ($this->manager->isPlayerInTournament($player)) {
                $this->manager->teleportPlayerToLobby($player);
            } else {
                $player->sendMessage(TF::YELLOW . "You are not in a tournament.");
            }
        }
    }
}
