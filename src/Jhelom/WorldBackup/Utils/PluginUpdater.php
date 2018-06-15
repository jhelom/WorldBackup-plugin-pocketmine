<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Utils;

use Exception;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

/**
 * Class PluginUpdater
 * @package Jhelom\WorldBackup
 */
class PluginUpdater
{
    private const LAST_CHECK_DATE = 'last_check_date';
    private const DOWNLOAD_VERSION = 'download_version';

    /** @var PluginBase */
    private $plugin;

    /** @var string */
    private $urlDomain;

    /** @var string */
    private $urlPath;

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

    /**
     * PluginUpdater constructor.
     * @param PluginBase $plugin
     * @param string $domain
     * @param string $path
     */
    public function __construct(PluginBase $plugin, string $domain, string $path)
    {
        $this->plugin = $plugin;
        $this->urlDomain = $domain;
        $this->urlPath = $path;
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

            $currentVersion = str_replace(' ', '_', $this->plugin->getDescription()->getFullName()) . '.phar';
            $this->info($this->getMessage('check', $currentVersion));

            $this->settings[self::LAST_CHECK_DATE] = $now;
            $html = $this->getHtml();
            $result = $this->parseHtml($html);

            if (is_null($result)) {
                return;
            }

            $downloadUrl = $this->urlDomain . $result;
            $downloadVersion = $this->parseFilename($downloadUrl);

            if (is_null($downloadVersion)) {
                return;
            }

            if ($currentVersion == $downloadVersion) {
                $this->info($this->getMessage('latest', $currentVersion));
            } else {
                $this->info($this->getMessage('outdated', $currentVersion, $downloadVersion));
                $save_path = $this->downloadDirectory . DIRECTORY_SEPARATOR . $downloadVersion;

                $this->info($this->getMessage('download_start', $downloadVersion));
                $this->download($downloadUrl, $save_path);
                $this->info($this->getMessage('download_end', $downloadVersion));

                $old_plugin_path = $this->plugin->getServer()->getDataPath() . 'plugins' . DIRECTORY_SEPARATOR . $currentVersion;
                $new_plugin_path = $this->plugin->getServer()->getDataPath() . 'plugins' . DIRECTORY_SEPARATOR . $downloadVersion;

                if (is_file($old_plugin_path)) {
                    unlink($old_plugin_path);
                    $this->info($this->getMessage('deleted', $currentVersion));
                }

                rename($save_path, $new_plugin_path);
                $this->info($this->getMessage('updated', $downloadVersion));

                $this->settings[self::DOWNLOAD_VERSION] = $downloadVersion;
            }

            $this->saveSettings();
        } catch (Exception $e) {
            $this->plugin->getLogger()->logException($e);
        }
    }

    private function loadSettings(): void
    {
        $filename = $this->getSettingsFilename();

        if (is_file($filename)) {
            $json = file_get_contents($filename);
            $this->settings = json_decode($json, true);
        }
    }

    /**
     * @return string
     */
    private function getSettingsFilename(): string
    {
        return $this->downloadDirectory . DIRECTORY_SEPARATOR . 'updater.json';
    }

    /**
     * @param string $message
     */
    private function info(string $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::GREEN . $message);
    }

    /**
     * @param string $key
     * @param string ...$args
     * @return string
     */
    private function getMessage(string $key, string... $args): string
    {
        $lang = $this->plugin->getServer()->getLanguage()->getLang();

        if (!array_key_exists($lang, $this->messages)) {
            $lang = 'eng';
        }

        if (!array_key_exists($key, $this->messages[$lang])) {
            return $key . ': ' . join(', ', $args);
        }

        $msg = $this->messages[$lang][$key];

        foreach ($args as $index => $value) {
            $search = '{' . $index . '}';
            $msg = str_replace($search, $value, $msg);
        }

        return $msg;
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
        $filename = $this->getSettingsFilename();
        $json = json_encode($this->settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($filename, $json);
    }
}

