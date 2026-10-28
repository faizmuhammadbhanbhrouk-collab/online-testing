<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete'])) db()->prepare('DELETE FROM announcements WHERE id=?')->execute([(int)$_POST['id']]);
 else{ $t=trim($_POST['title']??''); $b=trim($_POST['body']??''); if($t) db()->prepare('INSERT INTO announcements(title,body,is_active) VALUES(?,?,?)')->execute([$t,$b,isset($_POST['is_active'])?1:0]); }
 redirect('announcements.php');
}
$rows=db()->query("SELECT * FROM announcements ORDER BY id DESC")->fetchAll(); ?>
<h1>Announcements</h1><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Title</label><input name="title" required><label>Body</label><textarea name="body"></textarea><label><input type="checkbox" name="is_active" checked style="width:auto"> Active</label><br><button class="btn">Add announcement</button></form>
<table><tr><th>Title</th><th>Active</th><th>Action</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= $r['is_active']?'Yes':'No' ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn2 danger" name="delete" data-confirm="Delete announcement?">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
