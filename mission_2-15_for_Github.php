<?php
header('Content-Type: text/html; charset=UTF-8');

//データベースへの接続(2-7)
$dsn	=	'*************';
$user	=	'*************';
$dbpw	=	'*************';
$pdo	=	new PDO($dsn,$user,$dbpw);

//テーブルは2-8を用いて作っておく

//変数の受け取り
$name	=	$_POST['name'];
$come	=	$_POST['comment'];
$time	=	date("Y/m/d/ G:i:s");
$pass	=	$_POST['pass'];
//削除指定番号を受け取る
$hid_del	=	$_POST['del_num'];
//編集指定番号を受け取る
$hid_edi	=	$_POST['edi_num'];//番号
//編集指定番号を保持する
//$hid_edi2	=	$_POST['hid_edi'];//パスワード
//$hidden		=	$_POST['hidden'];
$hid_edi2		=	$_POST['hidden'];

//idの最大値+1を得る
//$sql	=	$pdo->SELECT MAX(id) + 1 FROM table15;
$sql	=	'SELECT MAX(id) FROM table15;';
$results=	$pdo -> query($sql);

$max = $pdo->query("SELECT MAX(id) FROM table15")->fetchColumn();
//echo $max;
$id	=	$max + 1;

//if文で条件分岐
//-----------------------------書込み機能-----------------------
//書き込みがある時{
if($_POST['name'] != false and $_POST['comment'] != false and $hid_edi2 != true){
	//編集番号が指定されていない時
	//テーブルへの書き込み(2-11)
	$stmt	=	$pdo->query('SET NAMES utf8'); //文字化け対策
	$sql	=	$pdo ->prepare("INSERT INTO table15(id,name,comment,time,password) VALUES(:id,:name,:comment,:time,:password);");
	$sql	->	bindParam(':id',		$id, PDO::PARAM_INT);
	$sql	->	bindParam(':name',		$name, PDO::PARAM_STR);
	$sql	->	bindParam(':comment',	$come, PDO::PARAM_STR);
	$sql	->	bindParam(':time',		$time, PDO::PARAM_STR);
	$sql	->	bindParam(':password', 	$pass, PDO::PARAM_STR);
	$sql	->	execute();
}elseif($_POST['name'] != false and $_POST['comment'] != false and $hid_edi2 == true){
	//編集する
	//echo "編集しました<br>";
	$id		=	$hid_edi2;
	$sql	=	"update table15 set name ='$name',comment = '$come', password = '$pass' where id = '$id';";
	$results =	$pdo ->query($sql);
	$edi_flag	= 1;
}
	//編集番号が指定されている時
		//テーブルの内容を編集(2-13)
//削除指定番号があり、パスワードが入力された時
//------------------------------削除機能----------------------
if(!empty($_POST['hid_del'])){
	//削除指定番号を変数に格納
	$id		=	$_POST['hid_del'];
	//指定された番号のプロパティを取得
	/*$sql	=	"SELECT password from table15 where id = '$id';";
	$result	=	$pdo->query($sql);
	echo $result[0];
	//パスワードを受け取る
	$judge	=	$result;
	if($judge == $_POST['pass_del']){
		//パスワードが正しい時、削除する
		$sql	=	"delete from table15 where id = '$id';";
		$result	=	$pdo->query($sql);
	}*/
	//prepareメソッドでSQLをセット
	$stmt	=	$pdo->prepare("select password from table15 where id = :id");
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
		$sql	=	"update table15 set name ='*****', comment = '*****' where id = '$id';";
		$result	=	$pdo->query($sql);
	}
}
	//テーブル内容の削除(2-14)
//編集指定番号がある時
//------------------------------編集機能---------------------------
if(!empty($_POST['pass_edi'])){
	//編集番号を受け取る
	$id		=	$_POST['hid_edi'];
	//編集するプロパティを受け取る
	//prepareメソッドでSQLをセット
	$stmt	=	$pdo->prepare("select name, comment, password from table15 where id = :id");
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
<title>カレーブログ</title>

<!-- 見出しをつける -->
		<h1>カレーブログ</h1>
		
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
<form action="" method="post">
	<div>
		名前: <input type = "text" name="name" value="<?php echo $edi_name; ?>"/>
		</div>
	<div>
		コメント: <input type = "text" name="comment" value="<?php echo $edi_comment; ?>"/>
		</div>
	<div>
		削除・編集用パスワード: <input type = "password" name="pass" value=""/>
		<!-- <input type = "hidden" name="pass_id" value="<?php echo $pass_id; ?>"/> -->
		</div>
	<div>
		<input type = "submit" />
		</div>
	<div>
		<?php if($hidden == true):?>
		<input type = "hidden" name="hidden" value="<?php echo $hidden; ?>"/>
		<?php endif; ?>
		</div>
</form>

<!--削除対象番号を指定する -->
<form action="" method="post">
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
</html>

<?php
//-------------------------------------ブラウザへの表示--------------------------------------

//--------------------------テーブル名の表示------------------------------
/*//テーブル名の表示？(2-9)
echo "テーブル名一覧<br>";
$sql	=	'SHOW TABLES;';
$result	=	$pdo -> query($sql);

foreach($result as $row){
	echo $row[0];
	echo $row[1];
//	var_dump($row);
	echo '<br>';
}*/
echo "<hr>";

//--------------------------テーブル内容の表示-----------------------------
//テーブル内容の表示(2-12)
//テーブル内容の取得
$sql	=	'SELECT * FROM table15;';
$results=	$pdo -> query($sql); //実行結果取得

//以下でブラウザ上に出力する
foreach($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	if(strpos($row['name'],"****") === false){
		echo "投稿番号:".$row['id'].' ';
		echo "名前:".$row['name'].' ';
		echo "コメント:".$row['comment'].' ';
		echo "投稿時間:".$row['time'].'<br>';
	}else{
		echo "この投稿は削除されました<br>";
	}
}

echo "<hr>";


?>