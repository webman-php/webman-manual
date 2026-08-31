<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/functions.php';

use app\controller\Doc;

$source = <<<'MARKDOWN'
普通文本 SKILL.md

行内代码 `SKILL.md`

```text
SKILL.md
```

[相对链接](guide.md)
[带锚点](guide.md#section)
[带查询参数](guide.md?mode=full#section)
[站内绝对路径](/doc/zh-cn/guide.md)
[查询参数中的文件名](search?file=README.md)
[页内锚点中的文件名](#README.md)
[外部 Markdown](https://github.com/webman-php/skills/blob/main/README.md)
[协议相对外链](//github.com/webman-php/skills/blob/main/README.md)
MARKDOWN;

$method = new ReflectionMethod(Doc::class, 'formatContent');
$method->setAccessible(true);
$html = $method->invoke(new Doc(), $source);
$nestedHtml = $method->invoke(new Doc(), '[嵌套链接](guide.md)', '../');

$expectations = [
    'ordinary text keeps SKILL.md' => strpos($html, '普通文本 SKILL.md') !== false,
    'inline code keeps SKILL.md' => strpos($html, '<code>SKILL.md</code>') !== false,
    'code block keeps SKILL.md' => strpos($html, '<pre><code class="language-text">SKILL.md</code></pre>') !== false,
    'ordinary text is not rewritten' => strpos($html, 'SKILL.html') === false,
    'relative Markdown link is rewritten' => strpos($html, 'href="guide.html"') !== false,
    'anchor is preserved' => strpos($html, 'href="guide.html#section"') !== false,
    'query and anchor are preserved' => strpos($html, 'href="guide.html?mode=full#section"') !== false,
    'root-relative Markdown link is rewritten' => strpos($html, 'href="/doc/zh-cn/guide.html"') !== false,
    'query value is not rewritten' => strpos($html, 'href="search?file=README.md"') !== false,
    'fragment is not rewritten' => strpos($html, 'href="#README.md"') !== false,
    'external Markdown URL is preserved' => strpos($html, 'href="https://github.com/webman-php/skills/blob/main/README.md"') !== false,
    'protocol-relative Markdown URL is preserved' => strpos($html, 'href="//github.com/webman-php/skills/blob/main/README.md"') !== false,
    'sidebar path prefix is preserved' => strpos($nestedHtml, 'href="../guide.html"') !== false,
];

foreach ($expectations as $message => $passed) {
    if (!$passed) {
        fwrite(STDERR, "[FAIL] $message\n\n$html\n");
        exit(1);
    }
}

$skillPages = glob(dirname(__DIR__, 2) . '/resource/doc/*/ai/skill.md');
foreach ($skillPages as $skillPage) {
    $skillHtml = $method->invoke(new Doc(), file_get_contents($skillPage));
    if (strpos($skillHtml, 'SKILL.html') !== false || strpos($skillHtml, 'SKILL.md') === false) {
        fwrite(STDERR, "[FAIL] Skill filename changed while rendering $skillPage\n");
        exit(1);
    }
}

echo 'Markdown link tests passed for ' . count($skillPages) . " localized Skill pages.\n";
