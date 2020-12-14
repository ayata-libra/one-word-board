<?php

    // DB接続設定
    $dsn = 'mysql:dbname=tb221049db;host=localhost';
    $user = 'tb-221049';
    $password = 'RBgfAFVawP';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS msgtable" //テーブルを作成（CREATE構文）
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY," //idは自動設定かつプライマリーキーを使い識別
        . "name varchar(32)," //nameカラムは32文字以内可変で保存
        . "comment TEXT," //テキスト型
        . "post_date DATETIME," //時刻型
        . "pass char(16)" //16文字固定で保存
        .");";
    $stmt = $pdo->query($sql);

    // //テーブルごと消して再作成する
	// $sql = 'truncate table msgtable';
	// $stmt = $pdo->prepare($sql);
	// $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	// $stmt->execute();
    
    $edit_number=NULL; //変数を全て初期化する
    $edit_name=NULL;
    $edit_comment=NULL;
    $edit_pass=NULL;
    $post_date=NULL;

    //編集フォームに入力されたとき
    if(!empty($_POST["edit_id"]) && !empty($_POST["editpass"])){ //postしたidとパスワードが空でない（否定演算子！）とき
        $edit_num=$_POST["edit_id"]; //値を変数にうつす
        $editpass = $_POST["editpass"]; //値を変数にうつす

        $sql = 'SELECT * FROM msgtable'; //全体を取得
        $stmt = $pdo->query($sql); //$sqlに入ったクエリを$pdoに対して実行する
        $results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする
        foreach ($results as $row){
            if($row['pass']==$editpass && $row['id']==$edit_num){ //パスワードとidが一致したものがある時deleteを実行
                $edit_number=$row[0]; //変数を用意することでhtmlでフォーム内に埋め込む
                $edit_name=$row[1];
                $edit_comment=$row[2];
                $edit_pass=$row[4];
            }
        }
    }
    //投稿フォームの編集用番号に入力された時
    else if(!empty($_POST["getName"]) && !empty($_POST["text"]) && !empty($_POST["post_edit_id"]) && !empty($_POST["pass"])){
        $name=$_POST["getName"]; //値を変数にうつす
        $comment=$_POST["text"]; //値を変数にうつす
        $edit_number=$_POST["post_edit_id"]; //値を変数にうつす
        $post_date=date('Y/m/d H:i:s'); //日付を変数に
        $editpass = $_POST["pass"]; //値を変数にうつす
        // $writeData =  "$edit_number<>$name<>$comment<>$date<>$editpass<>";
        // echo $writeData;
        
        //ファイル読み込み関数で、ファイルの中身を1行1要素として配列変数に代入する。
        // if(file_exists($filename)){ //$filenameが存在する場合
        //     $lines=file($filename,FILE_IGNORE_NEW_LINES); //file()で中身を配列化して変数にぶちこむ

        //     $fp=fopen($filename,"w"); //fopen()で$filenameのファイルがあるか探す、なければ作成を試みる、丸ごと編集なので書き換えモード

        //     foreach($lines as $line){ 
        //         $linearray = explode("<>",$line); //$lineの<>を区切りとして配列化、投稿番号を取得できる
        //         if($edit_number == $linearray[0]){ //投稿番号と編集対象番号を比較、一致したら入力された値に変換
        //             $line = $writeData;
        //             fwrite($fp,$line . PHP_EOL); //$lineを書き足す
        //         }else{
        //             fwrite($fp,$line . PHP_EOL); //$lineを書き足す
        //         }
        //     }
        //     fclose($fp); //ファイルを閉じる
        // } 

        $sql = 'SELECT * FROM msgtable'; //全体を取得
        $stmt = $pdo->query($sql); //$sqlに入ったクエリを$pdoに対して実行する
        $results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする
        foreach ($results as $row){
            if($row['id']==$edit_number){ //パスワードとidが一致したものがある時updateを実行
                $sql = 'update msgtable set name=:name,comment=:comment,post_date=:post_date where id=:id '; //idが:idに入っている値のカラムを削除
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':post_date', $post_date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT); //:idに$edit_numberを挿入
                $stmt->execute();
            }
        }

    }
    //投稿フォームに入力されたとき
    else if(!empty($_POST["getName"]) && !empty($_POST["text"]) && empty($_POST["post_edit_id"]) && !empty($_POST["pass"])){ //postしたtextとgetNameとpost_edit_idが空でない（否定演算子！）とき
        $post_date=date('Y/m/d H:i:s'); //日付を変数に

        $sql = $pdo -> prepare("INSERT INTO msgtable (name, comment,post_date,pass) VALUES (:name, :comment, :post_date, :pass)"); //prepare関数でインサート、実行はexecuteで行うので忘れずに
        $sql -> bindParam(':name', $name, PDO::PARAM_STR); //bindParam文字列をバインドする
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //上でテーブル名のそれぞれに対してパラメータを与える
        $sql -> bindValue(':post_date', $post_date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $name=$_POST["getName"]; //値を変数にうつす
        $comment=$_POST["text"]; //値を変数にうつす
        $pass=$_POST["pass"]; //値を変数にうつす
        $sql -> execute(); //ここでprepare関数が実施される。
    }
    //削除フォームに入力されたとき
    else if(!empty($_POST["delete_id"]) && !empty($_POST["delpass"])){ //postしたidが空でない（否定演算子！）とき
        $delete_num=$_POST["delete_id"]; //値を変数にうつす
        $delpass = $_POST["delpass"]; //値を変数にうつす 
        
        $sql = 'SELECT * FROM msgtable'; //全体を取得
        $stmt = $pdo->query($sql); //$sqlに入ったクエリを$pdoに対して実行する
        $results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする
        foreach ($results as $row){
            if($row['pass']==$delpass && $row['id']==$delete_num){ //パスワードとidが一致したものがある時deleteを実行
                $sql = 'delete from msgtable where id=:id '; //idが:idに入っている値のカラムを削除
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT); //:idに$delete_numを挿入
                $stmt->execute();
            }
        }
    }  
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>一言掲示板</title>
</head>
<body>
    <!--投稿フォーム -->
    <div class=form-title>【  投稿フォーム  】</div>
    <form action="" method="post">
        <table>
            <tr>
                <td>名前:</td>
                <td><input type="text" name="getName" value="<?php echo "$edit_name";?>"></td>
            </tr>
            <tr>
                <td>コメント:</td>
                <td><input type="text" name="text" value="<?php echo "$edit_comment";?>"></td>
            </tr>
            <tr>
                <!-- <td>編集用番号:</td> -->
                <td><input type="hidden" name="post_edit_id" value="<?php echo "$edit_number";?>"></td>
            </tr>
            <tr>
                <td>パスワード:</td>
                <td><input type="password" name="pass" value="<?php echo "$edit_pass";?>"></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="送信"></td>
            </tr>

        </table>
    </form>

    <!--削除フォーム -->
    <div class=form-title>【  削除フォーム  】</div>
    <form action="" method="post">
        <table>
            <tr>
                <td>投稿番号:</td>
                <td><input type="number" name="delete_id"></td>
            </tr>
            <tr>
                <td>パスワード:</td>
                <td><input type="password" name="delpass" value=""></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="送信"></td>
            </tr>
        </table>
    </form>

    <!--編集フォーム -->
    <div class=form-title>【  編集フォーム  】</div>
    <form action="" method="post">
        <table>
            <tr>
                <td>投稿番号:</td>
                <td><input type="number" name="edit_id"></td>
            </tr>
            <tr>
                <td>パスワード:</td>
                <td><input type="password" name="editpass" value=""></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="送信"></td>
            </tr>
        </table>
        <div>---------------------------------------</div>
        <div class=form-title>【  投稿一覧  】</div>
    </form> 

    <!-- msgtableに書き込まれた配列を表示する。-->
    <?php
        //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
        $sql = 'SELECT * FROM msgtable';
        $stmt = $pdo->query($sql); //$sqlに入ったクエリを$pdoに対して実行する
        $results = $stmt->fetchAll();
        // print_r($results);
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['post_date'].'<br>';
        echo "<hr>";
        }
    ?>

</body>
</html>

<style>

</style>

