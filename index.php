<?php
 session_start();
   


    //ログイン状態のチェック
    //ログインしていると判断できる条件
    //1.セッションにidが入っていること
    //2.最後の行動から1時間以内であること
    if ((isset($_SESSION['login_member_id'])) && ($_SESSION['time'] + 3600 > time()) ){
   
    //ログインしてる
    //セッションの時間を更新
      $_SESSION['time'] = time();

 } else {
  //ログインしてない
  header('Location: login.php');
  exit();
 }


     // dbconnect.phpを読み込む
    require ('dbconnect.php');


    //ログインしている人の情報をチェック（名前を表示）


    //SQLを実行し、ユーザーのデータを取得
    $sql = sprintf('SELECT * FROM `members` WHERE `member_id` = %d',mysqli_real_escape_string ($db,$_SESSION['login_member_id']));


    $record = mysqli_query($db,$sql) or die(mysqli_error);
    $member = mysqli_fetch_assoc($record);

    //DB登録処理
    if (!empty($_POST)) {
      //補足：つぶやきが空っぽではない時だけ、Insertする


    $tweet = htmlspecialchars($_POST['tweet'],ENT_QUOTES,'UTF-8');
    $login_member_id = $_SESSION['login_member_id'];
    $reply_tweet_id = 0;

    $sql = sprintf('INSERT INTO `tweets` (`tweet`, `member_id`, `reply_tweet_id`, `created`, `modified`) VALUES ("%s", "%s", "%s", now(),now());',
    //データベースサニタイズ
    mysqli_real_escape_string($db,$tweet),
    mysqli_real_escape_string($db,$login_member_id),
    mysqli_real_escape_string($db,$reply_tweet_id)
    );
    mysqli_query($db,$sql) or die(mysqli_error($db));
    
    //これをつけると再読み込みでpost送信が発生しなくなる。
    header("Location: index.php");
  exit();
}


 //投稿を取得する
    //$sql = 'SELECT * FROM `tweets`;';
	$sql = 'SELECT `members`.`nick_name`,`members`.`picture_path`,`tweets`.* FROM `tweets` INNER JOIN `members` on `tweets`.`member_id` = `members`.`member_id`';
    $tweets = mysqli_query($db,$sql) or die (mysqli_error($db));

    $tweets_array = array();
    while ($tweet = mysqli_fetch_assoc($tweets)) {
    	$tweets_array[] = $tweet;
    }


?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $member['nick_name']; ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">次</a></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <!-- ここでつぶやいた内容を表示する-->
        <?php foreach ($tweets_array as $tweet_each){
        	
         ?>
        <div class="msg">
       <img src="member_picture/<?php echo $tweet_each['picture_path']; ?>" width="48" height="48">
          <p>
            <?php echo $tweet_each['tweet']; ?><span class="name"> (<?php echo $tweet_each['nick_name']; ?>)
            	</span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              <?php echo $tweet_each['created']; ?>
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
         <?php } ?>  
          <!-- <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
          つぶやき３<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:03
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき２<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:02
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき１<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:01
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
          -->
        </div>
      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>