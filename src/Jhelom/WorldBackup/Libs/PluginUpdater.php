<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;

use Exception;
use pocketmine\plugin\Plugin;


/**
 * Class PluginUpdater
 */
class PluginUpdater
{
    private const LAST_CHECK_DATE = 'last_check_date';
    private const DOWNLOAD_VERSION = 'download_version';

    /** @var Plugin */
    private $plugin;

    /** @var string */
    private $downloadDirectory = '';

    /** @var array */
    private $settings = [
        self::LAST_CHECK_DATE => '2000-01-01',
        self::DOWNLOAD_VERSION => '',
    ];

    /** @var array */
    private $messages = [
        'eng' => [
            'check' => '{0} check update.',
            'outdated' => '{0} < {1} new version found.',
            'latest' => '{0} is latest.',
            'download_start' => '{0} downloading.',
            'download_end' => '{0} downloaded.',
            'deleted' => '{0} deleted.',
            'updated' => '{0} updated.',
        ],
        'jpn' => [
            'check' => '{0} のアップデートを確認します。',
            'outdated' => '{0} より新しい {1} が見つかりました。',
            'latest' => '{0} は最新です。',
            'download_start' => '{0} のダウンロードを開始します。',
            'download_end' => '{0} のダウンロードが完了しました。',
            'deleted' => '{0} を削除しました。',
            'updated' => '{0} に更新しました。'
        ]
    ];

    private $urlDomain;
    private $urlPath;

    /**
     * PluginUpdater constructor.
     * @param Plugin $plugin
     * @param string $url
     */
    public function __construct(Plugin $plugin, string $url)
    {
        $this->plugin = $plugin;
        $u = parse_url($url);
        $this->urlDomain = $u['scheme'] . '://' . $u['host'];
        $this->urlPath = $u['path'];
        $this->plugin->getLogger()->debug($this->urlDomain);
        $this->plugin->getLogger()->debug($this->urlPath);
    }

    public function update(): void
    {
        try {
            $this->downloadDirectory = $this->plugin->getDataFolder() . 'updater';

            if (!is_dir($this->downloadDirectory)) {
                mkdir($this->downloadDirectory, 755, true);
            }

            $this->loadSettings();
            $now = date('Y-m-d');

            if ($this->settings[self::LAST_CHECK_DATE] === $now) {
                return;
            }

            $currentFile = str_replace(' ', '_', $this->plugin->getDescription()->getFullName()) . '.phar';
            $this->plugin->getLogger()->info($this->getMessage('check', $currentFile));

            $this->settings[self::LAST_CHECK_DATE] = $now;
            $html = $this->getHtml();
            $result = $this->parseHtml($html);

            if (is_null($result)) {
                $this->plugin->getLogger()->info($this->getMessage('latest', $currentFile));
                return;
            }

            $downloadUrl = $this->urlDomain . $result;
            $downloadFile = $this->parseFilename($downloadUrl);

            if (is_null($downloadFile)) {
                $this->plugin->getLogger()->info($this->getMessage('latest', $currentFile));
                return;
            }

            $currentVersion = $this->parseVersion($currentFile);
            $downloadVersion = $this->parseVersion($downloadFile);

            $this->plugin->getLogger()->debug(StringFormat::format('current version  = {0}', implode('.', $currentVersion)));
            $this->plugin->getLogger()->debug(StringFormat::format('download version = {0}', implode('.', $downloadVersion)));

            if ($this->isOutdated($currentVersion, $downloadVersion)) {
                $this->downloadNewVersion($currentFile, $downloadFile, $downloadUrl);
            } else {
                $this->plugin->getLogger()->info($this->getMessage('latest', $currentFile));
            }

            $this->saveSettings();
        } catch (Exception $e) {
            $this->plugin->getLogger()->logException($e);
        }
    }

    private function loadSettings(): void
    {
        $path = $this->getSettingsFilePath();
        $this->settings = JsonFile::load($path);
    }

