<?php require 'config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
 $email=trim($_POST['email']??''); $pw=$_POST['password']??'';
 $st=db()->prepare('SELECT * FROM users WHERE email=?'); $st->execute([$email]); $u=$st->fetch();
 if($u && password_verify($pw,$u['password_hash'])){ session_regenerate_id(true); $_SESSION['user_id']=$u['id']; db()->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$u['id']]); redirect($u['role']==='admin'?'admin/index.php':'dashboard.php'); }
 $_SESSION['error']='Wrong email or password.'; redirect('login.php');
}
$page_title='Login'; include 'includes/header.php'; ?>
<h1>Login</h1><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Email</label><input type="email" name="email" required><label>Password</label><input type="password" name="password" required><button class="btn">Login</button></form>
<p>No account? <a href="register.php">Sign up</a></p><?php include 'includes/footer.php'; ?>
