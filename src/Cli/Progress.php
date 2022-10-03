<?php

namespace HongXunPan\Tools\Cli;

class Progress
{
    private static $phpMode;

    public function __construct()
    {
        self::setPhpMode();
    }

    private static function setPhpMode()
    {
        if (!isset(self::$phpMode) && self::$phpMode === null) {
            self::$phpMode = php_sapi_name();
        }
    }

    private static function checkPhpMode()
    {
        self::setPhpMode();
        if (self::$phpMode !== 'cli') {
            echo 'only in cli mode' . PHP_EOL;
            die();
        }
    }

    public static function input($tips)
    {
        self::checkPhpMode();
        echo "\33[1mpls input $tips ";
        $input = fgets(STDIN);
        echo PHP_EOL . "get $tips = " . $input . PHP_EOL;
        return trim($input);
    }

    public static function echoCliProgress($current, $total, $showDetail = true, $showPercent = true, $detailUnit = '')
    {
        self::checkPhpMode();
        static $lastTime = 0;
        $now = microtime(true);
        if ($current == 0) {
            $percent = 0;
        } else {
            $percent = (int)ceil($current / $total * 100);
        }
        if ($lastTime != 0 && $now - $lastTime < 0.1 && $percent < 90) {
            return;
        }
        $lastTime = $now;
        $process = "[";
        $process .= str_repeat('#', $percent);
        $process .= str_repeat(' ', 100 - $percent);
        $process .= "]";
        if ($showDetail) {
            $process .= " [$current/$total$detailUnit]";
        }
        if ($showPercent) {
            if ($percent == 0) {
                $percent = "0%";
            } else {
                $percent = number_format($current / $total * 100, 2) . "%";
            }
            $process .= " [$percent]";
        }
        echo "\033[?25l";//隐藏光标
        echo $process . "\r";

    }

    /** @deprecated some bug in docker or MacOs
     * @noinspection DuplicatedCode
     */
    public static function echoCliProgressOld($current, $total, $showDetail = true, $showPercent = true)
    {
        self::checkPhpMode();
        static $lastTime = 0;
        $now = microtime(true);
        $percent = (int)ceil($current / $total * 100);
        if ($lastTime != 0 && $now - $lastTime < 0.1 && $percent < 90) {
            return;
        }
        $lastTime = $now;
        echo "\033[?25l";//隐藏光标
        $process = "[";
        $process .= str_repeat('#', $percent);
        $process .= str_repeat(' ', 100 - $percent);
        $process .= "]";
        if ($showDetail) {
            $process .= " [$current/$total]";
        }
        if ($showPercent) {
            $percent = number_format($current / $total * 100, 2) . "%";
            $process .= " [$percent]";
        }
        echo "\33[s"; //保存光标位置
        echo $process;
        echo "\33[K"; //清除光标之后的内容
        echo "\33[u"; //恢复光标位置
//        echo "\33[?25h"; //显示光标
    }

    public static function endCliProgress()
    {
        echo PHP_EOL;
        echo PHP_EOL;
        echo "\33[?25h"; //显示光标
        echo "\33[0m";
    }

    private static function getFilesize($bytes, $format)
    {
        switch ($format) {
            case "kb":
            case 'KB':
            case 'k':
            case 'K':
                $p = 1;
                break;
            case 'mb':
            case 'M':
            case 'm':
            case "MB":
                $p = 2;
                break;
            case 'gb':
            case 'g':
            case 'G':
            case "GB":
                $p = 3;
                break;
            case '':
                $p = 0;
                break;
            default:
                return -1;
        }
        return number_format($bytes / pow(1024, $p), 3, '.', '');
    }

    public static function downloadProgress($ch, $countDownloadSize, $currentDownloadSize, $countUploadSize, $currentUploadSize, $unit = '')
    {
        $countDownloadSize = self::getFilesize($countDownloadSize, $unit);
        $currentDownloadSize = self::getFilesize($currentDownloadSize, $unit);
        if ($countDownloadSize == -1 || $currentDownloadSize == -1) {
            if (self::$phpMode == 'cli') {
                echo 'unit: ' . $unit . ' does not support';
            }
            return false;
        }
        self::echoCliProgress($currentDownloadSize, $countDownloadSize, true, true, $unit);
        return true;
    }

    public function curlDownloadProgress($url, $savePath, $saveName = '', $unit = 'kb', $isShowProgress = true)
    {
        if (!is_dir($savePath)) {
            if (self::$phpMode == 'cli') {
                echo 'path: ' . $savePath . ' dir does not exist';
            }
            return false;
        }
        if ($saveName == '') {
            $urlFileName = (false === $pos = strrpos($url, '/')) ? '' : substr($url, $pos);
            $urlFileName = ltrim($urlFileName, '/');
            $saveName = $urlFileName;
        }
        $saveFile = $savePath . $saveName;
        if (file_exists($saveFile)) {
            //文件已存在
            $saveFile = $savePath . date('YmdHis') . '-' . md5(microtime()) . $saveName;
        }

        $fp = fopen($saveFile, 'wb'); // 本地文件保存路径
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOPROGRESS, !$isShowProgress);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $downloadSize, $downloaded, $uploadSize, $uploaded) use ($unit) {
            self::downloadProgress($resource, $downloadSize, $downloaded, $uploadSize, $uploaded, $unit);
        });

        echo "Downloading \033[1;31m" . $url . "\033[0m to \033[1;32m$saveFile\033[0m \n";
        $response = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        self::endCliProgress();
        return $saveFile;
    }
}
