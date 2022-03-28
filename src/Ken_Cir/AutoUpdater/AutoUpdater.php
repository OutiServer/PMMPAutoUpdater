<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater;

use JsonException;
use Ken_Cir\AutoUpdater\commands\CheckUpdateCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class AutoUpdater extends PluginBase
{
    use SingletonTrait;

    private Config $messages;

    protected function onLoad(): void
    {
        self::setInstance($this);
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->saveResource("config.yml");

        $this->messages = new Config("{$this->getDataFolder()}messages.yml", Config::YAML);

        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new CheckUpdateCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
    }

    protected function onDisable(): void
    {
        try {
            $this->getConfig()->save();
        } catch (JsonException) {
        }
    }

    /**
     * @return Config
     */
    public function getMessages(): Config
    {
        return $this->messages;
    }
}