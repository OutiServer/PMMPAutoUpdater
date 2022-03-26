<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater;

use pocketmine\event\Listener;
use pocketmine\event\server\UpdateNotifyEvent;

class EventHandler implements Listener
{
    private AutoUpdater $plugin;

    public function __construct(AutoUpdater $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onUpdateNotify(UpdateNotifyEvent $event): void
    {

    }
}