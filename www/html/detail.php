<?php

require_once '../conf/const.php'; //定数関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php'; //共通関数ファイルの読み込み
require_once MODEL_PATH . 'user.php'; //ユーザーデータ用関数ファイルの読み込み
require_once MODEL_PATH . 'item.php'; //商品用関数ファイルの読みこみ
require_once MODEL_PATH . 'cart.php'; //カート用関数ファイルの読み込み
require_once MODEL_PATH . 'history_detail.php'; //購入履歴、詳細用関数ファイルの読み込み

session_start(); //セッションスタート、再開

//ログイン可否判断
if(is_logined() === false){
    //ログインしていなかった場合、login.php
    redirect_to(LOGIN_URL);
}

//データベースへ接続（sql接続）
$db = get_db_connect();

//ログインユーザー情報を取得して、変数へ代入
$user = get_login_user($db);

//POST値を取得
$order_id = get_post('order_id');

//管理者可否判断
if(is_admin($user)){
    //管理者の場合、全ての購入明細を取得な為user_idなし
    $details = get_detail($db, $order_id);
}else{
    //管理者ではない場合、ログインユーザーの購入詳細のみ取得な為user_idあり
    $details = get_detail($db, $order_id, $user['user_id']);
}

//XSS対策
$details = entity_assoc_array($details);

//購入詳細画面へ遷移
include_once VIEW_PATH. 'detail_view.php';