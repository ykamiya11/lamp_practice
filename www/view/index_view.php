<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print($item['name']); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                    <!-- トークンの埋め込み -->
                    <input type="hidden" name="csrf_token" value="<?php print $token ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <h2 class="my-3">人気ランキング1位〜3位</h2>
    <div class="card-deck">
      <div class="row mb-3">
      <?php foreach ($ranking as $key => $value) { ?>
        <div class="col-4 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print('第'. ($key + 1) .'位：'.$value['name']); ?>
            </div>
            <figure class="card-body">
              <img class="card-img h-20" src="<?php print(IMAGE_PATH . $value['image']); ?>">
              <figcaption>
                <?php print(number_format($value['price'])); ?>円
                <?php if($value['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートへ追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($value['item_id']); ?>">
                    <!-- トークン処理の追加 -->
                    <input type="hidden" name="csrf_token" value="<?php print $token ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>
</body>
</html>