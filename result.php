<?php require 'config.php'; require_login(); $user=current_user();
if(isset($_GET['download'])){
 $aid=(int)($_GET['attempt_id']??0);
 $st=db()->prepare('SELECT a.*,t.title FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.id=? AND a.user_id=? AND a.status="completed"'); $st->execute([$aid,$user['id']]); $a=$st->fetch();
 if(!$a){ http_response_code(404); die('Result not found.'); }
 $rows=db()->prepare('SELECT q.question_text,q.explanation, sa.selected_option_id, sa.is_correct, sa.marks_awarded, (SELECT option_text FROM options WHERE id=sa.selected_option_id) sel, (SELECT option_text FROM options WHERE question_id=q.id AND is_correct=1 LIMIT 1) corr FROM questions q LEFT JOIN student_answers sa ON sa.question_id=q.id AND sa.attempt_id=? WHERE q.test_id=? ORDER BY q.sort_order,q.id'); $rows->execute([$aid,$a['test_id']]); $rows=$rows->fetchAll();
 $txt="Online Testing - Result\nTest: ".$a['title']."\nMode: ".$a['mode']."\nMarks: ".$a['obtained_marks']." / ".$a['total_marks']."\nPercentage: ".$a['percentage']."%\nStatus: ".($a['passed']?'PASS':'FAIL')."\n\n";
 foreach($rows as $i=>$r){ $txt.=($i+1).". ".$r['question_text']."\n  Your answer: ".($r['sel']??'Unanswered')."\n  Correct answer: ".$r['corr']."\n  Marks: ".$r['marks_awarded']."\n"; if($r['explanation']) $txt.="  Explanation: ".$r['explanation']."\n"; $txt.="\n"; }
 header('Content-Type: text/plain; charset=utf-8'); header('Content-Disposition: attachment; filename="result-'.$aid.'.txt"'); echo $txt; exit;
}
$page_title='Result'; include 'includes/header.php';
$aid=(int)($_GET['attempt_id']??0); $st=db()->prepare('SELECT a.*,t.title FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.id=? AND a.user_id=? AND a.status="completed"'); $st->execute([$aid,$user['id']]); $a=$st->fetch();
if(!$a){ echo '<p>Result not found.</p>'; include 'includes/footer.php'; exit; }
$rows=db()->prepare('SELECT q.id AS qid,q.question_text,q.explanation,q.marks, sa.selected_option_id, sa.is_correct, sa.marks_awarded, (SELECT option_text FROM options WHERE id=sa.selected_option_id) sel, (SELECT option_text FROM options WHERE question_id=q.id AND is_correct=1 LIMIT 1) corr FROM questions q LEFT JOIN student_answers sa ON sa.question_id=q.id AND sa.attempt_id=? WHERE q.test_id=? ORDER BY q.sort_order,q.id'); $rows->execute([$aid,$a['test_id']]); $rows=$rows->fetchAll();
$unans=0; foreach($rows as $r) if($r['selected_option_id']===null) $unans++;
?>
<h1>Result: <?= e($a['title']) ?></h1>
<?php $pct=(float)$a['percentage']; $circ=2*3.14159*52; $off=$circ*(1-$pct/100); ?>
<div class="card ring-wrap"><svg class="ring" viewBox="0 0 120 120"><circle class="ring-bg" cx="60" cy="60" r="52" fill="none" stroke-width="12"/><circle class="ring-fg" cx="60" cy="60" r="52" fill="none" stroke-width="12" stroke-dasharray="<?= $circ ?>" stroke-dashoffset="<?= $off ?>"/><text x="60" y="58" text-anchor="middle" font-size="22" font-weight="800"><?= e($a['percentage']) ?>%</text><text x="60" y="78" text-anchor="middle" font-size="12"><?= $a['passed']?t('pass'):t('fail') ?></text></svg>
<div><p><b><?= t('marks') ?>:</b> <?= e($a['obtained_marks']) ?> / <?= e($a['total_marks']) ?> · <b><?= t('status') ?>:</b> <?= $a['passed']?'<span class="badge">'.t('pass').'</span>':'<span class="badge warn">'.t('fail').'</span>' ?></p>
<p>Correct: <?= count(array_filter($rows,fn($r)=>$r['is_correct'])) ?> · Incorrect: <?= count(array_filter($rows,fn($r)=>$r['selected_option_id']!==null&&!$r['is_correct'])) ?> · Unanswered: <?= $unans ?> · Mode: <?= e($a['mode']) ?></p>
<a class="btn" href="result.php?attempt_id=<?= $aid ?>&download=1">⬇ <?= t('download_result') ?></a> <button class="btn2" onclick="window.print()">🖨 <?= t('print_result') ?></button></div></div>
<h2><?= t('answers_explanations') ?></h2>
<?php foreach($rows as $i=>$r): ?><div class="card"><h3>Q<?= $i+1 ?>. <?= e($r['question_text']) ?></h3><p><a href="bookmark.php?question_id=<?= $r['qid'] ?>&back=<?= urlencode('result.php?attempt_id='.$aid) ?>">🔖 Bookmark this question</a></p><p><?= t('your_answer') ?>: <b><?= e($r['sel']??'— Unanswered —') ?></b> · <?= t('correct_answer') ?>: <b><?= e($r['corr']) ?></b> · <?= t('marks') ?>: <?= e($r['marks_awarded']) ?>/<?= e($r['marks']) ?></p><?php if($r['explanation']): ?><p class="muted"><?= t('explanation') ?>: <?= e($r['explanation']) ?></p><?php endif; ?></div><?php endforeach; ?>
<?php include 'includes/footer.php'; ?>
