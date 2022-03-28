<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\tasks;

use Ken_Cir\AutoUpdater\AutoUpdater;
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
        $this->plugin->getServer()->broadcastMessage("§e[警告] §fアップデートを行うため、{$minutes}分{$seconds}後サーバーは再起動されます");
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

            $this->plugin->getLogger()->info("サーバーを再起動しています...");
            $this->plugin->getServer()->shutdown();
            return;
        }

        if ($this->seconds < 1) {
            register_shutdown_function(function () {
                unlink(Server::getInstance()->getDataPath() . "PocketMine-MP.phar");
                rename(Server::getInstance()->getDataPath() . "NewVersionPocketMine-MP.phar", Server::getInstance()->getDataPath(). "PocketMine-MP.phar");
                pcntl_exec("./start.sh");
            });

            $this->plugin->getServer()->getLogger()->info("サーバーを再起動しています...");
            $this->plugin->getServer()->shutdown();
        }
        elseif ($this->seconds < 5) {
            $this->plugin->getServer()->broadcastMessage("§e[警告] §fアップデートを行うため、あと{$this->seconds}秒でサーバーは再起動されます");
        }
        elseif ($this->seconds % 60 === 0) {
            $this->plugin->getServer()->broadcastMessage("§e[警告] §fアップデートを行うため、" . $this->seconds / 60 . "分後サーバーは再起動されます");
        }
    }
}