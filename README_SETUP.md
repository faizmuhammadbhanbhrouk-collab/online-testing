# Online Testing Website — Setup Guide (Roman Urdu + English) — MATCHED VERSION

## Ye version kya hai? / What is this version?
**Muse-hosted Online Testing Website (slug: `online-testing-website`) is the MAIN / reference version.** Ye ZIP usi Muse version se match karne ke liye banaya gaya hai — isay "matched" version kehte hain. Purani original ZIP (`online-testing-website.zip`) backup ke tor par wesi hi mehfooz hai; is nayi ZIP ko us par overwrite NAHI kiya gaya.

Ye ek poori PHP + MySQL online testing website hai: Class 1–12, Computer, GK, IQ, English, Maths, Science tests, real quiz engine, server-side scoring, student dashboard aur admin panel ke sath. Ye Muse ke andar ka private app NAHI hai — ye normal website files hain, jo aap XAMPP par khud chala sakte ho.

**Important — data sync NAHI hota:** Muse version aur is PHP version ka data alag-alag hai. Muse mein banaya hua account/test/result is PHP site mein nahi ayega, aur is PHP site mein banaya hua Muse mein nahi ayega. Dono ko alag-alag use karna hoga.

## XAMPP Setup — Step by Step
1. XAMPP install karo aur **Apache** aur **MySQL** dono Start karo (XAMPP Control Panel se).
2. Is folder `online-testing-website-matched` ko copy karo: `C:\xampp\htdocs\online-testing-website-matched` (Windows) ya `~/xampp/htdocs/` ke andar. (Folder ka naam aap `online-testing-website` bhi rakh sakte ho, bas URL usi hisab se badal jayega.)
3. Browser mein `http://localhost/phpmyadmin` kholo.
4. **New** dabao, database ka naam likho: `online_testing`, collation `utf8mb4_unicode_ci`, phir **Create**.
5. Database select karo, **Import** tab kholo, is project ki file `database.sql` choose karo, **Go** dabao.
   - **Agar ye pehli dafa install hai**, to database.sql import karo.
   - **Agar aapke paas pehle wali original ZIP ki live database hai (asal data ke sath), to database.sql dobara import MAT karo** — warna data mit jayega. Is matched version ke liye **koi schema change nahi hai** (15 tables wesi hi hain), is liye koi `upgrade.sql` ki zaroorat nahi: sirf code files (PHP/CSS/JS) replace karo, database wesi hi chalegi. Agar aap nayi database bana rahe ho, to database.sql import karo.
6. `config.php` kholo aur apne MySQL details check karo. XAMPP default par ye theek hai:
   - Host: `127.0.0.1`, Database: `online_testing`, User: `root`, Password: (khali)
   - Agar aapka MySQL password alag hai, to `DB_PASS` mein wohi likho.
7. Browser mein kholo: `http://localhost/online-testing-website-matched/`
8. **Sign Up** se apna student account banao, ya neeche wale admin account se login karo.

## First Administrator Account (DEFAULT — foran change karo!)
- **Email:** admin@onlinetest.local
- **Password:** Admin@12345
- **⚠️ WARNING: Pehli login ke foran baad Dashboard → Account Settings se password change karo. Ye default password sirf demo/setup ke liye hai, isay live site par kabhi mat rakho.**
- Admin panel: login ke baad `Admin` link, ya seedha `http://localhost/online-testing-website-matched/admin/`
- Admin se naya admin banane ke liye: Admin → Students → kisi student par "Make admin" dabao.

## Kaise use karein? / How to use
- **Student:** Sign Up → Login → Dashboard → koi test kholo → Practice ya Exam mode chuno → Start → jawab do, Skip / Mark for Review use karo → Submit (confirmation dialog ayega) → Result page par score ring, marks, %, pass/fail aur explanations dekho (explanations sirf submission ke BAAD dikhte hain) → Download result (text file) ya Print.
- **Admin:** Admin Panel → Tests (create, publish/unpublish, delete) → Questions (add/edit/delete, MCQ + True/False, correct answer, explanation) → CSV Import (sample format import page par diya hai) → Classes/Boards, Subjects/Categories, Students, Attempts, Announcements, Settings.
- **Demo data:** Jo tests "DEMO" badge ke sath hain, wo sample/demo hain, kisi board ka official exam NAHI hain. Admin unhein edit ya delete kar sakta hai.

## Kya change kiya gaya hai is matched version mein? (Before → After, page by page)
Muse version ko reference bana kar ye changes kiye gaye hain. Original PHP build ki security (password_hash, PDO prepared statements, server-side scoring, take-page par correct answers/explanations kabhi nahi, admin 403) wesi hi rakhi gayi hai.

