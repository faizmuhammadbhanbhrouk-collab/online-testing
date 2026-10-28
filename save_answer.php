<?php require 'config.php'; header('Content-Type: application/json'); $u=current_user();
if(!$u || !csrf_check($_POST['csrf']??'')){ http_response_code(403); echo json_encode(['ok'=>false]); exit; }
$aid=(int)($_POST['attempt_id']??0); $qid=(int)($_POST['question_id']??0); $oid=$_POST['option_id']??''; $oid=$oid==='' ? null : (int)$oid; $rev=isset($_POST['marked_review'])?(int)$_POST['marked_review']:null;
$st=db()->prepare('SELECT id FROM attempts WHERE id=? AND user_id=? AND status="in_progress"'); $st->execute([$aid,$u['id']]); if(!$st->fetch()){ echo json_encode(['ok'=>false]); exit; }
if($oid!==null){ $v=db()->prepare('SELECT id FROM options WHERE id=? AND question_id=?'); $v->execute([$oid,$qid]); if(!$v->fetch()){ echo json_encode(['ok'=>false]); exit; } }
$ex=db()->prepare('SELECT id,marked_review FROM student_answers WHERE attempt_id=? AND question_id=?'); $ex->execute([$aid,$qid]); $old=$ex->fetch();
if($old){
  if($oid!==null) db()->prepare('UPDATE student_answers SET selected_option_id=? WHERE id=?')->execute([$oid,$old['id']]);
  if($rev!==null) db()->prepare('UPDATE student_answers SET marked_review=? WHERE id=?')->execute([$rev,$old['id']]);
} else {
  db()->prepare('INSERT INTO student_answers(attempt_id,question_id,selected_option_id,marked_review) VALUES(?,?,?,?)')->execute([$aid,$qid,$oid,$rev??0]);
}
echo json_encode(['ok'=>true]);
