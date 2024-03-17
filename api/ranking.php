<?php
include_once("./constants.php");

header("Access-Control-Allow-Origin: *");
echo file_get_contents($RANKING_POSTS_FILE);
