<?php
header('Content-Type: text/html; charset=UTF-8');

/*-----------------データベースへの接続---------------*/
$dsn	=	'***';
$user	=	'***';
$dbpw	=	'***';
$pdo	=	new PDO($dsn,$user,$dbpw);
/*-----------------データベースへの接続---------------*/

session_start(); //セッション開始
$stmt	=	$pdo->query('SET NAMES utf8'); //文字化け対策

//ログアウトボタンが押された場合
if (isset($_POST["logout"])){
	unset($_SESSION['user_name']);
}

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
	// 1. ユーザIDの入力チェック
	if (empty($_POST["user_name"])) {  // emptyは値が空のとき
		$errorMessage = '名前が未入力です。'; //IDが入力されてないとき
	} else if (empty($_POST["user_pass"])) {
		$errorMessage = 'パスワードが未入力です。'; //パスが入力されてないとき
	}

	if (!empty($_POST["user_name"]) && !empty($_POST["user_pass"])) { //両方入力されたとき
		// 入力したユーザIDを格納
		$user_name = $_POST["user_name"];
		
		// 3. エラー処理
		try {
			$pdo = new PDO($dsn, $user,$dbpw, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)); //エラーが出た時点で終了する https://qiita.com/7968/items/6f089fec8dde676abb5b
			//user_idが入力されたIDと同じ部分のプロパティを取得する
			$stmt = $pdo->prepare('SELECT * FROM member WHERE name = :name and flag = 1');
			$stmt	->	bindParam(':name',$user_name, PDO::PARAM_STR);
			$stmt->execute();
			// 入力したユーザパスを格納
			$user_pass	=	$_POST["user_pass"];
			$hash		=	hash('md5',$user_pass, FALSE);
			//echo "ハッシュ発行".$hash.$user_pass;
			//fetchを実行できる場合trueを返す
			if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				//echo "フェッチ";
				//パスワード認証
				if ($hash == $row['password']) {
					session_regenerate_id(true); //セッション固定攻撃から利用者を守る
					$_SESSION["user_name"]=$user_name;
					header("Location: crane_game_main2.php");  // メイン画面へ遷移
					exit();  // 処理終了
				} else {
					// 認証失敗
					$errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
				}
			} else {
				// 4. 認証成功なら、セッションIDを新規に発行する
				// 該当データなし
				$errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
			}
		} catch (PDOException $e) {
			$errorMessage = 'データベースエラー';
			//$errorMessage = $sql;
			// $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
			// echo $e->getMessage();
		}
	}
}

echo $errorMessage."<br>";
			
//変数の受け取り
$name	=	$_POST['name'];
$come	=	$_POST['comment'];
$time	=	date("Y/m/d/ G:i:s");
$pass	=	$_POST['pass'];
$c_name	=	$_POST['center_name'];
$c_place=	$_POST['center_place'];
$arm	=	$_POST['arm_pow'];
$smoke	=	$_POST['smoke'];
$toilet	=	$_POST['toilet'];
$prize	=	$_POST['prize'];
//削除指定番号を受け取る
$hid_del	=	$_POST['del_num'];
//編集指定番号を受け取る
$hid_edi	=	$_POST['edi_num'];//番号
//編集指定番号を保持する
//$hid_edi2	=	$_POST['hid_edi'];//パスワード
//$hidden		=	$_POST['hidden'];
$hid_edi2		=	$_POST['hidden'];
$img_name		=1;
$img_mime		=1;
$img_data		=1;


