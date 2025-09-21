<?php
use Webman\Openai\Chat;
use Workerman\Worker;

global $cache, $args, $zh_lang;

if (empty($lang) || !is_dir($dir = __DIR__ . '/../../resource/doc/' . $lang)) {
    exit("$dir not exists \n");
}

$json = json_decode(file_get_contents(__DIR__ . "/../../resource/doc/$lang.json"), true);
if (empty($json['zh-lang'])) {
    exit("miss zh-lang\n");
}

$zh_lang = $json['zh-lang'];

$worker = new Worker();
$worker->onWorkerStart = function () {
    global $lang, $need_translate;
    $files = getAllFiles(realpath(__DIR__ . "/../../resource/doc/$lang/"));
    foreach ($files as $key => $file) {
        $text = file_get_contents($file);
        if (!$need_translate($text)) {
            unset($files[$key]);
        }
    }
    $files = array_values(array_unique($files));
    var_export($files);
    $file_count = count($files);
    echo "total files: $file_count\n";
    $processNext = null;
    $processNext = function ($i) use (&$processNext, $files, $file_count) {
        if ($i >= $file_count) {
            echo "all done\n";
            return;
        }
        $file = $files[$i];
        $fileContent = file_get_contents($file);
        echo "$file start\n";
        translateTraditionalChinese($fileContent, $file, function () use (&$processNext, $i, $file_count) {
            $next = $i + 1;
            echo "completed: $i/$file_count\n";
            $processNext($next);
        });
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


function translateTraditionalChinese($content, $file, $done = null)
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
        . "6) 中文排版规范：中英文与数字之间留空格，使用全角标点，单位与数字之间留空格，避免生硬直译。\n"
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
        'complete' => function($result, $response) use ($file, $content, $apikey, $done) {
            if (isset($result['error'])) {
                var_export($result);
                echo "\napiley: $apikey\n";
                if (is_callable($done)) {
                    $done();
                }
                return;
            }
            $translate_content = $result['choices'][0]['message']['content'] . "\n";
            $stop_reason = $result['choices'][0]['finish_reason'];
            if ($stop_reason === 'length') {
                $translate_content = $content;
            }
            $content_length = strlen($content);
            $stop_reason = $stop_reason === 'length' ? "[[[length $content_length]]]]" : 'stop';
            echo $file, " ", $stop_reason, "\n\n";
            file_put_contents($file, $translate_content);
            if (is_callable($done)) {
                $done();
            }
        },
    ]);
}

function get_config($name = null)
{
    $config = include __DIR__ . '/config.php';

    if ($name) {
        return $config[$name] ?? null;
    }

    return $config;
}


