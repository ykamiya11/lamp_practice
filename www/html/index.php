<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

//トークン生成
$token = get_csrf_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();
//ログインユーザー情報を取得して、変数へ代入
$user = get_login_user($db);

//商品一覧情報の取得
$items = get_open_items($db);

//ランキング情報を取得して、変数へ代入
$ranking = get_ranking($db);

//XSS対策
$items = entity_assoc_array($items);
$ranking = entity_assoc_array($ranking);


include_once VIEW_PATH . 'index_view.php';