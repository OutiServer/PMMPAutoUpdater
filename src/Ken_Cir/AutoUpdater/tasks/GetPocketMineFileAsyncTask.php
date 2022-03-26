<?php

declare(strict_types=1);

namespace Ken_Cir\AutoUpdater\tasks;

use Ken_Cir\AutoUpdater\AutoUpdater;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use function file_put_contents;

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
    }

    public function onCompletion(): void
    {
        if ($this->getResult() !== 200) {
            AutoUpdater::getInstance()->getLogger()->error(AutoUpdater::getInstance()->getLanguageManager()->getDefaultLang()->translateString("pocketmine_file_download_failed", [$this->getResult()]));
        }
        else {
            AutoUpdater::getInstance()->getLogger()->info(AutoUpdater::getInstance()->getLanguageManager()->getDefaultLang()->translateString("pocketmine_file_download_success"));
        }
    }
}