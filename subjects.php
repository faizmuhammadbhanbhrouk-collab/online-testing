<?php $page_title='Subjects'; include 'includes/header.php'; $rows=db()->query("SELECT s.*,c.name cat FROM subjects s LEFT JOIN categories c ON c.id=s.category_id ORDER BY s.name")->fetchAll(); ?>
<h1><?= t('subjects') ?></h1><div class="grid"><?php foreach($rows as $r): ?><div class="card"><h3><?= e($r['name']) ?></h3><p class="muted"><?= e($r['cat']??'') ?></p><a class="btn" href="tests.php?subject_id=<?= $r['id'] ?>">View tests</a></div><?php endforeach; ?></div>
<?php include 'includes/footer.php'; ?>
