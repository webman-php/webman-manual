<?php
namespace app\support;

class Markdown extends \Parsedown
{
    protected function inlineUrl($Excerpt)
    {
        return $this->myParseUrl(parent::inlineUrl($Excerpt));
    }

    protected function inlineLink($Excerpt)
    {
        return $this->myParseUrl(parent::inlineLink($Excerpt));
    }

    protected function myParseUrl($result)
    {
        if (!is_array($result)) {
            return $result;
        }
        $href = $result['element']['attributes']['href'];
        $host = parse_url((string)$href, PHP_URL_HOST);
        if ($host && (strpos($host, 'workerman.net') === false && strpos($host, 'popoim') === false && strpos($host, '99kf') === false)) {
            $result['element']['attributes']['rel'] = 'nofollow';
            $result['element']['attributes']['target'] = '_blank';
        }
        return $result;
    }

}