//ファイルの投稿がある場合
if($_FILES['file']){
	//print_r($_FILES['file']);
	//名前の取得
	$img_name	=	$_FILES['file']["name"];
	//echo "イメージネーム: ".$img_name."<br>";
	//mimetypeの取得
	$img_mime	=	$_FILES['file']["type"];
	//echo "mime type: ".$img_mime."<br>";
	//バイナリデータの取得
	$img_data	=	file_get_contents($_FILES['file']["tmp_name"]);
	//echo "アップしたファイルの一時パス: ".$img_data."<br>";
	//添付先投稿番号
	$con_id		=	$_POST['con_id'];
	//パスワード受け取り
	$con_pass	=	$_POST['con_pass'];
	//echo $con_id;
	//投稿にかけたパスワード受け取り
	$sql		=	"SELECT * FROM crane_game2 where id = '$con_id';";
	$results	=	$pdo -> query($sql); //実行結果取得
	foreach($results as $row){
				$file_pass	=	$row['password'];
	}
	//echo $file_pass;
	
	
	if($file_pass == $con_pass){
		//テーブルへ書き込み
		//echo "分岐";
		$sql	=	$pdo ->prepare("update crane_game2 set file_name = :file_name, file_exte = :file_exte, file_data = :file_data where id = '$con_id';");
		$sql	->	bindParam(':file_name', $img_name);
		$sql	->	bindParam(':file_exte', $img_mime);
		$sql	->	bindParam(':file_data', $img_data);
		$sql	->	execute();
	}
}

//if文で条件分岐
//-----------------------------書込み機能-----------------------
//書き込みがある時{
if($_POST['name'] != false and $_POST['center_name'] != false and $_POST['center_place'] != false and $_POST['arm_pow'] != false and $_POST['pass'] != false and $hid_edi2 != true){
	//編集番号が指定されていない時
	//テーブルへの書き込み(2-11)
	$stmt	=	$pdo->query('SET NAMES utf8'); //文字化け対策
	$sql	=	$pdo ->prepare("INSERT INTO crane_game2(id,name,center_name,center_place,arm_pow,smoke,toilet,prize,comment,time,password,file_name,file_exte, file_data) VALUES(:id,:name,:center_name,:center_place,:arm_pow,:smoke,:toilet,:prize,:comment,:time,:password,:file_name,:file_exte, :file_data);");
	$sql	->	bindParam(':id',		$id, PDO::PARAM_INT);
	$sql	->	bindParam(':name',		$name, PDO::PARAM_STR);
	$sql	->	bindParam(':center_name',		$c_name, PDO::PARAM_STR);
	$sql	->	bindParam(':center_place',		$c_place, PDO::PARAM_STR);
	$sql	->	bindParam(':arm_pow',		$arm, PDO::PARAM_INT);
	$sql	->	bindParam(':smoke',		$smoke, PDO::PARAM_INT);
	$sql	->	bindParam(':toilet',	$toilet, PDO::PARAM_INT);
	$sql	->	bindParam(':prize',		$prize, PDO::PARAM_STR);
	$sql	->	bindParam(':comment',	$come, PDO::PARAM_STR);
	$sql	->	bindParam(':time',		$time, PDO::PARAM_STR);
	$sql	->	bindParam(':password', 	$pass, PDO::PARAM_STR);
	$sql	->	bindParam(':file_name', $img_name);
	$sql	->	bindParam(':file_exte', $img_mime);
	$sql	->	bindParam(':file_data', $img_data);
	$sql	->	execute();
	
}elseif($_POST['name'] != false and $_POST['center_name'] != false and $_POST['center_place'] != false and $_POST['arm_pow'] != false and $_POST['pass'] != false and $hid_edi2 == true){
	//編集番号が指定されている時
	//テーブルの内容を編集(2-13)
	echo "編集しました<br>";
	$id		=	$hid_edi2;
	$sql	=	"update crane_game2 set name='$name',center_name='$c_name',center_place='$c_place',arm_pow='$arm',smoke='$smoke',toilet='$toilet',prize='$prize',comment = '$come', password = '$pass' where id = '$id';";
	$results =	$pdo ->query($sql);
	$edi_flag	= 1;
}elseif(isset($_POST['kakikomi'])){
	if($_POST['name'] == false or $_POST['center_name'] == false or $_POST['center_place'] == false or $_POST['arm_pow'] == false or $_POST['pass'] == false){
		$errormessage	=	"必須項目が抜けています。";
	}
}
	
