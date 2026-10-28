<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete'])){ if((int)$_POST['id']!==$user['id']) db()->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_POST['id']]); }
 elseif(isset($_POST['make_admin'])) db()->prepare("UPDATE users SET role='admin' WHERE id=?")->execute([(int)$_POST['id']]);
 elseif(isset($_POST['make_student'])) db()->prepare("UPDATE users SET role='student' WHERE id=?")->execute([(int)$_POST['id']]);
 redirect('users.php');
}
$rows=db()->query("SELECT u.*,c.name cls FROM users u LEFT JOIN classes c ON c.id=u.class_id ORDER BY u.id DESC")->fetchAll(); ?>
<h1>Manage Students &amp; Accounts</h1><table><tr><th>Name</th><th>Email</th><th>Role</th><th>Class</th><th>Actions</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['role']) ?></td><td><?= e($r['cls']??'') ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<?php if($r['role']==='student'): ?><button class="btn2" name="make_admin">Make admin</button><?php else: ?><button class="btn2" name="make_student">Make student</button><?php endif; ?>
<button class="btn2 danger" name="delete" data-confirm="Delete this account and its attempts?">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
