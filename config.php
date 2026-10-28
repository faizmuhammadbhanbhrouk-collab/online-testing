<?php
// Online Testing Website - configuration, DB, auth helpers, language
// Edit DB credentials here for XAMPP (see README_SETUP.md).
define('DB_HOST','127.0.0.1');
define('DB_NAME','online_testing');
define('DB_USER','root');
define('DB_PASS','');
define('SITE_NAME','Online Testing');
define('BASE_HINT',''); // keep empty: all links are relative

if (!ob_get_level()) ob_start();
if (session_status()===PHP_SESSION_NONE) session_start();
// Session expiry: 2 hours of inactivity
if (isset($_SESSION['last_activity']) && time()-$_SESSION['last_activity']>7200) {
    session_unset(); session_destroy(); session_start();
}
$_SESSION['last_activity']=time();

function db(){
    static $pdo=null;
    if($pdo===null){
        $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    }
    return $pdo;
}
function e($s){ return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function redirect($u){ header('Location: '.$u); exit; }
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check($t){ return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'],(string)$t); }
function current_user(){
    if(empty($_SESSION['user_id'])) return null;
    $st=db()->prepare('SELECT * FROM users WHERE id=?'); $st->execute([$_SESSION['user_id']]);
    return $st->fetch() ?: null;
}
function require_login(){ if(!current_user()) redirect('login.php?next='.urlencode($_SERVER['REQUEST_URI']??'dashboard.php')); }
function require_admin(){ $u=current_user(); if(!$u) redirect('login.php'); if($u['role']!=='admin'){ http_response_code(403); die('Admin access required.'); } }
function is_admin(){ $u=current_user(); return $u && $u['role']==='admin'; }

// --- Language: EN (default) / UR (Urdu script, RTL). Coverage (matched to Muse version): main nav/labels + home, tests, test detail, take, result, dashboard, leaderboard, about, contact headings. Test content (questions) stays in the language it was written in; this is stated honestly in the footer/README.
// Test content (questions) stays in the language it was written in; this is stated honestly in the footer/README.
if(isset($_GET['lang']) && in_array($_GET['lang'],['en','ur'],true)) $_SESSION['lang']=$_GET['lang'];
$lang=$_SESSION['lang']??'en';
$T=[
 'en'=>['home'=>'Home','classes'=>'Classes','subjects'=>'Subjects','tests'=>'Tests','leaderboard'=>'Leaderboard','dashboard'=>'Dashboard','admin'=>'Admin','login'=>'Login','signup'=>'Sign Up','logout'=>'Logout','about'=>'About Us','contact'=>'Contact Us','search'=>'Search tests…','start_test'=>'Start Test','results'=>'Results','profile'=>'Profile','settings'=>'Settings','announcements'=>'Announcements','welcome'=>'Learn, Practice, Test — Class 1 to 12','latest_tests'=>'Latest Tests','categories'=>'Test Categories','registered_students'=>'Registered students','published_tests'=>'Published tests','all_classes'=>'Classes','how_it_works'=>'How it works','step1'=>'1. Sign up','step2'=>'2. Choose a test','step3'=>'3. Get your result','step1d'=>'Create a free student account.','step2d'=>'Pick a class, subject and test, then choose Practice or Exam mode.','step3d'=>'Submit and see marks, percentage, pass/fail and explanations.','all_tests'=>'All Tests','view_tests'=>'View tests','open_test'=>'Open test','continue_test'=>'Continue Test','available_tests'=>'Available Tests','recent_results'=>'Recent Results & History','bookmarked'=>'Bookmarked Questions','certificates'=>'Certificates & Achievements','performance'=>'Performance','total_attempts'=>'Total attempts','passed'=>'Passed','avg_pct'=>'Average %','board'=>'Board','selected_class'=>'Selected class','class'=>'Class','subject'=>'Subject','category'=>'Category','chapter'=>'Chapter','difficulty'=>'Difficulty','duration'=>'Duration','minutes'=>'min','questions'=>'Questions','pass_mark'=>'Pass','time_left'=>'Time left','answered'=>'Answered','unanswered'=>'Unanswered','review'=>'Marked for review','current'=>'Current','submit_test'=>'Submit Test','submit_confirm'=>'Submit this test now? You cannot change answers after submission.','download_result'=>'Download result (text file)','print_result'=>'Print / Download (PDF via browser print)','answers_explanations'=>'Answers & Explanations (shown only after submission)','your_answer'=>'Your answer','correct_answer'=>'Correct answer','explanation'=>'Explanation','marks'=>'Marks','status'=>'Status','pass'=>'PASS','fail'=>'FAIL','no_tests'=>'No tests match these filters. Try clearing a filter.','no_results'=>'No completed tests yet.','search_by_name'=>'Search by name','all_categories'=>'All categories','any_difficulty'=>'Any difficulty','max_minutes'=>'Max minutes','filter'=>'Filter'],
 'ur'=>['home'=>'ہوم','classes'=>'کلاسیں','subjects'=>'مضامین','tests'=>'ٹیسٹ','leaderboard'=>'لیڈر بورڈ','dashboard'=>'ڈیش بورڈ','admin'=>'ایڈمن','login'=>'لاگ ان','signup'=>'سائن اپ','logout'=>'لاگ آؤٹ','about'=>'ہمارے بارے میں','contact'=>'رابطہ','search'=>'ٹیسٹ تلاش کریں…','start_test'=>'ٹیسٹ شروع کریں','results'=>'نتائج','profile'=>'پروفائل','settings'=>'سیٹنگز','announcements'=>'اعلانات','welcome'=>'سیکھیں، مشق کریں، ٹیسٹ دیں — کلاس 1 تا 12','latest_tests'=>'تازہ ترین ٹیسٹ','categories'=>'ٹیسٹ کی اقسام','registered_students'=>'رجسٹرڈ طلبہ','published_tests'=>'شائع شدہ ٹیسٹ','all_classes'=>'کلاسیں','how_it_works'=>'طریقہ کار','step1'=>'1۔ سائن اپ کریں','step2'=>'2۔ ٹیسٹ منتخب کریں','step3'=>'3۔ نتیجہ دیکھیں','step1d'=>'مفت طالب علم اکاؤنٹ بنائیں۔','step2d'=>'کلاس، مضمون اور ٹیسٹ چنیں، پھر پریکٹس یا ایگزام موڈ منتخب کریں۔','step3d'=>'جمع کرائیں اور نمبر، فیصد، پاس/فیل اور وضاحتیں دیکھیں۔','all_tests'=>'تمام ٹیسٹ','view_tests'=>'ٹیسٹ دیکھیں','open_test'=>'ٹیسٹ کھولیں','continue_test'=>'ٹیسٹ جاری رکھیں','available_tests'=>'دستیاب ٹیسٹ','recent_results'=>'حالیہ نتائج اور تاریخ','bookmarked'=>'محفوظ شدہ سوالات','certificates'=>'سرٹیفکیٹس اور کامیابیاں','performance'=>'کارکردگی','total_attempts'=>'کل کوششیں','passed'=>'پاس','avg_pct'=>'اوسط ٪','board'=>'بورڈ','selected_class'=>'منتخب کلاس','class'=>'کلاس','subject'=>'مضمون','category'=>'زمرہ','chapter'=>'باب','difficulty'=>'مشکل','duration'=>'دورانیہ','minutes'=>'منٹ','questions'=>'سوالات','pass_mark'=>'پاس','time_left'=>'باقی وقت','answered'=>'جواب دیے گئے','unanswered'=>'جواب نہیں دیے گئے','review'=>'جائزے کے لیے نشان زد','current'=>'موجودہ','submit_test'=>'ٹیسٹ جمع کرائیں','submit_confirm'=>'ابھی ٹیسٹ جمع کریں؟ جمع کرانے کے بعد جوابات تبدیل نہیں ہو سکیں گے۔','download_result'=>'نتیجہ ڈاؤن لوڈ کریں (ٹیکسٹ فائل)','print_result'=>'پرنٹ / ڈاؤن لوڈ (براؤزر پرنٹ سے پی ڈی ایف)','answers_explanations'=>'جوابات اور وضاحتیں (صرف جمع کرانے کے بعد دکھائی جاتی ہیں)','your_answer'=>'آپ کا جواب','correct_answer'=>'درست جواب','explanation'=>'وضاحت','marks'=>'نمبر','status'=>'حیثیت','pass'=>'پاس','fail'=>'فیل','no_tests'=>'ان فلٹرز سے کوئی ٹیسٹ نہیں ملا۔ فلٹر صاف کر کے دوبارہ کوشش کریں۔','no_results'=>'ابھی کوئی مکمل ٹیسٹ نہیں ہے۔','search_by_name'=>'نام سے تلاش کریں','all_categories'=>'تمام اقسام','any_difficulty'=>'کوئی بھی مشکل','max_minutes'=>'زیادہ سے زیادہ منٹ','filter'=>'فلٹر'], 
];
function t($k){ global $T,$lang; return $T[$lang][$k]??$T['en'][$k]??$k; }
function site_setting($k,$def=''){
    try{ $st=db()->prepare('SELECT value FROM settings WHERE `key`=?'); $st->execute([$k]); $r=$st->fetch(); return $r?$r['value']:$def; }catch(Exception $ex){ return $def; }
}
