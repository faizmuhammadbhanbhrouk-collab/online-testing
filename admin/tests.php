<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete'])){ db()->prepare('DELETE FROM tests WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['flash']='Test deleted.'; }
 elseif(isset($_POST['toggle'])){ db()->prepare('UPDATE tests SET is_published=1-is_published WHERE id=?')->execute([(int)$_POST['id']]); $_SESSION['flash']='Publish status changed.'; }
 else{
  $f=[trim($_POST['title']??''), preg_replace('/[^a-z0-9-]+/','-',strtolower(trim($_POST['title']??''))).'-'.time(), (int)($_POST['class_id']??0)?:null,(int)($_POST['subject_id']??0)?:null,(int)($_POST['category_id']??0)?:null,trim($_POST['chapter']??''),$_POST['difficulty']??'Medium',(int)($_POST['duration_minutes']??20),(int)($_POST['passing_percent']??40),(float)($_POST['negative_marking']??0),trim($_POST['description']??''),isset($_POST['is_published'])?1:0];
  if($_POST['id']){ db()->prepare('UPDATE tests SET title=?,class_id=?,subject_id=?,category_id=?,chapter=?,difficulty=?,duration_minutes=?,passing_percent=?,negative_marking=?,description=?,is_published=? WHERE id=?')->execute([$f[0],$f[2],$f[3],$f[4],$f[5],$f[6],$f[7],$f[8],$f[9],$f[10],$f[11],(int)$_POST['id']]); $_SESSION['flash']='Test updated.'; }
  else{ db()->prepare('INSERT INTO tests(title,slug,class_id,subject_id,category_id,chapter,difficulty,duration_minutes,passing_percent,negative_marking,description,is_published,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([$f[0],$f[1],$f[2],$f[3],$f[4],$f[5],$f[6],$f[7],$f[8],$f[9],$f[10],$f[11],$user['id']]); $_SESSION['flash']='Test created.'; }
 }
 redirect('tests.php');
}
$tests=db()->query("SELECT t.*,c.name cat FROM tests t LEFT JOIN categories c ON c.id=t.category_id ORDER BY t.id DESC")->fetchAll();
$cls=db()->query("SELECT * FROM classes ORDER BY grade")->fetchAll(); $subs=db()->query("SELECT * FROM subjects ORDER BY name")->fetchAll(); $cats=db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$edit=null; if(isset($_GET['edit'])){ $s=db()->prepare('SELECT * FROM tests WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
?>
<h1>Manage Tests</h1>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $edit['id']??'' ?>">
<label>Title</label><input name="title" value="<?= e($edit['title']??'') ?>" required>
<label>Class</label><select name="class_id"><option value="0">Any</option><?php foreach($cls as $c): ?><option value="<?= $c['id'] ?>" <?= ($edit['class_id']??0)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<label>Subject</label><select name="subject_id"><option value="0">Any</option><?php foreach($subs as $c): ?><option value="<?= $c['id'] ?>" <?= ($edit['subject_id']??0)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<label>Category</label><select name="category_id"><option value="0">Any</option><?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>" <?= ($edit['category_id']??0)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<label>Chapter</label><input name="chapter" value="<?= e($edit['chapter']??'') ?>">
<label>Difficulty</label><select name="difficulty"><option>Easy</option><option <?= ($edit['difficulty']??'')==='Medium'?'selected':'' ?>>Medium</option><option <?= ($edit['difficulty']??'')==='Hard'?'selected':'' ?>>Hard</option></select>
<label>Duration (minutes)</label><input type="number" name="duration_minutes" value="<?= (int)($edit['duration_minutes']??20) ?>" required>
<label>Passing %</label><input type="number" name="passing_percent" value="<?= (int)($edit['passing_percent']??40) ?>" required>
<label>Negative marking per wrong answer</label><input type="number" step="0.25" name="negative_marking" value="<?= e($edit['negative_marking']??0) ?>">
<label>Description</label><textarea name="description"><?= e($edit['description']??'') ?></textarea>
<label><input type="checkbox" name="is_published" <?= !empty($edit['is_published'])?'checked':'' ?> style="width:auto"> Published</label><br><button class="btn">Save test</button></form>
<table><tr><th>Title</th><th>Category</th><th>Published</th><th>Demo</th><th>Actions</th></tr><?php foreach($tests as $t): ?><tr><td><?= e($t['title']) ?></td><td><?= e($t['cat']??'') ?></td><td><?= $t['is_published']?'Yes':'No' ?></td><td><?= $t['is_demo']?'DEMO':'' ?></td><td><a href="tests.php?edit=<?= $t['id'] ?>">Edit</a> · <a href="questions.php?test_id=<?= $t['id'] ?>">Questions</a> · <a href="import.php?test_id=<?= $t['id'] ?>">CSV Import</a>
<form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $t['id'] ?>"><button class="btn2" name="toggle">Publish/Unpublish</button> <button class="btn2 danger" name="delete" data-confirm="Delete this test and its questions?">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
