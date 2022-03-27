<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater;

use Ken_Cir\AutoUpdater\language\LanguageManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class AutoUpdater extends PluginBase
{
    use SingletonTrait;

    private LanguageManager $languageManager;

    protected function onLoad(): void
    {
        self::setInstance($this);
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->saveResource("lang/jpn.ini", true);
        $this->saveResource("lang/eng.ini", true);

        $this->languageManager = new LanguageManager($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
    }

    protected function onDisable(): void
    {
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager(): LanguageManager
    {
        return $this->languageManager;
    }
}