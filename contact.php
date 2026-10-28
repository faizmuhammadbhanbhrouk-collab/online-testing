<?php require 'config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_check($_POST['csrf']??'')) die('Invalid form.');
 $n=trim($_POST['name']??''); $em=trim($_POST['email']??''); $m=trim($_POST['message']??'');
 if(strlen($n)<2||!filter_var($em,FILTER_VALIDATE_EMAIL)||strlen($m)<10){ $_SESSION['error']='Please enter your name, a valid email and a message of at least 10 characters.'; redirect('contact.php'); }
 db()->prepare('INSERT INTO contact_messages(name,email,message) VALUES(?,?,?)')->execute([$n,$em,$m]); $_SESSION['flash']='Thank you! Your message was received.'; redirect('contact.php');
}
$page_title='Contact Us'; include 'includes/header.php'; ?>
<h1><?= t('contact') ?></h1><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<label>Name</label><input name="name" required><label>Email</label><input type="email" name="email" required><label>Message</label><textarea name="message" rows="5" required></textarea><button class="btn">Send message</button></form>
<?php include 'includes/footer.php'; ?>
