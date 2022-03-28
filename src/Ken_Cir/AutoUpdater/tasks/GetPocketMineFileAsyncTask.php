<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\tasks;

use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\lang\Translatable;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use function file_put_contents;
use function count;
use function register_shutdown_function;
use function unlink;
use function rename;
use function pcntl_exec;

class GetPocketMineFileAsyncTask extends AsyncTask
{
    private string $downloadURL;

    private string $dataPath;

    public function __construct(string $downloadURL, string $dataPath)
    {
        $this->downloadURL = $downloadURL;
        $this->dataPath = $dataPath;
    }

    public function onRun(): void
    {
        $result = Internet::getURL($this->downloadURL);
        if ($result->getCode() !== 200) {
            $this->setResult($result->getCode());
            return;
        }
        file_put_contents("{$this->dataPath}NewVersionPocketMine-MP.phar", $result->getBody());
        $this->setResult(200);
    }

    public function onCompletion(): void
    {
        if ($this->getResult() !== 200) {
            AutoUpdater::getInstance()->getLogger()->error("PocketMineの自動アップデートに失敗しました GitHub StatusCode: {$this->getResult()}");
        }
        else {
            AutoUpdater::getInstance()->getLogger()->info("新バージョンのPocketMine.pharファイルをダウンロードしました");
            AutoUpdater::getInstance()->getLogger()->info("アップデートの準備が整いました！");

            // もし、サーバーにプレイヤーがいないなら
            if (count(Server::getInstance()->getOnlinePlayers()) < 1) {
                register_shutdown_function(function () {
                    unlink(Server::getInstance()->getDataPath() . "PocketMine-MP.phar");
                    rename(Server::getInstance()->getDataPath() . "NewVersionPocketMine-MP.phar", Server::getInstance()->getDataPath() . "PocketMine-MP.phar");
                    pcntl_exec("./start.sh");
                });

                AutoUpdater::getInstance()->getLogger()->info("サーバーを再起動しています...");
                Server::getInstance()->shutdown();
            }
            else {
                AutoUpdater::getInstance()->getScheduler()->scheduleRepeatingTask(new UpdateRestartWaitTask(AutoUpdater::getInstance()), 20);
            }
        }
    }
}