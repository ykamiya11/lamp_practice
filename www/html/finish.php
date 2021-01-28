<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
//トークンの照合
if(is_valid_csrf_token(get_post('csrf_token'))){
//ログイン可否判断
if(is_logined() === false){
  //ログインしていない場合、login.php
  redirect_to(LOGIN_URL);
}
//データベースへ接続
$db = get_db_connect();
//ログインしているユーザー情報を取得して、変数へ代入
$user = get_login_user($db);
//ログインユーザーのカート情報を取得して、変数へ代入
$carts = get_user_carts($db, $user['user_id']);


if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
}

//XSS対策
$carts = entity_assoc_array($carts);

$total_price = sum_carts($carts);

include_once '../view/finish_view.php';
}

//メッセージを設定
set_error('不正なアクセスです。');
//購入完了ページへ遷移
include_once '../view/finish_view.php';