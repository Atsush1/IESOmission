<?php
header('Content-Type: text/html; charset=UTF-8');

//フォームに入力・送信後、フォーム内が空の状態で更新をした際に、前に入力した内容が投稿されてしまうのを防ぐ
//session処理を行ってブラウザではなく、サーバー側で入力したデータを扱う
//SESSIONを使用
/*session_start();
//POSTが送信された場合の処理
if ($_POST['name'] != false and $_POST['comment'] != false or $_POST['del_num'] != false and $_POST['hidden'] != true) {
	//SESSIONに保存したらページを更新
	$_SESSION['name'] = $_POST['name'];
	$_SESSION['comment'] = $_POST['comment'];
	$_SESSION['del_num'] = $_POST['del_num'];
	$_SESSION['edi_num'] = $_POST['edi_num'];
	$_SESSION['hidden']	 = $_POST['hidden'];
	session_write_close();
	//現在のURLで更新
	header("Location: {$_SERVER['REQUEST_URI']}");
	//更新する際はこれより下の作業はしない
	exit();
}else{
	echo "リダイレクトしてません<br />";
}*/
?>


<?php
header('Content-Type: text/html; charset=UTF-8');

$namae		=	$_POST['name'];
$come		=	$_POST['comment'];
$jikan		=	date("Y/m/d/ G:i:s");
$del_num	=	$_POST['del_num'];
$hid_del	=	$_POST['del_num'];
$edi_num	=	$_POST['edi_num'];
$hid_edi	=	$_POST['edi_num'];
$hid_edi2	=	$_POST['hid_edi'];
$pass		=	$_POST['pass'];
$filename	=	'kadai2-2.txt';
$filename2	=	"kadai2-2_shitagaki.txt";

//ループ処理で使う配列の初期化
$bangou		=	array();
$name		=	array();
$comment	=	array();
$date		=	array();
$id			=	array();
$num		=	0;

//input type = "hidden" で表示する文字列を指定
//$hidden	=	$_POST['hidden'];
//var_dump($_POST['hidden']);
if(isset($_POST['hidden'])){
	//hidden値を格納
	$hidden	=	$_POST['hidden'];
	//var_dump( $_POST['hidden'])."<br />";
	//echo "編集モードです<br />";
}else{
	//echo "受け取れてないよ<br />";
}
//var_dump($hidden);


//var_dump($del_num)."<br />";
//-----------------------------------書き込み----------------------------------------------------
/*-----名前とコメントが書き込まれているときkadai2-2.txtに入力されたものを書き込む-----*/  
if ($_POST['name'] != false and $_POST['comment'] != false and $hidden != true){ 	
	//echo "--------------------------書き込み-----------------------------------<br />";
	//countを使った方法
	//テキストファイルを変数に格納
	//テキストファイルの内容を配列に格納
	$hairetsu2	=	file("kadai2-2_shitagaki.txt",FILE_IGNORE_NEW_LINES);
	//var_dump($hairetsu2)."<br />";
	//番号を振る
	$number = count($hairetsu2, COUNT_RECURSIVE) + 1;
	//echo "ナンバー".$number."<br />";
	//テキストを作る(改行あり)
	$text = "$number<>$namae<>$come<>$jikan<>$pass\n";
	//echo "テキスト".$text."<br />";
	//kadai2-2を追記モードで開く
	$fp = fopen($filename,"a");
	$fp2 = fopen($filename2,"a");
	//テキストをkadai2-2に追記
	fwrite($fp,$text);
	fwrite($fp2,$text);
	//kadai2-2を閉じる
	fclose($fp);
	fclose($fp2);
	
	//readfile($filename);
	//readfile($filename2);

	//echo $text;
	//echo "--------------------------書き込み終了-----------------------------------<br />";
//hidden値が存在するとき分岐する(編集モード)
}elseif($_POST['name'] != false and $_POST['comment'] != false and $hidden != false){
	//echo "elseifテスト<br />";

	//削除機能を応用する
	//テキストファイルを読み込む
	fopen($filename2,'r');
	$hairetsu2	=	file("kadai2-2_shitagaki.txt",FILE_IGNORE_NEW_LINES);
	//テキストファイルを初期化
	fopen($filename2,'w');
	
	foreach($hairetsu2 as $hai2){
	//	echo $hai."<br />";
		$bunsho	=	explode("<>", $hairetsu2[$num]);
		//explodeで取得した値をさらに配列に入れ込む
		array_push($bangou,	$bunsho[0]);
		array_push($name,	$bunsho[1]);
		array_push($comment,$bunsho[2]);
		array_push($date,	$bunsho[3]);
		array_push($id,		$bunsho[4]);
		//文字列を作成
		$text2	= "$bangou[$num]<>$name[$num]<>$comment[$num]<>$date[$num]<>$id[$num] \n";
		//$bangouと編集対象番号を比較して一致するときのみファイルを書き換える
		$fp2 =fopen($filename2,'a');
		//編集対象には新たなテキストを埋め込む
		if($bangou[$num] == $hidden){
			fwrite($fp2,"$hidden<>$namae<>$come<>$jikan<>$pass\n");
		}else{
			fwrite($fp2,$text2);
		}
		//echo "番号".$bangou[$num]."<br />";
		//$bunshoを初期化
		$bunsho	=	array();
		$num	=	$num + 1;
	}//foreach終了	
	//echo "編集テスト<br />";
	//readfile($filename);
	//echo "編集テスト終了<br />";

}else{
	//echo "条件分岐できてないよ<br />";
}

