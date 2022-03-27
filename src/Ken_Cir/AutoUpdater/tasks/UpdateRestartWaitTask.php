<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\tasks;

use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\lang\Translatable;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use function floor;
use function count;
use function register_shutdown_function;
use function unlink;
use function rename;
use function pcntl_exec;

class UpdateRestartWaitTask extends Task
{
    private AutoUpdater $plugin;

    private int $seconds;

    public function __construct(AutoUpdater $plugin)
    {
        $this->plugin = $plugin;
        $this->seconds = (int)$this->plugin->getConfig()->get("updateRestart-WaitSeconds", 600);
        $minutes = floor($this->seconds / 60);
        $seconds = $this->seconds - ($minutes * 60);
        $this->plugin->getServer()->broadcastMessage(new Translatable("update_wait_minutes", [$minutes, $seconds]));
    }

    public function onRun(): void
    {
        $this->seconds--;

        if (count($this->plugin->getServer()->getOnlinePlayers()) < 1) {
            register_shutdown_function(function () {
                unlink(Server::getInstance()->getDataPath() . "PocketMine-MP.phar");
                rename(Server::getInstance()->getDataPath() . "NewVersionPocketMine-MP.phar", Server::getInstance()->getDataPath(). "PocketMine-MP.phar");
                pcntl_exec("./start.sh");
            });

            $this->plugin->getLogger()->info($this->plugin->getLanguageManager()->getDefaultLang()->translateString("server_restarted"));
            $this->plugin->getServer()->shutdown();
        }

        if ($this->seconds < 1) {
            $this->plugin->getServer()->getLogger()->info($this->plugin->getLanguageManager()->getDefaultLang()->translateString("server_restarted"));
            $this->plugin->getServer()->shutdown();
        }
        elseif ($this->seconds < 5) {
            $this->plugin->getServer()->broadcastMessage(new Translatable("update_wait_seconds", [$this->seconds]));
        }
        elseif ($this->seconds % 60 === 0) {
            $this->plugin->getServer()->broadcastMessage(new Translatable("update_wait_minutes", [$this->seconds / 60]));
        }
    }
}