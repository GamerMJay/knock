<?php

namespace ItzLightyHD\KnockbackFFA;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use JackMD\UpdateNotifier\UpdateNotifier;
use CortexPE\Commando\PacketHooker;
use ItzLightyHD\KnockbackFFA\command\KnockbackCommand;
use ItzLightyHD\KnockbackFFA\listeners\DamageListener;
use ItzLightyHD\KnockbackFFA\listeners\EssentialsListener;
use ItzLightyHD\KnockbackFFA\listeners\LevelListener;
use ItzLightyHD\KnockbackFFA\utils\GameSettings;
use ItzLightyHD\KnockbackFFA\utils\KnockbackPlayer;
use pocketmine\level\Level;

class Loader extends PluginBase {

    /** @var Config $config */
    protected $config;
    /** @var self $instance */
    protected static $instance;

    // What happens when plugin is enabled
    public function onEnable(): void {
        // Sets the instance
        self::$instance = $this;
        // Registers the event listeners
        $this->registerEvents();
        // Register the game settings
        new GameSettings($this);
        // Loads the arena that is wrote in the folder and upgrades it to the PM 4 world format
        $this->getServer()->getWorldManager()->loadWorld(GameSettings::getInstance()->world, true);
        // Checking for a new update (new system)
        UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
        // Register the packet hooker for Commando (command framework)
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        // Registers the "kbffa" command
        $this->getServer()->getCommandMap()->register($this->getName(), new KnockbackCommand($this));
        // Check for world existance (if the world doesn't exist, it will instantly disable the plugin)
        if(!($this->getServer()->getLevelByName(GameSettings::getInstance()->world) instanceof Level)) {
            $this->getLogger()->alert("The world specified for the arena in the configuration file doesn't exist. Change it or make sure it has the correct name!");
            $plugin = $this->getServer()->getPluginManager()->getPlugin($this->getName());
            $this->getServer()->getPluginManager()->disablePlugin($plugin);
        }
        if(!($this->getServer()->getLevelByName(GameSettings::getInstance()->lobby_world) instanceof Level)) {
            if(!($this->getServer()->getLevelByName(GameSettings::getInstance()->world) instanceof Level)) {
                $this->getLogger()->alert("The world specified for the lobby in the configuration file doesn't exist. Change it or make sure it has the correct name!");
                $plugin = $this->getServer()->getPluginManager()->getPlugin($this->getName());
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
            }
        }
    }

    private function registerEvents() {
        // Knockback player, used for getting the killstreak, the last damager, etc...
        $this->getServer()->getPluginManager()->registerEvents(new KnockbackPlayer($this), $this);
        // All the event listeners
        $this->getServer()->getPluginManager()->registerEvents(new DamageListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EssentialsListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new LevelListener($this), $this);
    }

    // Helpul to make an API for the plugin
    public static function getInstance(): self
    {
        return self::$instance;
    }
}