//削除指定番号があり、パスワードが入力された時
//------------------------------削除機能----------------------
if(!empty($_POST['hid_del'])){
	//削除指定番号を変数に格納
	$id		=	$_POST['hid_del'];
	//prepareメソッドでSQLをセット
	$stmt	=	$pdo->prepare("select password from crane_game2 where id = :id");
	//bindParamメソッドでパラメータをセット
	$stmt	->	bindParam(':id',$id, PDO::PARAM_INT);
	//executeでクエリを実行
	$stmt	->	execute();
	//結果を表示
	$result	=	$stmt->fetch();
	if (!$result) {
    die('クエリーが失敗しました。'.mysql_error());
	}
	//echo "password =".$result[0];
	//var_dump($result);
	$judge	=	$result["password"];
	if($result["password"] == $_POST['pass_del']){
		//パスワードが正しい時、削除する
		$sql	=	"update crane_game2 set name ='*****', comment = '*****' where id = '$id';";
		$result	=	$pdo->query($sql);
	}
}
	//テーブル内容の削除(2-14)
//編集指定番号がある時
//------------------------------編集機能---------------------------
if(!empty($_POST['pass_edi'])){
	//編集番号を受け取る
	$id		=	$_POST['hid_edi'];
	//prepareメソッドでSQLをセット
	$stmt	=	$pdo->prepare("select name, comment, password, center_name, center_place, prize from crane_game2 where id = :id");
	//bindParamメソッドでパラメータをセット
	$stmt	->	bindParam(':id',$id, PDO::PARAM_INT);
	//executeでクエリを実行
	$stmt	->	execute();
	//結果を表示
	$result	=	$stmt->fetch();
	//デバッグ
	if (!$result) {
    die('クエリーが失敗しました。'.mysql_error());
	}
	//var_dump($result);
	//パスワードが正しいとき編集したいプロパティを送る
	if($_POST['pass_edi']	==	$result["password"]){
		$edi_name	=	$result["name"];
		$edi_comment=	$result["comment"];
		$edi_c_name	=	$result["center_name"];
		$edi_c_place=	$result["center_place"];
		$edi_prize	=	$result["prize"];
		//echo "name =".$edi_name."<br>";
		//echo "comment =".$edi_comment."<br>";
		$hidden	=	$_POST['hid_edi']; //番号
	}
	$judge	=	$result["password"];
}
	//書き込みフォームに内容を表示

?>

<html>
<meta charset = "utf-8" />

<!-- タイトル -->
<!-- ログインしてなければログインフォームを表示 -->
<!formタグで入力フォームを作成する>
<?php if(empty($_SESSION['user_name'])):?>
<!-- 見出しをつける -->
<h1>ユーザー認証</h1>
		<form action="" method="post">
		<div>
			お名前: <input type = "text" name="user_name" />
			</div>
		<div>
			パスワード: <input type = "text" name="user_pass" />
			</div>
		<div>
			<input type = "submit" value="ログイン" name="login" />
			</div>
			</form>
		<?php endif; ?>
</html>

<html>
<meta charset = "utf-8" />
		
<!--------------- ログインされていなければ掲示板を表示しない ---------------------->
<?php if(!empty($_SESSION['user_name'])):?>
	
<!-- タイトル -->
		<title>クレーンゲームDB</title>
<!-- 見出しをつける -->
		<h1>クレーンゲームDB</h1>
		
		<!-- ログインしていれば次のメッセージとログアウトフォームを表示 -->
		<?php if(!empty($_SESSION['user_name'])):?>
		<?php echo "こんにちは".$_SESSION['user_name']."さん。<br>"; ?>
		<form action="" method="post">
		<div>
			<input type = "submit" value="ログアウト" name="logout" />
			</div>
			</form>
		<?php endif; ?>
		
		あなたの周りのクレーンゲームが置いてあるゲーセンについて教えてください!(今回は架空のゲーセンでもいいです！)<hr>
		
		<?php if(!empty($errormessage)):?>
		<font color="red"><?php echo $errormessage; ?></font>
		<?php endif; ?>
		
