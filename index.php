<?php
  //フォームに入力した情報をregist.phpに送信する記述
  ini_set('display_errors',1);
  error_reporting(E_ALL);
  session_start();

  if(isset($_POST['datapost'])){
   $_SESSION['name'] = $_POST['name']; 
   $_SESSION['post_text'] = $_POST['post_text'];    
   header('Location: regist.php'); 
  }

  ?>


<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="reset.css"　charset="UTF-8">
  <link rel="stylesheet" href="index3.css"　charset="UTF-8">

  <title>PHP TEST</title>
</head>

<body>
  <img src="top_image.jpg" class="top-image" width="100%">
  <?php

  try{
    //データベースへ接続
    $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
    $db['dbname'] = ltrim($db['path'], '/');
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
    $user = $db['user'];
    $password = $db['pass'];
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );
    $pdo = new PDO($dsn,$user,$password,$options);

    // SQL作成
    $sql = "SELECT * FROM messages;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    //データの取得
    $datas = []; //空の配列を用意することで、配列の中身がないときにエラーが表示されることを回避
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $datas[] = $result;
    rsort($datas);
    }
    
    //もしエラーが発生した時にエラーを表示する
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    }catch(PDOException $e){
    echo $e -> getName();
    die();
    }
    //接続を解除
    $dbh = null;

?>

  <?php 
    //ページネーション
    define('MAX','6'); 
    $datas_num = count($datas);
    $max_page = ceil($datas_num / MAX);  //必要なページ数を確認
    if(!isset($_GET['page_id'])){ 
      $now = 1;
    }else{
      $now = $_GET['page_id'];
    }

    $start_no = ($now - 1) * MAX;
    $disp_data = array_slice($datas, $start_no, MAX, true);
  ?>




  <h2>INDEX</h2>
  <div class="parent">
    <?php foreach($disp_data as $data) { ?>
      <div class="wrapper">
        <div class="name">
          <p><?php echo $data['name'] ?></p>
        </div>
        <div class="message">
          <p class="message"><?php echo $data['post_text'] ?></p>
        </div>
        <div class="date">
          <p class="date"><?php echo $data['post_date'] ?></p>
        </div>
      </div>
    <?php } ?>
  </div>

  <p class="pagination">
    <?php 
      echo '全'.$datas_num.'件　';

      if($now > 1){ // リンクをつけるかの判定
        echo '<a class="pagination" href=\'/yoshida2/index.php?page_id='.($now - 1).'\')>前へ</a>'. '　';
      }else{
        echo '前へ'. '　';
      }

      for($i = 1; $i <= $max_page; $i++){ // 最大ページ数分リンクを作成
        if ($i == $now) { // 現在表示中のページ数の場合はリンクを貼らない
            echo $now. '　'; 
        } else {
            echo '<a class="pagination" href=\'/yoshida2/index.php?page_id='. $i. '\')>'. $i. '</a>'. '　';
        }
      };

      if($now < $max_page){ 
        echo '<a class="pagination" href=\'/yoshida2/index.php?page_id='.($now + 1).'\')>次へ</a>'. '　';
      } else {
        echo '次へ';
      }
    ?>

  </p>

  <form method="POST" action="index.php" class="form">
    <input type="text" name="name" class="name-form" placeholder="Name">
    <textarea name="post_text" class="text-form" placeholder="Message"></textarea>
    <input type="submit" name="datapost" value="SEND" class="btn">
  </form> 

</body>