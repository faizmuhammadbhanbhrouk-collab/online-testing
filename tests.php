<?php $page_title='Tests'; include 'includes/header.php';
$q=trim($_GET['q']??''); $class=(int)($_GET['class_id']??0); $subj=(int)($_GET['subject_id']??0); $cat=trim($_GET['category']??''); $diff=trim($_GET['difficulty']??''); $dur=(int)($_GET['max_duration']??0);
$sql="SELECT t.*,cat.name catname,cat.slug catslug FROM tests t LEFT JOIN categories cat ON cat.id=t.category_id WHERE t.is_published=1"; $p=[];
if($q){$sql.=" AND t.title LIKE ?"; $p[]="%$q%";} if($class){$sql.=" AND t.class_id=?"; $p[]=$class;} if($subj){$sql.=" AND t.subject_id=?"; $p[]=$subj;} if($cat){$sql.=" AND cat.slug=?"; $p[]=$cat;} if($diff){$sql.=" AND t.difficulty=?"; $p[]=$diff;} if($dur){$sql.=" AND t.duration_minutes<=?"; $p[]=$dur;}
$sql.=" ORDER BY t.id DESC"; $st=db()->prepare($sql); $st->execute($p); $rows=$st->fetchAll();
$cats=db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$allcls=db()->query("SELECT * FROM classes ORDER BY grade")->fetchAll();
$allsubs=db()->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
?>
<h1><?= t('all_tests') ?></h1>
<form class="filters" method="get"><input name="q" value="<?= e($q) ?>" placeholder="<?= e(t('search_by_name')) ?>">
<select name="class_id"><option value="0"><?= t('all_classes') ?></option><?php foreach($allcls as $c): ?><option value="<?= $c['id'] ?>" <?= $class==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<select name="subject_id"><option value="0"><?= t('subjects') ?></option><?php foreach($allsubs as $c): ?><option value="<?= $c['id'] ?>" <?= $subj==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<select name="category"><option value=""><?= t('all_categories') ?></option><?php foreach($cats as $c): ?><option value="<?= e($c['slug']) ?>" <?= $cat===$c['slug']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<select name="difficulty"><option value=""><?= t('any_difficulty') ?></option><option <?= $diff==='Easy'?'selected':'' ?>>Easy</option><option <?= $diff==='Medium'?'selected':'' ?>>Medium</option><option <?= $diff==='Hard'?'selected':'' ?>>Hard</option></select>
<input type="number" name="max_duration" value="<?= $dur?:'' ?>" placeholder="<?= e(t('max_minutes')) ?>" style="width:130px"><button class="btn"><?= t('filter') ?></button></form>
<p class="muted">Quick categories: <a href="tests.php?category=computer">Computer Tests</a> · <a href="tests.php?category=general-knowledge">General Knowledge</a> · <a href="tests.php?category=iq-reasoning">IQ &amp; Reasoning</a> · <a href="tests.php?category=english">English</a> · <a href="tests.php?category=mathematics">Mathematics</a> · <a href="tests.php?category=science">Science</a></p>
<div class="grid"><?php foreach($rows as $r): ?><div class="card"><h3><?= e($r['title']) ?></h3><p class="muted"><?= e($r['catname']??'') ?> · <?= e($r['difficulty']) ?> · <?= (int)$r['duration_minutes'] ?> min · Pass <?= (int)$r['passing_percent'] ?>% <?php if($r['is_demo']) echo '<span class="badge demo">DEMO</span>'; ?></p><p><?= e($r['description']) ?></p><a class="btn" href="test.php?id=<?= $r['id'] ?>">Open test</a></div><?php endforeach; if(!$rows) echo '<div class="card"><p>'.e(t('no_tests')).'</p></div>'; ?></div>
<?php include 'includes/footer.php'; ?>