<!-- 削除機能で削除したとき表示する -->
<?php if(!empty($_POST['pass_del']) and ($judge == $_POST['pass_del'])):?>
		<b><?php echo "削除しました。"; ?></b>
		<?php endif; ?>
		
<!-- 削除機能でパスワードが違っていたとき表示する -->
<?php if(!empty($_POST['pass_del']) and ($judge != $_POST['pass_del'])):?>
		<font color="red"><?php echo "削除対象のパスワードが違います。"; ?></font>
		<?php endif; ?>

<!-- 編集モードの場合、編集モードと表示する-->
<?php if(!empty($_POST['pass_edi']) and ($judge == $_POST['pass_edi'])):?>
		<b><?php echo "編集モードです。"; ?></b>
		<?php endif; ?>

<!-- 編集機能でパスワードが違っていたとき表示する-->
<?php if(!empty($_POST['pass_edi']) and ($judge != $_POST['pass_edi'])):?>
		<font color="red"><?php echo "編集対象のパスワードが違います。"; ?></font>
		<?php endif; ?>

<!-- 編集機能で編集したとき表示する-->
<?php if($edi_flag == 1):?>
		<font color="blue"><?php echo "編集しました。"; ?></font>
		<?php endif; ?>

<!--formタグで入力フォームを作成する-->
<form action="" method="post" enctype="multipart/form-data">
	<投稿機能>
		※がついている項目は必須項目です！
	<div>
		※投稿者: <input type = "text" name="name" value="<?php echo $_SESSION['user_name']; ?>"/>
		</div>
	<div>
		※ゲームセンターの名前: <input type = "text" name="center_name" value="<?php echo $edi_c_name; ?>"/>
		</div>
	<div>
		※ゲームセンターの場所: <input type = "text" name="center_place" value="<?php echo $edi_c_place; ?>"/>
		</div>
	<div>
		※アーム強度(数字が大きいほど強い): <input type = "radio" name="arm_pow" value="1"/>1
		<input type = "radio" name="arm_pow" value="2"/>2
		<input type = "radio" name="arm_pow" value="3"/>3
		<input type = "radio" name="arm_pow" value="4"/>4
		<input type = "radio" name="arm_pow" value="5"/>5
		</div>
	<div>
		タバコ: <input type = "radio" name="smoke" value="2"/>禁煙
		<input type = "radio" name="smoke" value="1"/>喫煙可
		</div>
	<div>
		トイレ: <input type = "radio" name="toilet" value="2"/>有り
		<input type = "radio" name="toilet" value="1"/>無し
		</div>
		
	<div>
		賞品の種類: <input type = "text" name="prize" value="<?php echo $edi_prize; ?>"/>
		</div>
		
	<div>
		その他コメント: <input type = "text" name="comment" value="<?php echo $edi_comment; ?>"/>
		</div>
	<div>
		※削除・編集用パスワード: <input type = "password" name="pass" value=""/>
		<!-- <input type = "hidden" name="pass_id" value="<?php echo $pass_id; ?>"/> -->
		</div>
	<div>
		<input type = "submit" name="kakikomi" />
		</div>
	<div>
		<?php if($hidden == true):?>
		<input type = "hidden" name="hidden" value="<?php echo $hidden; ?>"/>
		<?php endif; ?>
		</div>
</form>


<hr>

<form action="" method="post" enctype="multipart/form-data">
	<ファイルアップロード><br>
	
	<div>
		ファイルを添付したい投稿番号: <input type = "text" name="con_id" />
		</div>
	<div>
		アップロードするファイル: <input type="file" name="file" value="">
		</div>
	<div>
		パスワード: <input type = "password" name="con_pass" value=""/>
		</div>
	<div>
		<input type = "submit"/>
		</div>
</form>
		
<hr>
		

