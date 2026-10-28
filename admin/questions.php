<?php include 'header.php';
$test_id=(int)($_GET['test_id']??$_POST['test_id']??0);
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete'])){ db()->prepare('DELETE FROM questions WHERE id=?')->execute([(int)$_POST['id']]); }
 else{
  $text=trim($_POST['question_text']??''); $type=$_POST['question_type']??'mcq'; $marks=(float)($_POST['marks']??1); $expl=trim($_POST['explanation']??''); $opts=$_POST['options']??[]; $corr=(int)($_POST['correct']??0);
  if($text && count(array_filter($opts))>=2){
   if($_POST['id']){ db()->prepare('UPDATE questions SET question_text=?,question_type=?,marks=?,explanation=? WHERE id=?')->execute([$text,$type,$marks,$expl,(int)$_POST['id']]); $qid=(int)$_POST['id']; db()->prepare('DELETE FROM options WHERE question_id=?')->execute([$qid]); }
   else{ db()->prepare('INSERT INTO questions(test_id,question_text,question_type,marks,explanation,sort_order) VALUES(?,?,?,?,?,0)')->execute([$test_id,$text,$type,$marks,$expl]); $qid=db()->lastInsertId(); }
   $i=0; foreach($opts as $o){ $o=trim($o); if($o==='') continue; db()->prepare('INSERT INTO options(question_id,option_text,is_correct,sort_order) VALUES(?,?,?,?)')->execute([$qid,$o,($i===$corr)?1:0,$i]); $i++; }
   $_SESSION['flash']='Question saved.';
  } else $_SESSION['error']='Question text and at least 2 options are required.';
 }
 redirect('questions.php?test_id='.$test_id);
}
$tests=db()->query("SELECT id,title FROM tests ORDER BY id DESC")->fetchAll();
$qs=[]; if($test_id){ $s=db()->prepare('SELECT * FROM questions WHERE test_id=? ORDER BY id'); $s->execute([$test_id]); $qs=$s->fetchAll(); }
$edit=null; if(isset($_GET['edit'])){ $s=db()->prepare('SELECT * FROM questions WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); $test_id=$edit['test_id']; $os=db()->prepare('SELECT * FROM options WHERE question_id=? ORDER BY sort_order'); $os->execute([$edit['id']]); $edit_opts=$os->fetchAll(); }
?>
<h1>Manage Questions</h1>
<form method="get"><label>Test</label><select name="test_id" onchange="this.form.submit()"><option value="0">Choose a test</option><?php foreach($tests as $t): ?><option value="<?= $t['id'] ?>" <?= $test_id==$t['id']?'selected':'' ?>><?= e($t['title']) ?></option><?php endforeach; ?></select></form>
<?php if($test_id): ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $edit['id']??'' ?>"><input type="hidden" name="test_id" value="<?= $test_id ?>">
<label>Question</label><textarea name="question_text" required><?= e($edit['question_text']??'') ?></textarea>
<label>Type</label><select name="question_type"><option value="mcq" <?= ($edit['question_type']??'')==='mcq'?'selected':'' ?>>MCQ</option><option value="tf" <?= ($edit['question_type']??'')==='tf'?'selected':'' ?>>True/False</option></select>
<label>Marks</label><input type="number" step="0.5" name="marks" value="<?= e($edit['marks']??1) ?>">
<label>Options (tick the correct one by its position below)</label>
<?php for($i=0;$i<4;$i++): ?><input name="options[]" value="<?= e($edit_opts[$i]['option_text']??'') ?>" placeholder="Option <?= $i+1 ?>"><?php endfor; ?>
<label>Correct option number (1-4)</label><input type="number" name="correct" min="0" max="3" value="<?php $ci=0; if(isset($edit_opts)) foreach($edit_opts as $k=>$o) if($o['is_correct']) $ci=$k; echo $ci; ?>">
<label>Explanation (shown only after submission)</label><textarea name="explanation"><?= e($edit['explanation']??'') ?></textarea><button class="btn">Save question</button></form>
<table><tr><th>Question</th><th>Type</th><th>Actions</th></tr><?php foreach($qs as $q): ?><tr><td><?= e(mb_substr($q['question_text'],0,120)) ?></td><td><?= e($q['question_type']) ?></td><td><a href="questions.php?test_id=<?= $test_id ?>&edit=<?= $q['id'] ?>">Edit</a> <form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="test_id" value="<?= $test_id ?>"><input type="hidden" name="id" value="<?= $q['id'] ?>"><button class="btn2 danger" name="delete" data-confirm="Delete this question?">Delete</button></form></td></tr><?php endforeach; ?></table><?php endif; ?>
<?php include 'footer.php'; ?>
