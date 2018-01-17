<?php

/*-----------------データベースへの接続---------------*/
$dsn	=	'mysql:dbname=co_553_it_99sv_coco_com;host=localhost';
$user	=	'co-553.it.99sv-c';
$dbpw	=	'9jNbywA';
$pdo	=	new PDO($dsn,$user,$dbpw);
/*-----------------データベースへの接続---------------*/

//IDを取得
$id	=	$_GET['id'];

//テーブルからデータを取得
$sql		=	"SELECT * FROM crane_game2 WHERE id = :id;";
$stmt		=	$pdo->prepare($sql);
$stmt		->	bindValue(":id", $id, PDO::PARAM_INT);
$stmt		->	execute();
$row	=	$stmt	->	fetch(PDO::FETCH_ASSOC);
header("Content-Type: ".$row['file_exte']);
echo $row['file_data'];



?>