<!--削除対象番号を指定する -->
<form action="" method="post">
	<削除機能><br>
	<div>
		<!-- 削除番号が指定されているとき(isset($_POST['del_num']))、フォームを表示しない-->
		<?php if(!isset($_POST['del_num'])):?>
		削除対象番号: <input type	=	"number" name="del_num"/>
		<?php elseif(isset($_POST['del_num'])):?>
		削除対象番号: <?php echo $_POST['del_num']; ?>
		<?php endif; ?>
		</div>
	<div>
		<font color = "blue">
		<?php if($_POST['del_num'] == true):?>
		削除するにはパスワードを入力してください<input type = "password" name="pass_del" />
		<input type = "hidden" name="hid_del" value="<?php echo $hid_del; ?>"/>
		<?php endif; ?>
		</font>
		</div>
	<div>
		<input type = "submit" />
		</div>
</form>
		
<!--編集対象番号を指定する -->
<form action="" method="post">
	<編集機能><br>
	<div>
		<?php if(!isset($_POST['edi_num'])):?>
		編集対象番号: <input type	=	"number" name="edi_num"/>
		<?php elseif(isset($_POST['edi_num'])):?>
		編集対象番号: <?php echo $_POST['edi_num']; ?>
		<?php endif; ?>
		</div>
	<div>
		<font color = "blue">
		<?php if($_POST['edi_num'] == true):?>
		編集するにはパスワードを入力してください<input type = "password" name="pass_edi" />
		<input type = "hidden" name="hid_edi" value="<?php echo $hid_edi; ?>"/>
		<?php endif; ?>
		</font>
		</div>
	<div>
		<input type = "submit" />
		</div>
		</form>
		
<?php endif; ?>
</html>

<?php
//-------------------------------------ブラウザへの表示--------------------------------------

//ログインされているときのみ掲示板を表示する
if(!empty($_SESSION['user_name'])){
	
	echo "<hr>";
	
//--------------------------テーブル内容の表示-----------------------------
	//テーブル内容の表示(2-12)
	//テーブル内容の取得
	$sql	=	'SELECT * FROM crane_game2 ORDER BY id ASC;';//id順に昇順で表示
	$results=	$pdo -> query($sql); //実行結果取得
	
	//以下でブラウザ上に出力する
	foreach($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		if(strpos($row['name'],"****") === false){
			echo "投稿番号:".$row['id'].' ';
			echo "投稿者:".$row['name'].' ';
			echo "投稿時間:".$row['time'].'<br>';
			echo "ゲーセンの名前:".$row['center_name'].' ';
			echo "ゲーセンの場所:".$row['center_place'].'<br>';
			echo "アーム強度(5段階評価):".$row['arm_pow'].'  ';
			if(!empty($row['smoke'])){
				if($row['smoke'] == 2){
					echo "タバコ:禁煙  ";
				}
				if($row['smoke'] == 1){
					echo "タバコ:喫煙可  ";
				}
			}
			if(!empty($row['toilet'])){
				if($row['toilet'] == 2){
					echo "トイレ:有り  ";
				}
				if($row['toilet'] == 1){
					echo "トイレ:無し  ";
				}
			}
			echo "賞品の種類:".$row['prize'].'  ';
			echo "その他コメント:".$row['comment'].'<br>';
			
			//画像の表示
			$mime	=	$row["file_exte"];
			if(strpos($mime,"image") !== false){//mimetypeにimageが入っていた場合画像出力
				//echo "画像テスト";
				echo ("<img src='./create_image.php?id=".$row["id"]."'><br>"); //画像を出力
			}else if(strpos($mime,"video") !== false){//mimetypeにvideoが含まれる場合動画出力
				//echo "動画テスト";
				echo ("<video src='./create_image.php?id=".$row["id"]."' width=\"426\" height=\"240\" controls></video><br>");//動画を出力
			}
			echo "<br>";
		}else{
			echo "投稿番号:".$row['id'].' ';
			echo "この投稿は削除されました<br><br>";
		}
	}
	
	echo "<hr>";
}//ログインif文終了

?>