<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\commands;

use CortexPE\Commando\BaseCommand;
use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class CheckUpdateCommand extends BaseCommand
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, "checkupdate", "Checking PocketMine Update", []);
    }

    protected function prepare(): void
    {
        $this->setPermission("outiserver.checkupdate.command");
        $this->setUsage("/checkupdate");
        $this->setDescription(new Translatable("check_update_command_description"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(AutoUpdater::getInstance()->getLanguageManager()->getDefaultLang()->translateString("check_update_command_success_message"));
        AutoUpdater::getInstance()->getServer()->getUpdater()->doCheck();
    }
}