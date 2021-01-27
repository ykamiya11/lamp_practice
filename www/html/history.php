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

//ログインユーザーの購入履歴を取得して、変数へ代入
$histories = get_history($db, $user);

//XSS対策
$histories = entity_assoc_array($histories);

//POST値取得
$order_id = get_post('order_id');

//購入履歴画面へ遷移
include_once VIEW_PATH. 'history_view.php';