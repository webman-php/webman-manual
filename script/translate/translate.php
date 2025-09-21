<?php
use Webman\Openai\Chat;
use Workerman\Worker;

require_once __DIR__ . '/../../server/vendor/autoload.php';

global $cache, $args, $zh_lang;

// 多语言并行翻译：启动若干进程，每个进程翻译一个语言（排除 zh-cn）
$langs = getTargetLanguages();

// 初始化全局开关与索引，避免未定义变量的通知
$echo_prompt = false;
$last_key_index = null;

$worker = new Worker();
$worker->count = max(1, count($langs));
$worker->onWorkerStart = function ($worker) use ($langs) {
    $index = $worker->id;
    $root = realpath(__DIR__ . '/../../resource/doc/');
    $source = $root . '/zh-cn';
    if ($index < 0 || $index >= count($langs)) {
        echo "[worker $index] no language assigned\n";
        return;
    }
    $lang = $langs[$index];
    $target = $root . '/' . $lang;

    // 校验语言参数，避免越权路径与误删
    if (!preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $lang)) {
        exit("[worker $index] invalid lang: $lang\n");
    }

    if (!is_dir($source)) {
        exit("[worker $index] $source not exists\n");
    }

    // 读取当前语言的配置，获取 prompt 中的目标中文名
    $langConfigPath = getLangConfigPath($lang);
    if (!$langConfigPath) {
        echo "[worker $index][$lang] lang config not found\n";
        return;
    }
    $json = json_decode(file_get_contents($langConfigPath), true);
    if (empty($json['zh-lang'])) {
        echo "[worker $index] miss zh-lang for $lang\n";
        return;
    }
    $GLOBALS['zh_lang'] = $json['zh-lang'];

    // 1) 删除目标目录
    if ($target === $source) {
        exit("[worker $index] target equals source, abort: $target\n");
    }
    if (is_dir($target)) {
        echo "[worker $index][$lang] delete dir: $target\n";
        deleteDirectory($target);
    }

    // 2) 从 zh-cn 整体拷贝为目标语言目录
    echo "[worker $index][$lang] copy dir: $source => $target\n";
    copyDirectory($source, $target);

    // 3) 列出目标目录的 md 文件并串行翻译
    $files = getAllFiles($target);
    $files = array_values(array_unique($files));
    $file_count = count($files);
    echo "[worker $index][$lang] total files: $file_count\n";

    $processNext = null;
    $processNext = function ($i) use (&$processNext, $files, $file_count, $index, $lang) {
        if ($i >= $file_count) {
            echo "[worker $index][$lang] all done\n";
            return;
        }
        $file = $files[$i];
        $fileContent = file_get_contents($file);
        $inputSize = strlen($fileContent);
        $startAt = microtime(true);
        $current = $i + 1;
        echo "[worker $index][$lang] [$current/$file_count] $file start size_in=$inputSize\n";
        translateTraditionalChinese($fileContent, $file, function () use (&$processNext, $i, $file_count, $index, $lang) {
            $next = $i + 1;
            $current = $next;
            echo "[worker $index][$lang] completed: $current/$file_count\n";
            $processNext($next);
        }, 0, $startAt, $inputSize, $index, $lang);
    };
    $processNext(0);
};

Worker::runAll();


function getAllFiles($dir) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $allFiles = [];
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'md') {
            $filePath = $file->getRealPath();
            $allFiles[$filePath] = $filePath;
        }
    }
    return array_values($allFiles);
}


