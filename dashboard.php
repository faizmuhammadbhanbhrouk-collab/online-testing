<?php $page_title='Dashboard'; include 'includes/header.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])){
 if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
 $name=trim($_POST['name']??''); $class=(int)($_POST['class_id']??0);
 if(strlen($name)>=2){ db()->prepare('UPDATE users SET name=?,class_id=? WHERE id=?')->execute([$name,$class?:null,$user['id']]); $_SESSION['flash']='Profile updated.'; redirect('dashboard.php'); }
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_pw'])){
 if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
 if(password_verify($_POST['current']??'',$user['password_hash']) && strlen($_POST['new']??'')>=6){ db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new'],PASSWORD_DEFAULT),$user['id']]); $_SESSION['flash']='Password changed.'; } else $_SESSION['error']='Current password wrong or new password too short.'; redirect('dashboard.php');
}
$cls=db()->query("SELECT * FROM classes ORDER BY grade")->fetchAll();
$inprog=db()->prepare("SELECT a.*,t.title FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.user_id=? AND a.status='in_progress' ORDER BY a.id DESC"); $inprog->execute([$user['id']]); $inprog=$inprog->fetchAll();
$done=db()->prepare("SELECT a.*,t.title FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.user_id=? AND a.status='completed' ORDER BY a.id DESC LIMIT 20"); $done->execute([$user['id']]); $done=$done->fetchAll();
$bm=db()->prepare("SELECT q.question_text,t.title FROM bookmarks b JOIN questions q ON q.id=b.question_id JOIN tests t ON t.id=q.test_id WHERE b.user_id=?"); $bm->execute([$user['id']]); $bm=$bm->fetchAll();
$cert=db()->prepare("SELECT * FROM certificates WHERE user_id=? ORDER BY id DESC"); $cert->execute([$user['id']]); $cert=$cert->fetchAll();
$passes=count(array_filter($done,fn($r)=>$r['passed'])); $badge=$passes>=10?'🥇 Gold':($passes>=5?'🥈 Silver':($passes>=1?'🥉 Bronze':'—'));
$avail=db()->prepare("SELECT * FROM tests WHERE is_published=1 AND (class_id IS NULL OR class_id=?) ORDER BY id DESC LIMIT 8"); $avail->execute([$user['class_id']]); $avail=$avail->fetchAll();
$boardname='—'; $classname='—'; if($user['class_id']){ $cb=db()->prepare("SELECT cl.name cn,b.name bn FROM classes cl LEFT JOIN boards b ON b.id=cl.board_id WHERE cl.id=?"); $cb->execute([$user['class_id']]); $cbr=$cb->fetch(); if($cbr){ $classname=$cbr['cn']; $boardname=$cbr['bn']??'—'; } }
$perf=db()->prepare("SELECT COUNT(*) n, SUM(passed) p, ROUND(AVG(percentage),1) av FROM attempts WHERE user_id=? AND status='completed'"); $perf->execute([$user['id']]); $perf=$perf->fetch();
?>
<h1>Welcome, <?= e($user['name']) ?> <span class="badge"><?= $badge ?></span></h1>
<div class="grid">
<div class="card"><h3>Profile</h3><p><?= e($user['email']) ?> · Role: <?= e($user['role']) ?></p><p><?= t('selected_class') ?>: <?= e($classname) ?> · <?= t('board') ?>: <?= e($boardname) ?></p>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="update_profile" value="1">
<label>Name</label><input name="name" value="<?= e($user['name']) ?>" required>
<label>Selected class</label><select name="class_id"><option value="0">—</option><?php foreach($cls as $c): ?><option value="<?= $c['id'] ?>" <?= $user['class_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<button class="btn">Save profile</button></form></div>
<div class="card"><h3>Account Settings</h3><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="change_pw" value="1">
<label>Current password</label><input type="password" name="current" required><label>New password (min 6)</label><input type="password" name="new" required><button class="btn">Change password</button></form></div>
<div class="card"><h3><?= t('performance') ?></h3><p><?= t('total_attempts') ?>: <?= (int)$perf['n'] ?> · <?= t('passed') ?>: <?= (int)$perf['p'] ?> · <?= t('avg_pct') ?>: <?= e($perf['av']??0) ?>%</p></div>
<div class="card"><h3>Certificates &amp; Achievements</h3><?php foreach($cert as $c): ?><p>🏆 <?= e($c['title']) ?> — Code: <b><?= e($c['code']) ?></b></p><?php endforeach; if(!$cert) echo '<p class="muted">Pass a test to earn a certificate.</p>'; ?><p>Badge: <?= $badge ?> (<?= $passes ?> passed)</p></div>
</div>
<h2><?= t('continue_test') ?></h2><?php foreach($inprog as $a): ?><div class="card"><p><?= e($a['title']) ?> (<?= e($a['mode']) ?>) — <a class="btn" href="take.php?attempt_id=<?= $a['id'] ?>">Continue →</a></p></div><?php endforeach; if(!$inprog) echo '<p class="muted">No test in progress.</p>'; ?>
<h2><?= t('available_tests') ?></h2><div class="grid"><?php foreach($avail as $t): ?><div class="card"><h3><?= e($t['title']) ?></h3><a class="btn" href="test.php?id=<?= $t['id'] ?>">Open</a></div><?php endforeach; ?></div>
<h2><?= t('recent_results') ?></h2><table><tr><th>Test</th><th>Mode</th><th>Marks</th><th>%</th><th>Pass?</th><th></th></tr>
<?php foreach($done as $a): ?><tr><td><?= e($a['title']) ?></td><td><?= e($a['mode']) ?></td><td><?= e($a['obtained_marks']) ?>/<?= e($a['total_marks']) ?></td><td><?= e($a['percentage']) ?>%</td><td><?= $a['passed']?'✅':'❌' ?></td><td><a href="result.php?attempt_id=<?= $a['id'] ?>">View</a></td></tr><?php endforeach; if(!$done) echo '<tr><td colspan="6">No completed tests yet.</td></tr>'; ?></table>
<h2><?= t('bookmarked') ?></h2><?php foreach($bm as $b): ?><div class="card"><p><?= e($b['question_text']) ?> <span class="muted">(<?= e($b['title']) ?>)</span></p></div><?php endforeach; if(!$bm) echo '<p class="muted">Use the "Bookmark this question" link on any result page to save a question here.</p>'; ?>
<?php include 'includes/footer.php'; ?>
