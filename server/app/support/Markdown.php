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
        $href = (string)($result['element']['attributes']['href'] ?? '');
        $href = $this->rewriteMarkdownLink($href);
        $result['element']['attributes']['href'] = $href;
        $host = parse_url((string)$href, PHP_URL_HOST);
        if ($host && (strpos($host, 'workerman.net') === false && strpos($host, 'popoim') === false && strpos($host, '99kf') === false)) {
            $result['element']['attributes']['rel'] = 'nofollow';
            $result['element']['attributes']['target'] = '_blank';
        }
        return $result;
    }

    /**
     * Convert only local Markdown document links to their public HTML routes.
     * Plain text, code blocks, and external .md URLs must remain unchanged.
     */
    protected function rewriteMarkdownLink($href)
    {
        $parts = parse_url($href);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return $href;
        }

        $path = $parts['path'] ?? '';
        if ($path === '' || substr($path, -3) !== '.md') {
            return $href;
        }

        return substr($path, 0, -3) . '.html' . substr($href, strlen($path));
    }

}
