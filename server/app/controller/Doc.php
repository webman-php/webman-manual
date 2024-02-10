<?php
namespace app\controller;

use GuzzleHttp\Psr7\Response;
use support\Request;

class Doc
{
    public function index(Request $request)
    {
        return redirect('/doc/en/');
    }

    public function view(Request $request, $lang)
    {
        $uri = $request->uri();
        if (strpos($uri, '../') !== false) {
            return response('<h1>400 Bad Request</h1>', 400);
        }

        if ($uri === "/doc/$lang") {
            return redirect("/doc/$lang/");
        }

        if ($uri === "/doc/$lang/README.html") {
            return redirect("/doc/$lang/");
        }

        if ($uri === "/doc/$lang/") {
            $uri = "/doc/$lang/README.md";
            $index_title = "$lang";
        } else {
            if (pathinfo($uri, PATHINFO_EXTENSION) !== 'html') {
                return notfound();
            }
            $uri = strstr($uri, '.html', true).'.md';
        }

        $file = resource_path() . "/$uri";

        if (!is_file($file)) {
            return response('<h1>404 Not Found</h1>', 404);
        }

        [$title, $sidebar] = $this->sidebar( resource_path() . "/doc/{$lang}/SUMMARY.md", $uri);

        $content = $this->format($file);

        $info = $this->getInfo($lang);

        $repo = $info['repo'] . "/tree/master/resource$uri";

        $langs = $this->getLangs();

        $path = substr($request->uri(), strlen("/doc/$lang/"));

        return view('doc/view', [
            'html_title' => $index_title??($title ? "$title-{$info['name']}": $info['name']),
            'repo'        => $repo,
            'name'        => $info['name'],
            'sidebar'     => $sidebar,
            'content'     => $content,
            'current_lang' => $langs[$lang],
            'langs'        => $langs,
            'path'         => $path,
        ]);
    }

    protected function sidebar($sidebar_file, $uri)
    {
        $sidebar_content = file_get_contents($sidebar_file);
        $title = '';
        $relative_uri_arr = explode('/', $uri);
        if (empty($relative_uri_arr[0])) {
            unset($relative_uri_arr[0], $relative_uri_arr[1], $relative_uri_arr[2]);
        } else {
            unset($relative_uri_arr[0], $relative_uri_arr[1]);
        }

        $relative_uri = implode('/', $relative_uri_arr);
        if (preg_match('/\[(.*?)\]\('.preg_quote($relative_uri, '/').'\)/', $sidebar_content, $match)) {
            $title = $match[1];
        }
        $path = '';
        $count = substr_count($uri, '/');
        if ($count > 3) {
            for ($i=0; $i<$count-3;$i++) {
                $path .= '../';
            }
        }
        return [$title, $this->formatContent($sidebar_content, $path)];
    }

    protected function format($file, $path = '') {
        if (!is_file($file)) {
            return '';
        }
        return $this->formatContent(file_get_contents($file, $path));
    }

    protected function formatContent($content, $path = '') {
        if ($path) {
            $content = preg_replace('/\[(.*?)\]\((.*?\.md)\)/', "[$1]($path$2)", $content);
        }
        return markdown(str_replace('.md', '.html', $content), false);
    }

    public function file(Request $request)
    {
        $path = $request->path();
        if (strpos($path, '/..') !== false) {
            return notfound();
        }
        $file = resource_path()."/$path";
        if (!is_file($file)) {
            return notfound();
        }
        return \response()->withFile($file);
    }

    protected function getLangs()
    {
        $langs = [];
        foreach (glob(resource_path() . '/doc/*.json') as $file) {
            $lang = basename($file, '.json');
            $data = json_decode(file_get_contents($file), true);
            $langs[$lang] = $data['lang'];
        }
        return $langs;
    }

    protected function getInfo($lang)
    {
        return json_decode(file_get_contents(resource_path() . "/doc/$lang.json"), true);
    }

}