<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater;

use Ken_Cir\AutoUpdater\tasks\GetPocketMineFileAsyncTask;
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
        if (!(bool)$this->plugin->getConfig()->get("autoUpdateEnable", true)) return;

        $updateInfo = $event->getUpdater()->getUpdateInfo();
        if ($updateInfo->git_commit === (string)$this->plugin->getConfig()->get("gitHash", "") or $updateInfo->is_dev) return;

        $this->plugin->getConfig()->get("gitHash", $updateInfo->git_commit);
        $this->plugin->getServer()->getAsyncPool()->submitTask(new GetPocketMineFileAsyncTask($updateInfo->download_url, $this->plugin->getServer()->getDataPath()));
    }
}