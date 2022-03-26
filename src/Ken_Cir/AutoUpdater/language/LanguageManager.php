<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\language;

use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\lang\Language;
use pocketmine\utils\SingletonTrait;

class LanguageManager
{
    use SingletonTrait;

    private AutoUpdater $plugin;

    /**
     * @var Language[]
     */
    private array $languages;

    public function __construct(AutoUpdater $plugin)
    {
        self::setInstance($this);
        $this->plugin = $plugin;

        $this->languages = [];
        foreach (Language::getLanguageList("{$plugin->getDataFolder()}lang") as $language) {
            $this->languages[$language] = new Language($language, "{$plugin->getDataFolder()}lang", $language);
        }
    }

    public function getDefaultLang(): Language
    {
        return $this->getLang($this->plugin->getServer()->getLanguage()->getLang());
    }

    public function getLang(string $lang): Language
    {
        if (!isset($this->languages[$lang])) return $this->languages["eng"];
        return $this->languages[$lang];
    }
}