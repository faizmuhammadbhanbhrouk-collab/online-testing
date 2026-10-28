<?php include 'header.php'; $rows=db()->query("SELECT a.*,u.name un,u.email,t.title FROM attempts a JOIN users u ON u.id=a.user_id JOIN tests t ON t.id=a.test_id ORDER BY a.id DESC LIMIT 100")->fetchAll(); ?>
<h1>Attempts &amp; Results</h1><table><tr><th>Student</th><th>Test</th><th>Mode</th><th>Status</th><th>Marks</th><th>%</th><th>Pass</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['un']) ?> (<?= e($r['email']) ?>)</td><td><?= e($r['title']) ?></td><td><?= e($r['mode']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['obtained_marks']) ?>/<?= e($r['total_marks']) ?></td><td><?= e($r['percentage']) ?>%</td><td><?= $r['passed']?'✅':'❌' ?></td></tr><?php endforeach; if(!$rows) echo '<tr><td colspan="7">No attempts yet.</td></tr>'; ?></table>
<?php include 'footer.php'; ?>
