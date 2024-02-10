<?php
use app\support\Markdown;

function resource_path()
{
    return config('app.resource_path');
}

function notfound()
{
    return response(file_get_contents(public_path() . '/404.html', 404));
}

function markdown($content, $output_safe_content = true)
{
    static $markdown;
    if (!$markdown) $markdown = Markdown::instance()->setBreaksEnabled(true);
    $content = htmlspecialchars_decode($content);
    return $markdown->setMarkupEscaped($output_safe_content)->text($content);
}
