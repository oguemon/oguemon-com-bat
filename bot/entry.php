<?php
include_once("./constants.php");
include_once("./get-ranking-paths.php");
include_once("./get-ranking-posts.php");

// 人気記事のパス一覧を取得してJSONに出力
file_put_contents($RANKING_PATHS_FILE, json_encode(get_ranking_paths(
    $KEY_LOCATION,
    $PROPERTY_ID,
    $GET_COUNT,
)));

// 人気記事の記事情報一覧を生成してJSONに出力
file_put_contents($RANKING_POSTS_FILE, json_encode(get_ranking_posts(
    $RANKING_PATHS_FILE,
    $SITEMAP_FILE,
), JSON_UNESCAPED_UNICODE));
