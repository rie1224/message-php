<?php 
  ini_set('display_errors',1);
  error_reporting(E_ALL);
  session_start(); 
  
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


  $name = $_SESSION['name'];//受け取った値を変数に入れる
  $post_text = $_SESSION['post_text'];
  $post_date = date("Y-m-d H:i:s");
  
  $stmt = $pdo -> prepare("INSERT INTO messages(name, post_text, post_date) VALUES(:name, :post_text, :post_date)");//登録準備
  $stmt -> bindValue(':name', $name, PDO::PARAM_STR);//登録する文字の型を固定
  $stmt -> bindValue(':post_text', $post_text, PDO::PARAM_STR);
  $stmt -> bindValue(':post_date', $post_date, PDO::PARAM_STR);
  $stmt -> execute();//データベースの登録を実行
  $pdo = NULL;//データベース接続を解除
  
  header('Location: index.php');
?>