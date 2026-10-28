<?php require 'config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
 $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $pw=$_POST['password']??''; $class=(int)($_POST['class_id']??0);
 if(strlen($name)<2||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pw)<6){ $_SESSION['error']='Please enter a name, a valid email and a password of at least 6 characters.'; redirect('register.php'); }
 try{ $st=db()->prepare('INSERT INTO users(name,email,password_hash,class_id) VALUES(?,?,?,?)'); $st->execute([$name,$email,password_hash($pw,PASSWORD_DEFAULT),$class?:null]); $_SESSION['flash']='Account created. Please log in.'; redirect('login.php'); }catch(PDOException $ex){ $_SESSION['error']='That email is already registered.'; redirect('register.php'); }
}
$page_title='Sign Up'; include 'includes/header.php';
$cls=db()->query("SELECT * FROM classes ORDER BY grade")->fetchAll(); ?>
<h1>Sign Up</h1><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Name</label><input name="name" required><label>Email</label><input type="email" name="email" required>
<label>Class (optional)</label><select name="class_id"><option value="0">—</option><?php foreach($cls as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select>
<label>Password (min 6)</label><input type="password" name="password" required><button class="btn">Create account</button></form>
<p>Already have an account? <a href="login.php">Login</a></p><?php include 'includes/footer.php'; ?>
