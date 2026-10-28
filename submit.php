<?php require 'config.php'; require_login(); $u=current_user();
if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
$aid=(int)($_POST['attempt_id']??0);
$st=db()->prepare('SELECT a.*,t.passing_percent,t.negative_marking FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.id=? AND a.user_id=? AND a.status="in_progress"'); $st->execute([$aid,$u['id']]); $a=$st->fetch();
if(!$a){ redirect('dashboard.php'); }
// Server-side scoring ONLY: correct answers are read here, never sent to browser before.
$qs=db()->prepare('SELECT q.id,q.marks, (SELECT id FROM options WHERE question_id=q.id AND is_correct=1 LIMIT 1) correct_id FROM questions q WHERE q.test_id=?'); $qs->execute([$a['test_id']]); $questions=$qs->fetchAll();
$ans=db()->prepare('SELECT * FROM student_answers WHERE attempt_id=?'); $ans->execute([$aid]); $map=[]; foreach($ans->fetchAll() as $r) $map[$r['question_id']]=$r;
$obtained=0; $total=0; $correct=0; $wrong=0; $unanswered=0;
foreach($questions as $q){
  $total+=(float)$q['marks']; $sel=$map[$q['id']]['selected_option_id']??null;
  if($sel===null){ $unanswered++; $mark=0; $isc=0; }
  elseif((int)$sel===(int)$q['correct_id']){ $correct++; $mark=(float)$q['marks']; $isc=1; }
  else{ $wrong++; $mark=-(float)$a['negative_marking']; $isc=0; }
  $obtained+=$mark;
  db()->prepare('UPDATE student_answers SET is_correct=?,marks_awarded=? WHERE attempt_id=? AND question_id=?')->execute([$isc,$mark,$aid,$q['id']]);
}
if($obtained<0) $obtained=0;
$pct=$total>0?round($obtained/$total*100,2):0; $passed=$pct>=(int)$a['passing_percent']?1:0;
db()->prepare('UPDATE attempts SET status="completed",submitted_at=NOW(),obtained_marks=?,total_marks=?,percentage=?,passed=? WHERE id=?')->execute([$obtained,$total,$pct,$passed,$aid]);
if($passed){
  $code='CERT-'.strtoupper(bin2hex(random_bytes(5)));
  $t=db()->prepare('SELECT title FROM tests WHERE id=?'); $t->execute([$a['test_id']]); $title=$t->fetch()['title'];
  try{ db()->prepare('INSERT INTO certificates(user_id,attempt_id,code,title) VALUES(?,?,?,?)')->execute([$u['id'],$aid,$code,$title]); }catch(Exception $e){}
}
redirect('result.php?attempt_id='.$aid);
