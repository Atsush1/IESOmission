<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(rand(0,99));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
 
/*-----------------データベースへの接続---------------*/
$dsn	=	'***';
$user	=	'***';
$dbpw	=	'***';
$pdo	=	new PDO($dsn,$user,$dbpw);
/*-----------------データベースへの接続---------------*/

//ログインページへのボタンが押されたとき
if (isset($_POST["login"])){
	header("Location: crane_game_main2.php");//入力フォームに何も書いていない場合入力フォーム画面に戻る
	exit();
}

//エラーメッセージの初期化
$errors = array();
 
if(empty($_GET)) {
	header("Location: registration_mail_form.php");//入力フォームに何も書いていない場合入力フォーム画面に戻る
	exit();
}else{
	//GETデータを変数に入れる
	$urltoken = $_GET[urltoken];
	//メール入力判定
	if ($urltoken == ''){
		$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
	}else{
		try{
			//例外処理を投げる（スロー）ようにする
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//flagが0の未登録者・仮登録日から24時間以内
			$stmt = $pdo->prepare("SELECT mail FROM member WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
			$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$stmt->execute();
			
			//登録件数取得
			$row_count = $stmt->rowCount();
			
			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count ==1){//検索された登録が1件の場合
				$flag	=	1;
				$stmt = $pdo->prepare("update member set flag = :flag WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
				$stmt->bindValue(':flag', $flag, PDO::PARAM_INT);
				$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
				$stmt->execute();
			}else{
				$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
			}
			
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}
	}
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>会員登録画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>会員登録画面</h1>
 
<?php if (count($errors) === 0): ?>
 
	登録完了しました！
	<form action="" method="post">
		<div>
			<input type = "submit" value="ログインページへ" name="login" />
			</div>
			</form>
	
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<?php endif; ?>
 
</body>
</html>

<?php
	/*$sql	=	'SELECT * FROM member;';//id順に昇順で表示
	$results=	$pdo -> query($sql); //実行結果取得
	
	//以下でブラウザ上に出力する
	foreach($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		if(strpos($row['name'],"****") === false){
			echo "投稿番号:".$row['id'].' ';
			echo "名前:".$row['name'].' ';
			echo $row['password'].' ';
			echo "flag:".$row['flag'].'<br>';
		}else{
			echo "この投稿は削除されました<br>";
		}
	}*/
?>
