<?php
  //データベースに接続する
  require('dbconnect.php');

  $sql ='UPDATE `tweets` SET `delete_flag` =1 WHERE `tweet_id`='.$_REQUEST['tweet_id'];
  
  $tweets = mysqli_query($db,$sql) or die(mysqli_error($db));

	header("Location: index.php");
	exit;

?>

