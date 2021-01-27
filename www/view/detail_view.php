<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>購入明細</h1>
    <?php if(count($details) === 0){ ?>
      <p>該当する明細はありません</p>
    <?php } else { ?>

    <p>注文番号：<?php print $_POST['order_id'];?></p>
    <p>購入日時：<?php print $_POST['purchase_datetime'];?></p>
    <p>合計金額：<?php print $_POST['total'];?>円</p>
    <?php } ?>
    <table class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
        <th>商品名</th>
        <th>購入価格</th>
        <th>個数</th>
        <th>小計</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($details as $detail){ ?>
        <tr>
        <td><?php print $detail['name'] ;?></td>
        <td><?php print $detail['price'] ;?>円</td>
        <td><?php print $detail['amount'] ;?>個</td>
        <td><?php print $detail['subtotal'] ;?>円</td>
        </tr>
        <?php } ?>
    </tbody>
    </table>
  </div>
</body>
</html>