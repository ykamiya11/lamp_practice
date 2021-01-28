<?php
require_once MODEL_PATH. 'functions.php';
require_once MODEL_PATH. 'db.php';

//購入履歴へ購入完了商品の情報を登録
function insert_history($db, $user_id){
    $sql = "
      INSERT INTO
        purchase_history(
          user_id
        )
      VALUES(?)
    ";
    return execute_query($db, $sql, array($user_id));
}

//購入詳細へ購入完了商品の各詳細情報を登録
function insert_detail($db, $order_id, $item_id, $price, $amount){
  $sql = "
     INSERT INTO
      purchase_detail(
         order_id,
         item_id,
         price,
         amount
       )
     VALUES(?,?,?,?)
   ";
    return execute_query($db, $sql, array($order_id, $item_id, $price, $amount));
}

//ログインユーザーの購入履歴情報を取得
function get_history($db, $user){
    $params = array();
    $sql = "
        SELECT
            purchase_history.order_id,
            purchase_history.purchase_datetime,
            SUM(purchase_detail.price * purchase_detail.amount) AS total
        FROM
            purchase_history
        JOIN
            purchase_detail
        ON
            purchase_history.order_id = purchase_detail.order_id
        ";
    if($user['type'] === USER_TYPE_NORMAL){
        $sql .= '
        WHERE
            purchase_history.user_id = ?
        GROUP BY
            purchase_history.order_id
        ORDER BY
            purchase_history.purchase_datetime DESC
        ';
        $params[] = $user['user_id'];
    }else{
    $sql .= '
        GROUP BY
            purchase_history.order_id
        ORDER BY
            purchase_history.purchase_datetime DESC
        ';
    }
    return fetch_all_query($db, $sql, $params);
}

//ログインユーザーの購入詳細情報を取得
function get_detail($db, $order_id, $user_id = null){
    $params = array($order_id);
    $sql = "
        SELECT
            purchase_detail.order_id,
            items.name,
            purchase_detail.price,
            purchase_detail.amount,
            purchase_detail.price * purchase_detail.amount AS subtotal
        FROM
            purchase_detail
        JOIN
            items
        ON
            purchase_detail.item_id = items.item_id
        WHERE
            purchase_detail.order_id = ?
        ";

    if($user_id !== null){
        $sql .= '
            AND
                exists( SELECT * FROM purchase_history WHERE order_id=? AND user_id = ?)
        ';
        $params[] = $order_id;
        $params[] = $user_id;
    }
    return fetch_all_query($db, $sql, $params);
}
