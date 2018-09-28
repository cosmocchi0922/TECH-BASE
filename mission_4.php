<?php

		$name = $_POST['name'];
		$comment = $_POST['comment'];
		$delete = $_POST['delete'];
		$edit = $_POST['edit'];
		$editt = $_POST['editt'];
		$pass=$_POST['pass1'];
		$date = date("Y年m月d日 H時i分s秒");
		
					
	//STEP1 データベースへ接続
		$dbname = 'データベース名'; 
		$host = 'ホスト名';
		$user = 'ユーザー名';
		$password = 'パスワード';
		$dsn = 'mysql:dbname='.$dbname.'; host='.$host.'; charset=utf8';
		try {
			$pdo = new PDO($dsn,$user,$password);
		} catch(PDOException $e) {
			echo('Connection failed:'.$e->getMessage());
		 	die();
		}
				
	//STEP2 データベース内にテーブルを作成する
		$sql= "CREATE TABLE mission4"
			." ("
			. "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
			. "name char(32),"
			. "comment TEXT,"
			. "date TEXT"
			.");";
		$stmt = $pdo->query($sql);
		
		$sql= "CREATE TABLE pass"
			." ("
			//. "id INT PRIMARY KEY,"
			. "pass TEXT"
			.");";
		$stmt = $pdo->query($sql);
		
	/*
	//テーブル削除
		$sql ='DROP TABLE mission4';
		$result = $pdo -> query($sql);
	*/
	
	/*					
	//STEP3 テーブルが作成できたか確認（テーブル一覧を表示するコマンドを使用）
		$sql ='SHOW TABLES';
		$result = $pdo -> query($sql);
		foreach ($result as $row){
			echo $row[0];
			echo '<br>';
		}
		echo "<hr>";
						
	//STEP4 意図した内容のテーブルか確認（テーブルの中身を確認するコマンドを使用）
		$sql ='SHOW CREATE TABLE mission4';
		$result = $pdo -> query($sql);
		foreach ($result as $row){
			print_r($row);
		}
		echo "<hr>";
		
		$sql ='SHOW CREATE TABLE pass';
		$result = $pdo -> query($sql);
		foreach ($result as $row){
			print_r($row);
		}
		echo "<hr>";
	*/

	
// mysqlのデータベース に書き込む
	
		if (!empty($_POST['name'])) {
				if (!empty($_POST['comment'])) {		//名前とコメントが入力されている時
						if (!empty($_POST['editt'])) {                             //編集実行モード
							
							//STEP7 データ編集(update)
								$sql = "update mission4 set name='$name', comment='$comment' where id = $editt";
								$result = $pdo->query($sql);
					
						}
						else{																	//通常書き込みモード
							if (!empty($_POST['pass1'])) {			//パスワードが入力されている時
							
								/*(idごとに)パスワード保存*/
									$sql = "update pass set pass='$pass'";
									$result = $pdo->query($sql);
										
								//STEP5 データ入力(insert)
									$sql = $pdo -> prepare("INSERT INTO mission4 (name, comment, date) VALUES (:name, :comment, :date)");
									$sql -> bindParam(':name', $name, PDO::PARAM_STR);
									$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
									$sql -> bindParam(':date', $date, PDO::PARAM_STR);
									$sql -> execute();	
									
								}
								else {												//パスワードが入力されていない時
										echo 'パスワードを入力してください！⚠️';
								}
																	
						}
				}
		}

		if (!empty($_POST['delete'])) {
				if (!empty($_POST['pass2'])) {				//パスワードが入力されている時
				
							/*保存してあるパスワード読み込み*/
							$sql = 'SELECT * FROM pass';
							$password = $pdo -> query($sql);
							foreach ($password as $pw) {
								if($pw['pass'] == $_POST['pass2']) {			//⇨パスワード照合後、合っていたら削除可能
									//STEP8 データ削除(delete)
										$sql = "delete from mission4 where id=$delete"; 
										$result = $pdo->query($sql);
										
										$sql = "ALTER TABLE mission4 AUTO_INCREMENT=$delete";
										$results = $pdo->query($sql);
								}
								else {														//⇨パスワード照合後、間違っていた時
										echo 'パスワードが違います！⚠️';
								}
							}
							
				}
				else {												//パスワードが入力されていない時
						echo 'パスワードを入力してください！⚠️';
				}
		}
		
		if (!empty($_POST['edit'])) {                //編集選択モード
				if (!empty($_POST['pass3'])) {						//パスワードが入力されている時
			
							/*保存してあるパスワード読み込み*/
							$sql = 'SELECT * FROM pass';
							$password = $pdo -> query($sql);
							foreach ($password as $pw) {
								if($pw['pass'] == $_POST['pass3']) {				//⇨パスワード照合後、合っていたら編集可能
											$sql = "select * from mission4 where id=$edit";
											$result = $pdo -> query($sql);
											foreach ($result as $row){
													$edit2 = $row['id'];
													$name2 = $row['name'];
													$comment2 = $row['comment'];
											}
								}
								else {														//⇨パスワード照合後、間違っていた時
										echo 'パスワードが違います！⚠️';
								}
							}
				}
				else {												//パスワードが入力されていない時
						echo 'パスワードを入力してください！⚠️';
				}
				
		}
		
?>


<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="utf-8">
</head>

<body>
	<form action = "http://tt-308.99sv-coco.com/mission_4.php"  method="post">
		<div>
			<input type = "text" name = "name" placeholder = "名前" value = "<?php echo $name2; ?>" ><br>
			<input type = "text" name = "comment" placeholder = "コメント" value = "<?php echo $comment2; ?>" >
			<input type = "hidden" name = "editt" value = "<?php echo $edit2; ?>" ><br>
			<input type = "text" name = "pass1" placeholder = "パスワード">
			<button type = "submit">送信</button><br><br>
			<input type = "text" name = "delete" placeholder ="削除対象番号"><br>
			<input type = "text" name = "pass2" placeholder = "パスワード">
			<button type = "submit">削除</button><br><br>
			<input type = "text" name = "edit" placeholder ="編集対象番号"><br>
			<input type = "text" name = "pass3" placeholder = "パスワード">
			<button type = "submit">編集</button>
		</div>
	</form>
</body>

</html>
		


<?php

// mysqlのデータベース の中身をフォームに表示させる

	//STEP6 データ表示(select)
		$sql = 'SELECT * FROM mission4 ORDER BY id';
		$results = $pdo -> query($sql);
		foreach ($results as $row){
			$sum = $row['id'].' '.$row['name'].' '.$row['comment'].' '.$row['date'];
		 	echo $sum.'<br>';
		}
	
?>