//-----------------------------------テキストファイルの読み込み------------------------------------
//ループ処理で使う配列の初期化
$bangou		=	array();
$name		=	array();
$comment	=	array();
$date		=	array();
$num		=	0;

//2-2のテキストファイルを読み込む
//echo "読み込み----------------------------------------------<br />";
$filename	=	'kadai2-2.txt';
fopen($filename,'r');
$hairetsu	=	file('kadai2-2.txt',FILE_IGNORE_NEW_LINES);
//var_dump($hairetsu)."<br />";
$filename2	=	'kadai2-2_shitagaki.txt';
fopen($filename2,'r');
$hairetsu2	=	file('kadai2-2_shitagaki.txt',FILE_IGNORE_NEW_LINES);
//var_dump($hairetsu2)."<br />";

//読み込んで取得した配列を、配列の数（行数分）だけループさせる（繰り返し処理する）
$fp 		=	fopen($filename,'w');

//さらに記号「<>」で分割することでそれぞれの値を取得する(explodeを使う)
foreach($hairetsu2 as $hai2){
//	echo $hai."<br />";
	$bunsho	=	explode("<>", $hairetsu2[$num]);
	//explodeで取得した値をさらに配列に入れ込む
	array_push($bangou,	$bunsho[0]);
	array_push($name,	$bunsho[1]);
	array_push($comment,$bunsho[2]);
	array_push($date,	$bunsho[3]);
	//取得した値をecho等を用いて表示する（※このとき区切り文字である「<>」は入れないこと）
	//削除対象番号のみ出力しない→ボツ案
//	if ($bangou[$num] != $del_num){
	$text = "投稿番号: $bangou[$num] 名前: $name[$num] コメント: $comment[$num] 投稿時間: $date[$num] \n";
//	}
	$fp =fopen($filename,'a');
	fwrite($fp,$text);
	//デバッグ
//	echo "番号".$bangou[$num]."<br />";
//	echo "時間".$date[$num]."<br />";
	//$bunshoを初期化
	$bunsho	=	array();
	$num	=	$num + 1;
	
}//foreach終了
//echo "読み込みテスト<br />";
//readfile($filename);
//readfile($filename2);
//echo "読み込みテスト終了<br />";
//echo "--------------------------読み込み終了-----------------------------------<br />";

//-------------------------------------------削除機能---------------------------------------

//ループ処理で使う配列の初期化
$bangou		=	array();
$name		=	array();
$comment	=	array();
$date		=	array();
$id			=	array();
$num		=	0;

//削除番号のみが指定されたときにする処理
if(!empty($_POST['hid_del'])){

	//echo "削除機能-------------------------------------------<br />";
	
	//テキストファイルを読み込んでexplodeで分解
	fopen($filename2,'r');
	//$hairetsu	=	file('kadai2-2.txt');
	//var_dump($hairetsu)."<br />";
	//テキストファイルを初期化
	fopen($filename,'w');
	fopen($filename2,'w');
	
	foreach($hairetsu2 as $hai2){
	//	echo $hai."<br />";
		$bunsho	=	explode("<>", $hairetsu2[$num]);
		//explodeで取得した値をさらに配列に入れ込む
		array_push($bangou,	$bunsho[0]);
		array_push($name,	$bunsho[1]);
		array_push($comment,$bunsho[2]);
		array_push($date,	$bunsho[3]);
		array_push($id,		$bunsho[4]);
		//文字列を作成
		$text	= "投稿番号: $bangou[$num] 名前: $name[$num] コメント: $comment[$num] 投稿時間: $date[$num] \n";
		$text2	= "$bangou[$num]<>$name[$num]<>$comment[$num]<>$date[$num]<>$id[$num] \n";
		//$bangouと削除対象番号を比較して一致するときのみファイルに書き込まない
		$fp =fopen($filename,'a');
		$fp2 =fopen($filename2,'a');
		//var_dump($id[$num]);
		//var_dump($_POST['pass_del']);
		$id[$num]	=	trim($id[$num]);
		//echo "<br/>";
		//削除対象には*****を埋め込む
		if($bangou[$num] == $_POST['hid_del'] and $id[$num] == $_POST['pass_del']){
			fwrite($fp,"このコメントは削除されました。\n");
			fwrite($fp2,"*****<> <> <> <>\n");
			$judge	=	$id[$num];
		}elseif($bangou[$num] == $_POST['hid_del'] and $id[$num] != $_POST['pass_del']){
			fwrite($fp,$text);
			fwrite($fp2,$text2);
			$judge	=	$id[$num];
		}else{
			fwrite($fp,$text);
			fwrite($fp2,$text2);
		}
		//echo "番号".$bangou[$num]."<br />";
		//$bunshoを初期化
		$bunsho	=	array();
		$num	=	$num + 1;
	}//foreach終了	
	//echo "削除テスト<br />";
	//readfile($filename);
	//echo "削除テスト終了<br />";
}//削除if文終了


