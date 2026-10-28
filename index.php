<?php $page_title='Home'; include 'includes/header.php';
$tests=db()->query("SELECT t.*,c.name AS cat FROM tests t LEFT JOIN categories c ON c.id=t.category_id WHERE t.is_published=1 ORDER BY t.id DESC LIMIT 8")->fetchAll();
$ann=db()->query("SELECT * FROM announcements WHERE is_active=1 ORDER BY id DESC LIMIT 3")->fetchAll();
$cls=db()->query("SELECT * FROM classes ORDER BY grade LIMIT 12")->fetchAll();
$stat_users=(int)db()->query("SELECT COUNT(*) c FROM users")->fetch()['c'];
$stat_tests=(int)db()->query("SELECT COUNT(*) c FROM tests WHERE is_published=1")->fetch()['c'];
$stat_cats=db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<div class="stat"><div class="card"><h3><?= t('registered_students') ?></h3><p class="num"><?= $stat_users ?></p><p class="muted">Includes the seeded admin account, as in the Muse version.</p></div><div class="card"><h3><?= t('published_tests') ?></h3><p class="num"><?= $stat_tests ?></p></div><div class="card"><h3><?= t('all_classes') ?></h3><p class="num"><?= count($cls) ?></p></div></div>
<div class="hero"><h1><?= e(t('welcome')) ?></h1><p>Practice Mode for learning, Exam Mode for a timed test. Results are calculated on the server — never in the browser alone.</p>
<form method="get" action="tests.php" style="display:flex;gap:8px;max-width:560px"><input name="q" placeholder="<?= e(t('search')) ?>"><button class="btn2">Search</button></form></div>
<?php if($ann): ?><h2><?= t('announcements') ?></h2><div class="grid"><?php foreach($ann as $a): ?><div class="card"><h3><?= e($a['title']) ?></h3><p><?= e($a['body']) ?></p></div><?php endforeach; ?></div><?php endif; ?>
<h2><?= t('categories') ?></h2><div class="grid"><?php foreach($stat_cats as $cc): ?><div class="cat-card"><h3><?= e($cc['name']) ?></h3><a class="btn2" href="tests.php?category=<?= e($cc['slug']) ?>"><?= t('view_tests') ?> →</a></div><?php endforeach; ?></div>
<h2><?= t('how_it_works') ?></h2><div class="grid"><div class="card"><h3><?= t('step1') ?></h3><p><?= t('step1d') ?></p></div><div class="card"><h3><?= t('step2') ?></h3><p><?= t('step2d') ?></p></div><div class="card"><h3><?= t('step3') ?></h3><p><?= t('step3d') ?></p></div></div>
<h2>Classes 1–12</h2><div class="grid"><?php foreach($cls as $c): ?><div class="card"><h3><?= e($c['name']) ?></h3><a class="btn2" href="tests.php?class_id=<?= $c['id'] ?>">View tests →</a></div><?php endforeach; ?></div>
<h2><?= t('latest_tests') ?></h2><div class="grid"><?php foreach($tests as $tt): ?><div class="card"><h3><?= e($tt['title']) ?></h3><p class="muted"><?= e($tt['cat']??'') ?> · <?= e($tt['difficulty']) ?> · <?= (int)$tt['duration_minutes'] ?> min <?php if($tt['is_demo']) echo '<span class="badge demo">DEMO</span>'; ?></p><p><?= e($tt['description']) ?></p><a class="btn" href="test.php?id=<?= $tt['id'] ?>">Open test</a></div><?php endforeach; if(!$tests) echo '<p>No published tests yet. Admin can create one.</p>'; ?></div>
<?php include 'includes/footer.php'; ?>
