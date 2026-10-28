<?php include 'header.php';
$stats=[]; foreach(['users','tests','questions','attempts'] as $t){ $stats[$t]=db()->query("SELECT COUNT(*) c FROM $t")->fetch()['c']; }
$stats['published']=(int)db()->query("SELECT COUNT(*) c FROM tests WHERE is_published=1")->fetch()['c'];
$stats['students']=(int)db()->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch()['c'];
$stats['completed']=(int)db()->query("SELECT COUNT(*) c FROM attempts WHERE status='completed'")->fetch()['c'];
$recent=db()->query("SELECT a.*,u.name un,t.title FROM attempts a JOIN users u ON u.id=a.user_id JOIN tests t ON t.id=a.test_id ORDER BY a.id DESC LIMIT 10")->fetchAll(); ?>
<h1>Admin Dashboard</h1><div class="grid">
<div class="card"><h3>Users</h3><p><?= (int)$stats['users'] ?></p></div><div class="card"><h3>Tests</h3><p><?= (int)$stats['tests'] ?></p></div><div class="card"><h3>Questions</h3><p><?= (int)$stats['questions'] ?></p></div><div class="card"><h3>Attempts</h3><p><?= (int)$stats['attempts'] ?></p></div><div class="card"><h3>Published Tests</h3><p><?= (int)$stats['published'] ?></p></div><div class="card"><h3>Students</h3><p><?= (int)$stats['students'] ?></p></div><div class="card"><h3>Completed Attempts</h3><p><?= (int)$stats['completed'] ?></p></div></div>
<h2>Recent Attempts</h2><table><tr><th>Student</th><th>Test</th><th>Status</th><th>%</th></tr><?php foreach($recent as $r): ?><tr><td><?= e($r['un']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['percentage']) ?>%</td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
