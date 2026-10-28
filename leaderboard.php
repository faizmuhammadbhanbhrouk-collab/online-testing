<?php $page_title='Leaderboard'; include 'includes/header.php';
$rows=db()->query("SELECT u.name, COUNT(a.id) attempts, SUM(a.passed) passes, ROUND(AVG(a.percentage),1) avgpct FROM users u JOIN attempts a ON a.user_id=u.id WHERE a.status='completed' GROUP BY u.id ORDER BY passes DESC, avgpct DESC LIMIT 20")->fetchAll(); ?>
<h1><?= t('leaderboard') ?></h1><p class="muted">Badges: 🥉 Bronze = 1 passed test · 🥈 Silver = 5 · 🥇 Gold = 10.</p>
<table><tr><th>#</th><th>Student</th><th>Attempts</th><th>Passed</th><th>Avg %</th><th>Badge</th></tr>
<?php $i=1; foreach($rows as $r): $badge=$r['passes']>=10?'🥇 Gold':($r['passes']>=5?'🥈 Silver':($r['passes']>=1?'🥉 Bronze':'—')); ?>
<tr><td><?= $i++ ?></td><td><?= e($r['name']) ?></td><td><?= (int)$r['attempts'] ?></td><td><?= (int)$r['passes'] ?></td><td><?= e($r['avgpct']) ?>%</td><td><?= $badge ?></td></tr><?php endforeach; if(!$rows) echo '<tr><td colspan="6">No completed tests yet.</td></tr>'; ?></table>
<?php include 'includes/footer.php'; ?>
