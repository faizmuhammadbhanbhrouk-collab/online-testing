<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete'])) db()->prepare('DELETE FROM subjects WHERE id=?')->execute([(int)$_POST['id']]);
 elseif(isset($_POST['add_subject'])){ $n=trim($_POST['name']??''); $cat=(int)($_POST['category_id']??0)?:null; if($n) db()->prepare('INSERT INTO subjects(category_id,name,slug) VALUES(?,?,?)')->execute([$cat,$n,preg_replace('/[^a-z0-9-]+/','-',strtolower($n)).'-'.time()]); }
 elseif(isset($_POST['add_category'])){ $n=trim($_POST['name']??''); if($n) db()->prepare('INSERT INTO categories(name,slug) VALUES(?,?)')->execute([$n,preg_replace('/[^a-z0-9-]+/','-',strtolower($n)).'-'.time()]); }
 elseif(isset($_POST['delete_category'])) db()->prepare('DELETE FROM categories WHERE id=?')->execute([(int)$_POST['id']]);
 redirect('subjects.php');
}
$cats=db()->query("SELECT * FROM categories ORDER BY name")->fetchAll(); $subs=db()->query("SELECT s.*,c.name cat FROM subjects s LEFT JOIN categories c ON c.id=s.category_id ORDER BY s.name")->fetchAll(); ?>
<h1>Subjects &amp; Categories</h1>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>New category</label><input name="name" required><button class="btn" name="add_category">Add category</button></form>
<table><tr><th>Category</th><th>Action</th></tr><?php foreach($cats as $c): ?><tr><td><?= e($c['name']) ?> <?= $c['is_demo']?'<span class="badge demo">DEMO</span>':'' ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn2 danger" name="delete_category" data-confirm="Delete this category?">Delete</button></form></td></tr><?php endforeach; ?></table>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>New subject</label><input name="name" required><label>Category</label><select name="category_id"><option value="0">—</option><?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select><button class="btn" name="add_subject">Add subject</button></form>
<table><tr><th>Subject</th><th>Category</th><th>Action</th></tr><?php foreach($subs as $s): ?><tr><td><?= e($s['name']) ?></td><td><?= e($s['cat']??'') ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn2 danger" name="delete" data-confirm="Delete this subject?">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
