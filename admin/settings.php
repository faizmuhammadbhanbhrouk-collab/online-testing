<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 foreach(['site_name','site_tagline'] as $k){ $v=trim($_POST[$k]??''); db()->prepare('INSERT INTO settings(`key`,value) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute([$k,$v]); }
 $_SESSION['flash']='Settings saved.'; redirect('settings.php');
} ?>
<h1>Website Settings</h1><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Site name</label><input name="site_name" value="<?= e(site_setting('site_name')) ?>" required>
<label>Site tagline</label><input name="site_tagline" value="<?= e(site_setting('site_tagline')) ?>">
<button class="btn">Save settings</button></form>
<h2>Contact Messages</h2><?php $msgs=db()->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 20")->fetchAll(); foreach($msgs as $m): ?><div class="card"><p><b><?= e($m['name']) ?></b> (<?= e($m['email']) ?>)</p><p><?= e($m['message']) ?></p></div><?php endforeach; if(!$msgs) echo '<p class="muted">No messages yet.</p>'; ?>
<?php include 'footer.php'; ?>
