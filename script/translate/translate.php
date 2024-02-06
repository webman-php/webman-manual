<?php
use Webman\Openai\Chat;
use Workerman\Timer;
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
    $time = 1;
    $file_count = count($files);
    foreach ($files as $file_index => $file) {
        $fileContent = file_get_contents($file);
        $contents = splitMarkdownByHeadings($fileContent);
        $count = count($contents);
        foreach ($contents as $index => $content) {
            Timer::add($time, function () use ($content, $file, $index, $count, $file_index, $file_count) {
                echo "$file $index $count start\n";
                translateTraditionalChinese($content, $file, $index, $count);
                if ($file_index === $file_count - 1) {
                    Timer::add(60, function () {
                        echo "last file\n";
                    }, null, false);
                }
            }, null, false);
            $time += 20;
        }
    }
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


function translateTraditionalChinese($content, $file, $index, $count)
{
    global $cache, $zh_lang, $echo_prompt, $last_key_index;
    $cache = $cache === null ? [] : $cache;
    $api = get_config('api');
    $apikeys = get_config('apikeys');
    $key_count = count($apikeys);
    $last_key_index = $last_key_index === null ? 0 :  ++$last_key_index;
    if ($last_key_index >= $key_count) {
        $last_key_index = 0;
    }
    $apikey = $apikeys[$last_key_index];
    $chat = new Chat(['apikey' => $apikey, 'api' => $api]);
    $prompt = "webman是一个基于workerman开的的高性能PHP框架，以下是webman的文档，请作为文档翻译器，将我发送的文档翻译成{$zh_lang}，要逐句翻译，不要遗漏任何细节，请确保翻译准确通顺。";
    if (!$echo_prompt) echo "\nPrompt $prompt\n\n";
    $echo_prompt = true;
    $chat->completions(
        [
            'model' => 'gpt-3.5-turbo-1106',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $content]
            ],
        ], [
        'complete' => function($result, $response) use ($file, $index, $count) {
            global $cache;
            if (isset($result['error'])) {
                var_export($result);
                return;
            }
            $content = $result['choices'][0]['message']['content'] . "\n";
            echo $file," $index ",$result['choices'][0]['finish_reason'],"\n\n";
            $cache[$file][$index] = $content;
            ksort($cache[$file]);
            // 所有文档片段收集完毕
            if ($count === count($cache[$file])) {
                file_put_contents($file, implode('', $cache[$file]));
                unset($cache[$file]);
            }
        },
    ]);
}

function splitMarkdownByHeadings($markdown) {
    // 使用正则表达式匹配一级级标题
    $pattern = '/^##\s+(.*)$/m';
    preg_match_all($pattern, $markdown, $matches);
    // 将匹配到的标题作为分割点，将文档分割成多个段落
    $sections = [];
    $start = 0;
    foreach ($matches[0] as $match) {
        $end = strpos($markdown, $match, $start);
        $section = substr($markdown, $start, $end - $start);
        $sections[] = $section;
        $start = $end;
    }
    // 添加最后一个标题后的内容作为最后一个段落
    $sections[] = substr($markdown, $start);
    $data = [''];
    foreach ($sections as $section) {
        $index = count($data) - 1;
        $tmp = $data[$index] . $section;
        if (strlen($tmp) < 9000) {
            $data[$index] = $tmp;
        } else {
            $data[] = $section;
        }
    }
    return $data;
}

function get_config($name = null)
{
    $config = include __DIR__ . '/config.php';

    if ($name) {
        return $config[$name] ?? null;
    }

    return $config;
}