- **Home (`index.php`) — BEFORE:** sirf hero, announcements, Classes 1–12 grid, Latest Tests. **AFTER:** + stats strip (Registered students / Published tests / Classes), + Test Categories cards (Computer, GK, IQ, English, Maths, Science, Urdu) jo category filter par le jate hain, + "How it works" 3 steps (Sign up → Choose test → Result). Registered students counter tamam accounts gin ta hai, seeded admin samet — ye Muse version jesa hi hai, aur footer mein ye saaf likha hai.
- **Tests (`tests.php`) — BEFORE:** search sirf title se, category/difficulty/duration filters (class/subject sirf URL se). **AFTER:** + Class aur Subject ke dropdown filters bhi, + chapter display, + headings/labels EN/UR toggle mein.
- **Test detail (`test.php`) — BEFORE:** sirf title/description/duration/difficulty/pass/negative. **AFTER:** + Class, Subject, Category aur Chapter bhi dikhte hain, + headings EN/UR mein.
- **Quiz page (`take.php`) — BEFORE:** palette thi, lekin legend/counts nahi the; timer expiry par submit confirmation dialog auto-submit ko rok sakta tha. **AFTER:** + palette legend (Answered / Unanswered / Marked for review / Current) aur live counts (answered / unanswered / review), + submit confirmation dialog EN/UR mein, + **Exam Mode auto-submit ab confirmation ko bypass karta hai** (timer khatam hone par test khud submit ho jata hai, jese Muse version mein hai).
- **Result (`result.php`) — BEFORE:** sirf text marks/%, Print button. **AFTER:** + Muse jesi **score ring** (percentage ring ke andar, PASS/FAIL neeche), + summary (Correct/Incorrect/Unanswered), + **Download result (text file)** button — jo Muse version jesa text result download karta hai — + browser Print button bhi barkarar hai. Explanations ab bhi sirf submit ke baad dikhte hain.
- **Student Dashboard (`dashboard.php`) — BEFORE:** profile, available tests, history, bookmarks, certificates, badge. **AFTER:** + **Performance card** (total attempts / passed / average %), + profile mein **Selected class ke sath Board bhi** (class ke board se liya gaya, schema change ke baghair), + headings EN/UR mein.
- **Leaderboard / About / Contact / Classes / Subjects — BEFORE:** headings sirf English mein. **AFTER:** headings EN/UR toggle mein aate hain. Leaderboard aur badges ka formula wesa hi hai (Bronze 1 / Silver 5 / Gold 10 passed).
- **Language toggle (`config.php`, `includes/header.php`, `admin/header.php`) — BEFORE:** sirf ~20 labels, header toggle query kho deta tha, admin header sirf English (lang="en") tha. **AFTER:** ~60 labels EN/UR mein (home, tests, test detail, take, result, dashboard, leaderboard, about, contact), header toggle ab current page ke filters/query ko barkarar rakhta hai, admin header bhi lang/dir (UR = RTL) aur EN/UR toggle dikhata hai.
- **Admin Dashboard (`admin/index.php`) — BEFORE:** sirf Users/Tests/Questions/Attempts. **AFTER:** + Published Tests, Students, Completed Attempts ke stats cards bhi.
- **Design (`assets/css/style.css`) — BEFORE:** basic cards/grid. **AFTER:** + score ring, stats strip, palette legend, category cards, print CSS (print mein header/buttons chhupte hain). Light theme, blue accents, white backgrounds, rounded corners wese hi hain, fully responsive.
- **Database (`database.sql`) — BEFORE/AFTER: UNCHANGED.** 15 tables wesi hi hain: boards, classes, categories, subjects, users, tests, questions, options, attempts, student_answers, bookmarks, certificates, announcements, settings, contact_messages. Is liye **koi upgrade.sql nahi chahiye** aur live database par database.sql dobara import karne ki zaroorat nahi.

## File Structure
```
config.php                 DB + auth + language helpers (EN/UR, ~60 labels)
index.php, classes.php, subjects.php, tests.php, leaderboard.php, about.php, contact.php
register.php, login.php, logout.php, dashboard.php
test.php (start), take.php (quiz engine + timer + palette legend), save_answer.php (AJAX save), submit.php (server scoring), result.php (score ring + text download), bookmark.php
includes/header.php, includes/footer.php
admin/index.php, admin/tests.php, admin/questions.php, admin/classes.php, admin/subjects.php, admin/users.php, admin/attempts.php, admin/announcements.php, admin/settings.php, admin/import.php
assets/css/style.css, assets/js/app.js
database.sql, README_SETUP.md
```

## Ab bhi kya farq hai? (Honest differences — Muse vs PHP)
- **Data sync nahi:** dono copies ka data alag hai (upar likha hai).
- **Stack farq hai:** Muse version Muse ke andar chalti hai, isay XAMPP/MySQL chahiye. Is liye PHP version ko XAMPP par hi chalana hoga, Muse card se nahi kholega.
- **Result download:** Muse mein Download result (text file) hai; PHP mein ab wahi text download bhi hai, PLUS browser Print → Save as PDF bhi hai.
- **Certificates:** dono mein certificates dashboard cards hain (code + title), koi PDF certificate generator nahi hai.
- **Urdu coverage partial hai (dono mein):** main navigation/labels EN/UR mein toggle hote hain (Urdu script, RTL), lekin question content usi language mein rehta hai jis mein likha gaya tha. Poori Urdu translation abhi nahi hai.
- **Timer:** server start time se calculate hota hai; browser band ho jaye to attempt "Continue Test" se resume hoti hai jab tak submit na ho.
- **Security:** Password hashing, prepared statements, CSRF tokens, admin-only routes aur server-side scoring shamil hain — phir bhi production par jaane se pehle HTTPS, strong passwords aur regular backup zaroor rakho.
- **Home counter:** "Registered students" tamam accounts gin ta hai, seeded admin samet — ye Muse version jesa hi hai, aur isay chhupaya nahi gaya, footer mein saaf likha hai.
