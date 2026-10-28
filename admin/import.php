<?php include 'header.php'; $test_id=(int)($_GET['test_id']??0);
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')){
 $test_id=(int)$_POST['test_id']; $count=0;
 if(!empty($_FILES['csv']['tmp_name']) && is_uploaded_file($_FILES['csv']['tmp_name'])){
  $fh=fopen($_FILES['csv']['tmp_name'],'r'); $header=fgetcsv($fh);
  while(($row=fgetcsv($fh))!==false){
   // Columns: question_text, type(mcq/tf), marks, option1, option2, option3, option4, correct_index(0-based), explanation
   if(count($row)<8 || trim($row[0])==='') continue;
   db()->prepare('INSERT INTO questions(test_id,question_text,question_type,marks,explanation,sort_order) VALUES(?,?,?,?,?,0)')->execute([$test_id,trim($row[0]),($row[1]==='tf'?'tf':'mcq'),(float)($row[2]?:1),trim($row[8]??'')]);
   $qid=db()->lastInsertId(); $ci=(int)$row[7];
   for($i=0;$i<4;$i++){ $opt=trim($row[3+$i]??''); if($opt==='') continue; db()->prepare('INSERT INTO options(question_id,option_text,is_correct,sort_order) VALUES(?,?,?,?)')->execute([$qid,$opt,($i===$ci)?1:0,$i]); }
   $count++;
  } fclose($fh); $_SESSION['flash']="Imported $count questions.";
 } else $_SESSION['error']='Please choose a CSV file.';
 redirect('import.php?test_id='.$test_id);
}
$tests=db()->query("SELECT id,title FROM tests ORDER BY id DESC")->fetchAll(); ?>
<h1>Import Questions (CSV)</h1>
<p class="muted">CSV columns, in order: <b>question_text, type (mcq/tf), marks, option1, option2, option3, option4, correct_index (0-based), explanation</b>. First row is treated as a header and skipped. Sample:</p>
<pre>question_text,type,marks,option1,option2,option3,option4,correct_index,explanation
What is 2+2?,mcq,1,3,4,5,6,1,2+2 equals 4.</pre>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Test</label><select name="test_id" required><option value="">Choose</option><?php foreach($tests as $t): ?><option value="<?= $t['id'] ?>" <?= $test_id==$t['id']?'selected':'' ?>><?= e($t['title']) ?></option><?php endforeach; ?></select>
<label>CSV file</label><input type="file" name="csv" accept=".csv" required><button class="btn">Import</button></form>
<?php include 'footer.php'; ?>
