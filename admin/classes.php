<?php include 'header.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 if(isset($_POST['delete_board'])) db()->prepare('DELETE FROM boards WHERE id=?')->execute([(int)$_POST['id']]);
 elseif(isset($_POST['add_board'])){ $n=trim($_POST['name']??''); if($n) db()->prepare('INSERT INTO boards(name,slug) VALUES(?,?)')->execute([$n,preg_replace('/[^a-z0-9-]+/','-',strtolower($n)).'-'.time()]); }
 elseif(isset($_POST['delete_class'])) db()->prepare('DELETE FROM classes WHERE id=?')->execute([(int)$_POST['id']]);
 elseif(isset($_POST['add_class'])){ $n=trim($_POST['name']??''); $g=(int)($_POST['grade']??0); $b=(int)($_POST['board_id']??0)?:null; if($n&&$g) db()->prepare('INSERT INTO classes(board_id,name,grade) VALUES(?,?,?)')->execute([$b,$n,$g]); }
 redirect('classes.php');
}
$boards=db()->query("SELECT * FROM boards ORDER BY id")->fetchAll(); $cls=db()->query("SELECT cl.*,b.name board FROM classes cl LEFT JOIN boards b ON b.id=cl.board_id ORDER BY grade")->fetchAll(); ?>
<h1>Boards &amp; Classes</h1>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>New board name</label><input name="name" required><button class="btn" name="add_board">Add board</button></form>
<table><tr><th>Board</th><th>Action</th></tr><?php foreach($boards as $b): ?><tr><td><?= e($b['name']) ?> <?= $b['is_demo']?'<span class="badge demo">DEMO</span>':'' ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button class="btn2 danger" name="delete_board" data-confirm="Delete this board?">Delete</button></form></td></tr><?php endforeach; ?></table>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Class name</label><input name="name" required><label>Grade</label><input type="number" name="grade" required><label>Board</label><select name="board_id"><option value="0">—</option><?php foreach($boards as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option><?php endforeach; ?></select><button class="btn" name="add_class">Add class</button></form>
<table><tr><th>Class</th><th>Grade</th><th>Board</th><th>Action</th></tr><?php foreach($cls as $c): ?><tr><td><?= e($c['name']) ?></td><td><?= (int)$c['grade'] ?></td><td><?= e($c['board']??'') ?></td><td><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn2 danger" name="delete_class" data-confirm="Delete this class?">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php include 'footer.php'; ?>
