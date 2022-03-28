<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\commands;

use CortexPE\Commando\BaseCommand;
use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class CheckUpdateCommand extends BaseCommand
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, "checkupdate", "PocketMineのアップデートを確認するTaskを実行する", []);
    }

    protected function prepare(): void
    {
        $this->setPermission("outiserver.checkupdate.command");
        $this->setUsage("/checkupdate");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(AutoUpdater::getInstance()->getMessages()->get("checkUpdateCommand.success"));
        AutoUpdater::getInstance()->getServer()->getUpdater()->doCheck();
    }
}