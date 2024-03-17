<?php
/** 人気記事のパスとサイトマップデータから人気記事の記事情報を取得する */
function get_ranking_posts(
    string $ranking_paths_file,
    string $sitemap_file,
)
{
    $ranking = json_decode(file_get_contents($ranking_paths_file));
    $sitemap = json_decode(file_get_contents($sitemap_file));

    $posts = [];
    foreach ($ranking->paths as $path) {
        $posts[] = $sitemap->{$path};
    }

    $ret = [
        'datetime' => $ranking->datetime,
        'posts' => $posts
    ];
    print_r($ret);
    return $ret;
}
