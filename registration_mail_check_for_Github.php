<?php
//セッション開始
session_start();
header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ(CRSF)対策のトークン判定
if($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

/*-----------------データベースへの接続---------------*/
$dsn	=	'***';
$user	=	'***';
$dbpw	=	'***';
$pdo	=	new PDO($dsn,$user,$dbpw);
/*-----------------データベースへの接続---------------*/

//エラーメッセージの初期化
$errors	=	array();

if(empty($_POST)){
	header("Location: registration_mail_form.php");//メール入力フォームが空の場合、入力ページへジャンプさせる
	exit();
}else if(!empty($_POST)){
	//POSTされたデータを変数に入れる
	$mail	=	$_POST['mail'];
	$user_name	=	$_POST['name'];
	$password	=	$_POST['pass'];
	$hash	=	hash('md5',$password, FALSE);
	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{//メールにしようされている文字の判定
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
		$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
	}
	if(empty($_POST['name'])){
		$errors['name'] = "名前が入力されていません。";
	}
	if(empty($_POST['pass'])){
		$errors['pass'] = "パスワードが入力されていません。";
	}
	//データベースに接続して同じ名前が登録されていないか確認する
	$sql	=	'SELECT * FROM member;';
	$results=	$pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE); //実行結果取得
	foreach($results as $row){
			//ポストで受け取った名前と同じ名前がある場合、エラーを出す
			if($user_name == $row['name']){
				$errors['same_name']	=	"同じ名前が登録されています。";
			}
			if($mail == $row['mail']){
				$errors['same_mail']	=	"同じアドレスが登録されています。";
			}
	}
}

if(count($errors) === 0){
	
	$urltoken	=	hash('sha256', uniqid(rand(),1));//ユニークIDをより細かく生成し、ハッシュ化
	$url = "http://co-553.it.99sv-coco.com/3-9.php"."?urltoken=".$urltoken;//トークンを使用したURLを発行
	
	//データベースへ登録
	try{
		//例外処理を投げるようにする
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt	=	$pdo->prepare("INSERT INTO member(name,password,urltoken,mail,date) VALUES (:name,:password,:urltoken,:mail,now() )");
		$stmt	->	bindValue(':name',$user_name, PDO::PARAM_STR);
		$stmt	->	bindValue(':password',$hash, PDO::PARAM_STR);
		$stmt	->	bindValue(':urltoken',$urltoken, PDO::PARAM_STR);
		$stmt	->	bindValue(':mail',$mail, PDO::PARAM_STR);
		$stmt	->	execute();
		
	}catch(PDOException $e){//エラーをキャッチ
		print('Error:' .$e->getMessage());
		die();//エラーがあった場合処理を止める
	}
	//メールの宛先(送り先)
	$mailTo = $mail;
 
	//メールが送信できなかった場合にエラーメッセージを送るメールアドレス
	$returnMail = 'atsushi.saito.1993@gmail.com';
	
	$subject = "仮登録完了のお知らせ";//メールタイトル
	$name = "齋藤敦";//送信者名
	$mail = 'atsushi.saito.1993@gmail.com';//送信者アドレス
	
//EOMはEnd of messageの略で <<< EOM と書くと EOM;と書いた場所までを一つのテキストとして扱う
$body = <<< EOM
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;
	mb_language('ja');//mb_send_mailでエンコードする言語を設定
	mb_internal_encoding('UTF-8');//エンコードする文字コードを設定
	
	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';//From:名前<アドレス> ヘッダーに文字列を使うためにはエンコードが必要らしい
 
	if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {//メールが送れたときの分岐(-fは$returnmailを設定するための引数)
	
	 	//セッション変数を全て解除
		$_SESSION = array();
	
		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}
	
 		//セッションを破棄する
 		session_destroy();
 	
 		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
 	
	 } else {//メールが送れないとき
		$errors['mail_error'] = "メールの送信に失敗しました。";
	}	
}

?>

<!DOCTYPE html>
<html>
<head>
<title>メール確認画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>メール確認画面</h1>
 
<?php if (count($errors) === 0): ?><!-- エラーがなくメールが送れた場合以下を表示-->
 
<p><?=$message?></p>

 
<?php elseif(count($errors) > 0): ?><!-- エラーがあってメールが送れなかった場合以下を表示-->
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<input type="button" value="戻る" onClick="history.back()">
 
<?php endif; ?>
 
</body>
</html>