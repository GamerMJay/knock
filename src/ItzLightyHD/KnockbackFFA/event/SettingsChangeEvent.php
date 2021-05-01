<?php

namespace ItzLightyHD\KnockbackFFA\event;

use ItzLightyHD\KnockbackFFA\Loader;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\Player;

class SettingsChangeEvent extends Event implements Cancellable {

    protected $plugin;
    protected $player;

    public function __construct(Player $player)
    {
        $this->plugin = Loader::getInstance();
        $this->player = $player;
    }

    public function getPlugin(): Loader
    {
        return $this->plugin;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

}