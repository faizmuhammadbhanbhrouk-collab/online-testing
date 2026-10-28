<?php $page_title='Classes 1-12'; include 'includes/header.php'; $rows=db()->query("SELECT cl.*,b.name board FROM classes cl LEFT JOIN boards b ON b.id=cl.board_id ORDER BY grade")->fetchAll(); ?>
<h1><?= t('classes') ?> 1–12</h1><p class="muted">Boards are configurable in the Admin Panel (Boards are not hardcoded to one curriculum).</p>
<div class="grid"><?php foreach($rows as $r): ?><div class="card"><h3><?= e($r['name']) ?></h3><p class="muted">Board: <?= e($r['board']??'—') ?></p><a class="btn" href="tests.php?class_id=<?= $r['id'] ?>">Tests for this class</a></div><?php endforeach; ?></div>
<?php include 'includes/footer.php'; ?>