    /**
     * @return string
     */
    private function getSettingsFilePath(): string
    {
        return $this->downloadDirectory . DIRECTORY_SEPARATOR . 'updater.json';
    }

    /**
     * @param string $key
     * @param mixed ...$args
     * @return string
     */
    private function getMessage(string $key, ... $args): string
    {
        $lang = $this->plugin->getServer()->getLanguage()->getLang();

        if (!array_key_exists($lang, $this->messages)) {
            $lang = 'eng';
        }

        if (!array_key_exists($key, $this->messages[$lang])) {
            return $key . ': ' . join(', ', $args);
        }

        $msg = $this->messages[$lang][$key];

        return StringFormat::formatEx($msg, $args);
    }

    /**
     * @return string
     */
    private function getHtml(): string
    {
        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, $this->urlDomain . $this->urlPath);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            return curl_exec($ch);
        } finally {
            curl_close($ch);
        }
    }

    /**
     * @param string $html
     * @return null|string
     */
    private function parseHtml(string $html): ?string
    {
        $pattern = '/<a href="(.*?\.phar)"/';

        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param string $downloadUrl
     * @return null|string
     */
    private function parseFilename(string $downloadUrl): ?string
    {
        $list = explode('/', $downloadUrl);
        $name = array_pop($list);

        if (is_null($name)) {
            return null;
        }

        return $name;
    }

    /**
     * @param string $filename
     * @return int[]
     */
    private function parseVersion(string $filename): array
    {
        if (preg_match('/_v([0-9\.]+[0-9]+)/', $filename, $matches)) {
            $ss = explode('.', $matches[1]);
            $numbers = [];

            foreach ($ss as $s) {
                $numbers[] = intval($s);
            }

            return $numbers;
        } else {
            return [];
        }
    }

    /**
     * @param int[] $currentVersion
     * @param int[] $downloadVersion
     * @return bool
     */
    private function isOutdated(array $currentVersion, array $downloadVersion): bool
    {
        $max = max(count($currentVersion), count($downloadVersion));

        for ($i = 0; $i < $max; $i++) {
            $currentVersion[] = 0;
            $downloadVersion[] = 0;
        }

        foreach ($currentVersion as $cur) {
            $tar = array_shift($downloadVersion);

            if ($cur < $tar) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $currentFile
     * @param string $downloadFile
     * @param string $downloadUrl
     */
    private function downloadNewVersion(string $currentFile, string $downloadFile, string $downloadUrl): void
    {
        $this->plugin->getLogger()->info($this->getMessage('outdated', $currentFile, $downloadFile));
        $save_path = $this->downloadDirectory . DIRECTORY_SEPARATOR . $downloadFile;
        $this->plugin->getLogger()->info($this->getMessage('download_start', $downloadFile));
        $this->download($downloadUrl, $save_path);
        $this->plugin->getLogger()->info($this->getMessage('download_end', $downloadFile));

        $old_plugin_path = $this->plugin->getServer()->getDataPath() . 'plugins' . DIRECTORY_SEPARATOR . $currentFile;
        $new_plugin_path = $this->plugin->getServer()->getDataPath() . 'plugins' . DIRECTORY_SEPARATOR . $downloadFile;

        if (is_file($old_plugin_path)) {
            unlink($old_plugin_path);
            $this->plugin->getLogger()->info($this->getMessage('deleted', $currentFile));
        }

        rename($save_path, $new_plugin_path);
        $this->plugin->getLogger()->info($this->getMessage('updated', $downloadFile));

        $this->settings[self::DOWNLOAD_VERSION] = $downloadFile;
    }

    /**
     * @param string $downloadUrl
     * @param string $savePath
     */
    private function download(string $downloadUrl, string $savePath): void
    {
        $fp = fopen($savePath, 'w');

        try {
            $ch = curl_init($downloadUrl);

            try {
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);
            } finally {
                curl_close($ch);
            }
        } finally {
            fclose($fp);
        }
    }

    private function saveSettings(): void
    {
        $path = $this->getSettingsFilePath();
        JsonFile::save($path, $this->settings);
    }
}

