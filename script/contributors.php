<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

$opts = array(
    'http'=>array(
        'header'=>"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.80 Safari/537.36\r\n"
    )
);
$context = stream_context_create($opts);

$webman_contributors = file_get_contents('https://api.github.com/repos/walkor/webman/contributors', false, $context);
if (!$webman_contributors) die;
$webman_contributors = json_decode($webman_contributors, true);
if (!$webman_contributors) die;

$manual_contributors = file_get_contents('https://api.github.com/repos/webman-php/webman-manual/contributors', false, $context);
if (!$manual_contributors) die;
$manual_contributors = json_decode($manual_contributors, true);
if (!$manual_contributors) die;

$contributors = [];
foreach ($webman_contributors as $contributor) {
    if ($contributor['login'] == 'walkor') continue;
    $contributors[$contributor['id']] = [
        'name' => $contributor['login'],
        'avatar' => $contributor['avatar_url'],
        'contributions' => $contributor['contributions']
    ];
}

foreach ($manual_contributors as $contributor) {
    if ($contributor['login'] == 'walkor') continue;
    $id = $contributor['id'];
    if (!isset($contributors[$id])) {
        $contributors[$id] = [
            'name' => $contributor['login'],
            'avatar' => $contributor['avatar_url'],
            'contributions' => 0
        ];
    }
    $contributors[$id]['contributions'] += $contributor['contributions'];
}

$sort = array_column($contributors, 'contributions');
array_multisort($sort, SORT_DESC, SORT_NUMERIC, $contributors);

$contributors_chunk = array_chunk($contributors, 8);

$table = '<table>';
foreach ($contributors_chunk as $contributors) {
    $table .= '
  <tr>';
    foreach ($contributors as $contributor) {
        $table .='
    <td align="center">
      <a href="https://github.com/'.$contributor['name'].'">
        <img src="'.$contributor['avatar'].'" width="100px;"/><h5>'.$contributor['name'].'</h5>
      </a>
    </td>';
    }
    $table .= '
  </tr>';
}
$table .= '
</table>';

$zh_cn_thanks = '#感谢以下贡献者对webman及文档的贡献';
$en_thanks = '#Thank the following contributors for their contributions to webman and the document';

$dir = __DIR__ . '/../resource/doc';

// 遍历dir下所有的thanks.md文件
foreach (glob("$dir/*/thanks.md") as $path) {
    $content = file_get_contents($path);
    // 将文件内容的table部分替换
    $content = preg_replace('/<table>[\s\S]*?<\/table>/s', $table, $content);
    file_put_contents($path, $content);
}
