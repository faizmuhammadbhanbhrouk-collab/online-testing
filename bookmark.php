<?php require 'config.php'; require_login(); $u=current_user();
$qid=(int)($_GET['question_id']??0); $back=$_GET['back']??'dashboard.php';
if($qid){ try{ db()->prepare('INSERT INTO bookmarks(user_id,question_id) VALUES(?,?)')->execute([$u['id'],$qid]); $_SESSION['flash']='Question bookmarked.'; }catch(Exception $e){ $_SESSION['flash']='Already bookmarked.'; } }
redirect($back);
