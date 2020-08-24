<?php
    //⓵データベースの接続
    
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    if(!empty($_POST["e_num"])){
        //$sql =  'SELECT * FROM mission5 WHERE id='.$_POST["e_num"].' AND password="'.$_POST["e_pass"].'"';
        //$sql =  'SELECT * FROM mission5 WHERE id=:id ,password=:password' ;
        //$id = $_POST["e_num"];
        //$pass = $_POST["e_pass"];
        //$stmt = $pdo->prepare($sql);
        //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        //$stmt->bindParam(':password', $pass, PDO::PARAM_STR);
        //$stmt->execute();
        //$results = $stmt->fetchAll();
        //$row = $results[0];
        $sql = 'SELECT * FROM mission5 WHERE id=:id AND password=:password';

        $id=$_POST["e_num"];
        $editpass=$_POST["e_pass"];
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt -> bindParam(':password', $editpass,PDO::PARAM_STR);
        $stmt->execute();                    
        $results = $stmt->fetchAll();
        $row = $results[0];
        //Noticeはパスワードが間違っているときのエラーなだけ
    
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <h1>掲示板</h1>

    <form action="" method="post" >
    <input type="txt" name="name" placeholder="名前"
         value= <?php
                if(!empty($_POST["e_num"])){
                echo $row['name'];}
        ?>><br>

        <input type="text"   name="comment"      placeholder="コメント"
        value=<?php 
                if(!empty($_POST["e_num"])){
                    echo $row["comment"];}//編集する前のコメントを表示
                ?>><br>
        <input type="password"   name="pass"      placeholder="パスワード"><br>
        <input type="hidden" name="ch_num"   
        value=<?php 
                if(!empty($_POST["e_pass"])){
                    echo $_POST["e_num"];}//パスワードが一致したものだけを表示（隠してるけど）
                    ?>><br>
                    
        <input type="submit" name="submit"   value="送信"><br>    
        
        <input type="text"   name="del_num"   placeholder="削除フォーム"><br>
        <input type="password"   name="del_pass"      placeholder="パスワード"><br>
        <input type="submit" name="del_submit"   value="削除"><br>
        
        <input type="text"   name="e_num"   placeholder="編集番号指定用フォーム"><br>
        <input type="password"   name="e_pass"      placeholder="パスワード"><br>
        <input type="submit" name="e_submit"   value="編集">
    </form>
    
    <?php


   
    
    //⓶テーブルの作成
    //テーブル名は[mission5]
   //もしまだこのテーブルが存在しないなら
	$sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    //以下で入れるデータの項目
	. "name char(32),"//名前
    . "comment TEXT,"//コメント
    . "date TEXT,"//日時
    . "password TEXT"//パスワード
	.");";
	$stmt = $pdo->query($sql);

    

    //⓷新規投稿
    if(!empty($_POST["submit"])){ //送信ボタンが押されたら
    //ここを!emptyにすることで「Notice」がなくなる
    //「Notice」：未定義の配列の要素を使用したときにでるエラー
        
        if(empty($_POST["ch_num"])){//strはhiddenのところ
            if(empty($_POST["name"])){
                echo "お名前を入力してください"."<br>";
            }elseif(empty($_POST["comment"])){
                echo "コメントを入力してください"."<br>";
            }elseif(empty($_POST["pass"])){
                echo "パスワードを入力してください"."<br>";
            }else{//新規投稿の場合
                $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment,date,password) VALUES (:name, :comment,:date,:password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date',$date , PDO::PARAM_STR);
                $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
                $name = $_POST["name"]; //ここをいれたらひとつのデータになる
                $comment =  $_POST["comment"];//好きな名前、好きな言葉は自分で決めること
                $date= date("Y/m/d/H:i:s");
                $pass= $_POST["pass"];
                $sql -> execute();
                    echo "投稿されました"."<br>";
            }
           
            //⓸編集機能
            }else{
            
                if(!empty($_POST["pass"])){//パスワードが空でなかったら
                  
                        $id=$_POST["ch_num"];//idは編集する投稿番号
                        $edit_pass=$_POST["pass"];
                        $name = $_POST["name"]; //ここをいれたらひとつのデータになる
                        $comment =  $_POST["comment"];//好きな名前、好きな言葉は自分で決めること
                        $date= date("Y/m/d/H:i:s");

                        $sql =  'UPDATE mission5 SET name=:name,comment=:comment,date=:date WHERE id=:id  AND password=:password';
                        $sql = $pdo -> prepare($sql);
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date',$date , PDO::PARAM_STR);
                        $sql -> bindParam(':password', $edit_pass, PDO::PARAM_STR);
                        $sql -> bindParam(':id', $id, PDO::PARAM_STR);
                        //$name= $_POST["name"];
                        //$comment = $_POST["comment"];
                        $sql -> execute();
                        
                            echo "編集されました<br>";
                    }
                        //echo "パスワードが無効です<br>";
                }//elseの終わり
            }//ifの終わり
                    //echo "パスワードを入力してください";
                
        
    //⓹削除機能
    if(!empty($_POST["del_submit"])){//削除ボタンが押されたら
         if(empty($_POST["del_num"]) && empty($_POST["del_pass"])){
                            echo "削除したい番号を入力してください<br>";
            }//ifの終わり
            elseif(!empty($_POST["del_num"]) && empty($_POST["del_pass"])){
                    echo "削除パスワードを入力してください<br>";
            }//elseifの終わり
            elseif(!empty($_POST["del_num"]) && !empty($_POST["del_pass"])){
                    
                    $delete=$_POST["del_num"];
                    $del_pass=$_POST["del_pass"];
                    
                    $id = $_POST["del_num"];//idは削除する投稿番号
                    $sql = 'delete from mission5 where id=:id and password=:password';
                    $sql = $pdo -> prepare($sql);
                    //$sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    //$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    //$sql -> bindParam(':date',$date , PDO::PARAM_STR);
                    $sql -> bindParam(':password', $del_pass, PDO::PARAM_STR);
                    $sql -> bindParam(':id', $id, PDO::PARAM_STR);
                    //$name= $_POST["name"];
                    //$comment = $_POST["comment"];
                    $sql -> execute();

                        echo "削除が実行されました<br>";
                    }//elseih終わり
                }//elseif終わり
                    
                //}else{//パスワードが間違っていたら
                    //echo "パスワードが無効です<br>";
    
        if(!empty($_POST["e_submit"])){ //編集ボタンが押されたら
                                //編集フォーム（一番うえ）は投稿フォームにもとのコメントと名前を表示するかしないかの判断
                                //ここの部分でやることは、うえの編集フォームに送るかどうかの判断
            
            if(empty($_POST["e_num"])){
                echo "編集したい番号を入力してください<br>";
                }elseif(empty($_POST["e_pass"])){
                    echo "編集パスワードを入力してください<br>";
                }elseif(!empty($_POST["e_num"]) && !empty($_POST["e_pass"])){
                    $edit=$_POST["e_num"];
                    //パスワードが合ってたらhtmlの上の部分に表示させる
                   
                    //echo "パスワードが無効です<br>";
                }//elseの終わり
        
        }//ifの終わり
        
    //⓺ブラウザへの表示
    $sql = 'SELECT * FROM mission5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	    echo $row['id'].',';//ここは一行にidからdateが表示されるイメージ
	    echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
	    echo "<hr>";

        
    }
        
    ?>
</body>
</html>