function translateTraditionalChinese($content, $file, $done = null, $attempt = 0, $startAt = null, $inputSize = null, $index = null, $lang = null)
{
    global $cache, $zh_lang, $echo_prompt, $last_key_index;
    $api = get_config('api');
    $apikeys = get_config('apikeys');
    $key_count = count($apikeys);
    $last_key_index = $last_key_index === null ? 0 :  ++$last_key_index;
    if ($last_key_index >= $key_count) {
        $last_key_index = 0;
    }
    $apikey = $apikeys[$last_key_index];
    $chat = new Chat(['apikey' => $apikey, 'api' => $api]);
    $prompt = "你是资深技术文档本地化专家。请将以下 Webman（基于 Workerman 的高性能 PHP 框架）官方文档精准翻译为{$zh_lang}，并严格遵循：\n"
        . "1) 忠实准确、通顺自然，不增删含义，不编造内容；尽量与原文段落一一对应，不合并不拆分，不改变空行与缩进。\n"
        . "2) 完整保留 Markdown 结构与格式：标题层级、列表编号、表格、链接与锚点、图片、引用、分隔线、脚注均保持不变。\n"
        . "3) 代码块、命令行、配置与输出不翻译，仅翻译其中的自然语言注释；内联代码与反引号内容保持原样。\n"
        . "4) 变量名、占位符、路径、类名、函数名、接口名、配置键、ENV/INI/YAML/JSON 字段名、HTTP 方法与状态码、URL、端口、IP、正则与转义字符全部保留原文。\n"
        . "5) 术语统一：若有术语表请严格遵循；无术语表时使用业界常见译法；不确定时保留英文原文。\n"
        . "6) 排版规范：遵循目标语言的本地化书写习惯（空格、标点、大小写、数字与单位等）；若目标语言为中文，则遵循中文排版规范：中英文与数字之间留空格，使用全角标点，单位与数字之间留空格，避免生硬直译。\n"
        . "7) YAML Front Matter 如存在，仅在必要时翻译可读值，键名与语法不得更改。\n"
        . "8) ASCII 图、Mermaid/PlantUML、LaTeX 公式、目录树等结构内容不要改动；只翻译其外部说明文字。\n"
        . "9) 若原文包含强调（注意/警告/提示），请用等效 Markdown 表达保留。\n"
        . "输出要求：仅返回完整的 Markdown 文档，不要添加任何额外说明或前后缀。";
    if (!$echo_prompt) echo "\nPrompt $prompt\n\n";
    $echo_prompt = true;
    $chat->completions(
        [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $content]
            ],
        ], [
        'complete' => function($result, $response) use ($file, $content, $apikey, $done, $attempt, $startAt, $inputSize, $index, $lang) {
            $failAndMaybeRetry = function($reason) use ($file, $content, $done, $attempt, $startAt, $inputSize, $index, $lang) {
                if ($attempt < 1) {
                    echo "[worker $index][$lang] ", $file, " retry due to: ", $reason, "\n";
                    translateTraditionalChinese($content, $file, $done, $attempt + 1, $startAt, $inputSize, $index, $lang);
                    return;
                }
                $elapsed = $startAt ? round(microtime(true) - $startAt, 3) : 0;
                $in = $inputSize === null ? strlen($content) : $inputSize;
                echo "[worker $index][$lang] ", $file, " failed (kept original): ", $reason, " size_in=", $in, " size_out=", $in, " elapsed=", $elapsed, "s\n";
                if (is_callable($done)) {
                    $done();
                }
            };

            if (isset($result['error'])) {
                var_export($result);
                echo "\n[worker $index][$lang] apiley: $apikey\n";
                $failAndMaybeRetry('error');
                return;
            }

            $choice = $result['choices'][0] ?? null;
            $stop_reason = $choice['finish_reason'] ?? null;
            if ($stop_reason !== 'stop') {
                $failAndMaybeRetry($stop_reason ?: 'unknown');
                return;
            }

            $translate_content = ($choice['message']['content'] ?? '') . "\n";
            if ($translate_content === "\n" || $translate_content === '') {
                $failAndMaybeRetry('empty');
                return;
            }

            $outSize = strlen($translate_content);
            $elapsed = $startAt ? round(microtime(true) - $startAt, 3) : 0;
            echo "[worker $index][$lang] ", $file, " ", $stop_reason, " size_in=", ($inputSize === null ? strlen($content) : $inputSize), " size_out=", $outSize, " elapsed=", $elapsed, "s\n\n";
            file_put_contents($file, $translate_content);
            if (is_callable($done)) {
                $done();
            }
        },
    ]);
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            @chmod($path, 0777);
            unlink($path);
        }
    }
    return rmdir($dir);
}

function copyDirectory($src, $dst)
{
    $src = rtrim($src, DIRECTORY_SEPARATOR);
    $dst = rtrim($dst, DIRECTORY_SEPARATOR);
    if (!is_dir($dst)) {
        mkdir($dst, 0777, true);
    }
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $srcPath = $src . DIRECTORY_SEPARATOR . $item;
        $dstPath = $dst . DIRECTORY_SEPARATOR . $item;
        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

function get_config($name = null)
{
    $config = include __DIR__ . '/config.php';

    if ($name) {
        return $config[$name] ?? null;
    }

    return $config;
}

function getTargetLanguages()
{
    $langs = [];
    // 1) 从 resource 根扫描 *.json
    $resourceDir = realpath(__DIR__ . '/../../resource');
    if ($resourceDir) {
        foreach (scandir($resourceDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (substr($file, -5) === '.json') {
                $lang = substr($file, 0, -5);
                if ($lang !== 'zh-cn' && preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $lang)) {
                    $langs[$lang] = true;
                }
            }
        }
    }
    // 2) 从 resource/doc 扫描 *.json（有些项目结构放这里）
    $resourceDocDir = realpath(__DIR__ . '/../../resource/doc');
    if ($resourceDocDir) {
        foreach (scandir($resourceDocDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (substr($file, -5) === '.json') {
                $lang = substr($file, 0, -5);
                if ($lang !== 'zh-cn' && preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/i', $lang)) {
                    $langs[$lang] = true;
                }
            }
        }
    }
    $langs = array_keys($langs);
    sort($langs);
    return $langs;
}

function getLangConfigPath($lang)
{
    $path1 = __DIR__ . "/../../resource/doc/$lang.json";
    if (is_file($path1)) {
        return $path1;
    }
    $path2 = __DIR__ . "/../../resource/$lang.json";
    if (is_file($path2)) {
        return $path2;
    }
    return null;
}


