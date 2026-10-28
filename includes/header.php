<?php require_once __DIR__.'/../config.php'; $user=current_user(); $isUr=($lang==='ur'); ?>
<!DOCTYPE html><html lang="<?= $isUr?'ur':'en' ?>" dir="<?= $isUr?'rtl':'ltr' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($page_title??site_setting('site_name',SITE_NAME)) ?> | <?= e(site_setting('site_name',SITE_NAME)) ?></title>
<meta name="description" content="<?= e(site_setting('site_tagline','Free online tests for Class 1-12, Computer, GK, IQ, English, Maths and Science.')) ?>">
<link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<header class="top"><div class="wrap top-in">
<a class="logo" href="index.php">🎓 <?= e(site_setting('site_name',SITE_NAME)) ?></a>
<button class="nav-toggle" onclick="document.querySelector('nav').classList.toggle('open')" aria-label="Menu">☰</button>
<nav>
<a href="index.php"><?= t('home') ?></a><a href="classes.php"><?= t('classes') ?></a><a href="subjects.php"><?= t('subjects') ?></a>
<a href="tests.php"><?= t('tests') ?></a><a href="leaderboard.php"><?= t('leaderboard') ?></a>
<a href="about.php"><?= t('about') ?></a><a href="contact.php"><?= t('contact') ?></a>
<?php if($user): ?><a href="dashboard.php"><?= t('dashboard') ?></a><?php if(is_admin()): ?><a href="admin/index.php">👑 <?= t('admin') ?></a><?php endif; ?><a href="logout.php"><?= t('logout') ?></a>
<?php else: ?><a href="login.php"><?= t('login') ?></a><a class="btn" href="register.php"><?= t('signup') ?></a><?php endif; ?>
<?php $toggle_qs=$_GET; $toggle_qs['lang']=$isUr?'en':'ur'; ?><a href="?<?= http_build_query($toggle_qs) ?>"><?= $isUr?'English':'اردو' ?></a>
</nav></div></header>
<main class="wrap">
<?php if(!empty($_SESSION['flash'])){ echo '<div class="flash">'.e($_SESSION['flash']).'</div>'; unset($_SESSION['flash']); } ?>
<?php if(!empty($_SESSION['error'])){ echo '<div class="flash err">'.e($_SESSION['error']).'</div>'; unset($_SESSION['error']); } ?>
