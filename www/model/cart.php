<?php 
require_once MODEL_PATH . 'functions.php'; //共通関数ファイルの読み込み
require_once MODEL_PATH . 'db.php'; //データベース関数ファイルの読み込み
require_once MODEL_PATH . 'history_detail.php'; //購入履歴、詳細関数ファイル読み込み

//指定ユーザーのカート情報を取得
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}
//ユーザーIDと商品IDが一致している情報を取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}
//カートへ商品追加の関数
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}
//カートへ新しく商品を追加
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}
//カートの指定商品の数量を更新
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}
//カートに追加されている商品を1商品削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}
//商品の購入処理
function purchase_carts($db, $carts){
  //カート商品の有無、また購入可否判断
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  //トランザクション開始
  $db->beginTransaction();
  try{
    //購入履歴へ登録
    insert_history($db, $carts[0]['user_id']);
    //最新で追加した注文番号を取得して、変数へ代入
    $order_id = $db->lastInsertId();
    //カート商品は複数ある可能性もあるため、配列用のforeachで適用していく
    foreach($carts as $cart){
      //最新で購入履歴へ追加した注文番号も一緒に購入詳細へ登録
      insert_detail($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount']);
      if(update_item_stock(
          $db,
          $cart['item_id'],
          $cart['stock'] - $cart['amount']
        ) === false){
        set_error($cart['name'] . 'の購入に失敗しました。');
      }
    }
    //現在、カートに入っている商品を削除
    delete_user_carts($db, $carts[0]['user_id']);
    $db->commit();
  }catch(PDOException $e){
    $db->rollback();
    throw $e;
  }
}

//現在、ログインユーザーのカート商品を削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}

//カート内の商品合計の結果を返す
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

