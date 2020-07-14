<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php
    // DB接続設定
    $dsn = '****';
    $user = '****';
    $password = '****';
    $pdo = new PDO($dsn,$user,$password);
	
	#テーブルの作成
	$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME,"
	. "password char(16)"
	.");";
	$stmt = $pdo->query($sql);
	
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $date=date("Y/m/d H:i:s");
    $password=$_POST["password"];
    $delete=$_POST["delete"];
    $edit=$_POST["edit"];
    $edit_num=$_POST["edit_num"];
    $submit=$_POST["submit"];
    
    if($submit=="送信" && empty($edit_num)==true){

	    $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
	    $sql -> execute();
            
    }elseif($submit=="送信" && empty($edit_num)==false){
        
        $id = $edit_num; //変更する投稿番号
	    $sql = 'UPDATE tbtest SET name=:name,comment=:comment,password=:password,date=:date WHERE id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
	    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
	    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
	    $stmt -> execute();
                
    }elseif($submit=="削除"){#削除
    
        $id = $delete;
	    $sql ='DELETE FROM tbtest WHERE id=:id and password=:password';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
	    $stmt->execute();
	    
	    $sql = 'SELECT id,password FROM tbtest';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
	    foreach ($results as $row){
            if($row['id']==$id && $row['password']!=$password){
	            $error=1;
	        }
	    }
            
    }elseif($submit=="編集"){#編集
    
        $id =$edit;
        $sql = 'SELECT * FROM tbtest';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
	    foreach ($results as $row){
	        if ($row['id']==$id && $row['password']==$password){
	            $edit_num=$row['id'];
	            $edit_name=$row['name'];
	            $edit_comment=$row['comment'];
	        }else if($row['id']==$id && $row['password']!=$password){
	            $error=1;
	        }
	    }
	    
    }
?>  
    <h2>きのこ派？たけのこ派？コメントしてね！</h2>
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前"<?php if($submit=="編集"){echo "value='".$edit_name."'";}?> required><br>
        <input type="text" name="comment" placeholder="コメント" <?php if($submit=="編集"){echo "value='".$edit_comment."'";}?> required>
        <input type="hidden" name="edit_num"<?php if($submit=="編集"){echo "value='".$edit_num."'";}?>>
        <br><input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="submit" value="送信">
    </form>
    <form  action="" method="post">
        <br>
        <input type="text" name="edit" placeholder="編集番号" required>
        <br><input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="submit" value="編集">
    </form>    
    <form  action="" method="post">
        <br>
        <input type="text" name="delete" placeholder="削除番号"required>
        <br><input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="submit"  value="削除">
        <br><br>
    </form>
    <?php
        #すべて表示する
	    $sql = 'SELECT * FROM tbtest';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
	    foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		    echo $row['id'].'<>';
		    echo $row['name'].'<>';
		    echo $row['comment'].'<>';
		    echo $row['date']."<br>";
	        echo "<hr>";
	    }
        if($error==1){
            echo "<br><font color='red'>パスワードが違います。</font><br>";
        }
    ?>
</body>
</html>