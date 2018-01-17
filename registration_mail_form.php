<?php
//セッションを始める
session_start();

header("Content-type: text/html; charset=utf-8");//文字化け防止

//クロスサイトリクエストフォージェリ(CSRF)対策
$_SESSION['token']	=	base64_encode(rand(0,99));
$token	=	$_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

?>

<!DOCTYPE html>
<html>
<head>
<title>メール登録画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>メンバー登録</h1>

登録済みの方は<a href="http://co-553.it.99sv-coco.com/crane_game_main2.php">こちら</a><br>

<form action="registration_mail_check.php" method="post">
<p>名前:<input type="text" name="name" size = "50"></p>
<p>パスワード:<input type="text" name="pass" size = "50"></p>
<p>メールアドレス:<input type="text" name="mail" size = "50"></p>
		
		<input type="hidden" name="token" value="<?=$token?>">
		<input type="submit" value"登録する">
		
		</form>
		
</body>
</html>
