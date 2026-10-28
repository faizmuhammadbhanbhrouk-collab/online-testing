<?php $page_title='Test'; include 'includes/header.php'; require_login();
$id=(int)($_GET['id']??0); $st=db()->prepare('SELECT t.*,cl.name clsname,sub.name subname,cat.name catname FROM tests t LEFT JOIN classes cl ON cl.id=t.class_id LEFT JOIN subjects sub ON sub.id=t.subject_id LEFT JOIN categories cat ON cat.id=t.category_id WHERE t.id=? AND t.is_published=1'); $st->execute([$id]); $t=$st->fetch();
if(!$t){ echo '<p>Test not found or not published.</p>'; include 'includes/footer.php'; exit; }
$cnt=db()->prepare('SELECT COUNT(*) c FROM questions WHERE test_id=?'); $cnt->execute([$id]); $n=$cnt->fetch()['c'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $mode=in_array($_POST['mode']??'',['practice','exam'],true)?$_POST['mode']:'practice';
 // Duplicate active attempt prevention: resume existing in-progress attempt for same mode, else create one
 $ex=db()->prepare("SELECT id FROM attempts WHERE user_id=? AND test_id=? AND mode=? AND status='in_progress' ORDER BY id DESC LIMIT 1"); $ex->execute([$user['id'],$id,$mode]); $old=$ex->fetch();
 if($old) redirect('take.php?attempt_id='.$old['id']);
 $qs=db()->prepare('SELECT id FROM questions WHERE test_id=? ORDER BY sort_order,id'); $qs->execute([$id]); $ids=array_column($qs->fetchAll(),'id'); shuffle($ids);
 // Shuffle options per question and store in question_order JSON: {order:[qid...], options:{qid:[oid...]}}
 $order=['order'=>$ids,'options'=>[]];
 foreach($ids as $qid){ $os=db()->prepare('SELECT id FROM options WHERE question_id=? ORDER BY sort_order,id'); $os->execute([$qid]); $oids=array_column($os->fetchAll(),'id'); shuffle($oids); $order['options'][$qid]=$oids; }
 $ins=db()->prepare('INSERT INTO attempts(user_id,test_id,mode,question_order) VALUES(?,?,?,?)'); $ins->execute([$user['id'],$id,$mode,json_encode($order)]);
 redirect('take.php?attempt_id='.db()->lastInsertId());
}
?>
<h1><?= e($t['title']) ?></h1><div class="card"><p><?= e($t['description']) ?></p>
<p><b><?= t('questions') ?>:</b> <?= (int)$n ?> · <b><?= t('duration') ?>:</b> <?= (int)$t['duration_minutes'] ?> <?= t('minutes') ?> · <b><?= t('difficulty') ?>:</b> <?= e($t['difficulty']) ?> · <b><?= t('pass_mark') ?>:</b> <?= (int)$t['passing_percent'] ?>% · <b>Negative marking:</b> <?= e($t['negative_marking']) ?> <?php if($t['is_demo']) echo '<span class="badge demo">DEMO</span>'; ?></p>
<p class="muted"><?= t('class') ?>: <?= e($t['clsname']??'—') ?> · <?= t('subject') ?>: <?= e($t['subname']??'—') ?> · <?= t('category') ?>: <?= e($t['catname']??'—') ?><?php if(!empty($t['chapter'])) echo ' · '.e(t('chapter')).': '.e($t['chapter']); ?></p>
<p><b>Practice Mode:</b> learn at your pace, explanations after submission. <b>Exam Mode:</b> timed, same scoring rules.</p>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Mode</label><select name="mode"><option value="practice">Practice Mode</option><option value="exam">Exam Mode</option></select>
<button class="btn">Start Test</button></form></div>
<?php include 'includes/footer.php'; ?>