//---------------------------------------編集機能------------------------------------------

//ループ処理で使う配列の初期化
$bangou		=	array();
$name		=	array();
$comment	=	array();
$date		=	array();
$id			=	array();
$num		=	0;

if(isset($hid_edi2)){
	echo "-------------------------編集機能-----------------------------<br />";
	//2-2.txtを１行ずつ配列に読み込む
	$hairetsu2	=	file('kadai2-2_shitagaki.txt');
	foreach($hairetsu2 as $hai2){
		$bunsho	=	explode("<>", $hairetsu2[$num]);
		//explodeで取得した値をさらに配列に入れ込む
		array_push($bangou,	$bunsho[0]);
		array_push($name,	$bunsho[1]);
		array_push($comment,$bunsho[2]);
		array_push($date,	$bunsho[3]);
		array_push($id,		$bunsho[4]);
		//echo $bangou[$num]."<br />";
		//echo $edi_num."<br />";
		//編集番号と投稿番号が一致したとき、名前とコメントを変数に格納する
		//var_dump($bangou[$num]);
		//var_dump($hid_edi2);
		//echo "<br />";
		//var_dump($id[$num]);
		//var_dump($_POST['pass_edi']);
		//echo "<br />";
		//$pass_edi = trim($id[$num]);
		//var_dump($pass_edi);
		//空白文字を取り除く
		$id[$num]	=	trim($id[$num]);
		//var_dump($id[$num]);
		//echo "<br />";
		if($bangou[$num] == $hid_edi2 and $id[$num] == $_POST['pass_edi']){
			//echo "成功?<br />";
			$edi_name		=	$name[$num];
			$edi_comment	=	$comment[$num];
			$judge	=	$id[$num];
		}elseif($bangou[$num] == $hid_edi2 and $id[$num] != $_POST['pass_edi']){
			$judge	=	$id[$num];
		}
		//echo "番号".$bangou[$num]."<br />";
		//$bunshoを初期化
		$bunsho	=	array();
		$num	=	$num + 1;
	}//foreach終了
	//セッションを用いて編集すべき内容を保持
	//$_POST['edi_name']		=	$edi_name;
	//$_POST['edi_comment']	=	$edi_comment;
	//session_write_close();
	//echo "セッション名前".$edi_name."セッションコメント".$edi_comment."<br />";
	//hidden値を格納
	$hidden	=	$_POST['hid_edi'];
}//編集if終了

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
		<font color="red"><?php echo "パスワードが違います。"; ?></font>
		<?php endif; ?>

<!-- 編集モードの場合、編集モードと表示する-->
<?php if(!empty($_POST['pass_edi']) and ($judge == $_POST['pass_edi'])):?>
		<b><?php echo "編集モードです。"; ?></b>
		<?php endif; ?>

<!-- 編集機能でパスワードが違っていたとき表示する-->
<?php if(!empty($_POST['pass_edi']) and ($judge != $_POST['pass_edi'])):?>
		<font color="red"><?php echo "パスワードが違います。"; ?></font>
		<?php endif; ?>

<!formタグで入力フォームを作成する>
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
//var_dump($bangou);
//書き込んだ2-2.txtをまとめて表示する
//echo "まとめて表示<br />";
//readfile($filename);
//readfile($filename2);
//foreachで表示させる
//echo "foreachで2-2.txtを表示<br />";
//var_dump($_POST['hidden'])."<br />";
$hyoujitest = file('kadai2-2.txt');
//var_dump($hyoujitest)."<br />";
foreach($hyoujitest as $hyouji){
	//var_dump($hyouji);
	//文字列'*****'が$hyoujiに含まれていない場合,配列を改行して表示する
	if(strpos($hyouji,"*****") === false){
		echo $hyouji."<br />";
	}
}
//初期化
$hyoujitest =	array();
//echo $hid_edi2."<br />";
//var_dump($_POST['hid_edi']);
//var_dump($_POST['pass_edi']);
//var_dump($hidden);

/*//POSTにxxxが残っている場合出力して削除
if (isset($_SESSION)){
	//確認後は削除
	unset($_SESSION['name']);
	unset($_SESSION['comment']);
	unset($_SESSION['del_num']);
	unset($_SESSION['edi_num']);
}
*/
?>