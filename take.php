<?php require 'config.php'; require_login(); $user=current_user();
$aid=(int)($_GET['attempt_id']??0); $st=db()->prepare('SELECT a.*,t.title,t.duration_minutes,t.negative_marking FROM attempts a JOIN tests t ON t.id=a.test_id WHERE a.id=? AND a.user_id=? AND a.status="in_progress"'); $st->execute([$aid,$user['id']]); $a=$st->fetch();
if(!$a) redirect('dashboard.php');
$order=json_decode($a['question_order'],true); $qids=$order['order']??[];
// Load questions WITHOUT correct answers or explanations
$qs=[]; foreach($qids as $qid){ $s=db()->prepare('SELECT id,question_text,question_type,marks FROM questions WHERE id=?'); $s->execute([$qid]); $q=$s->fetch(); if(!$q) continue; $os=db()->prepare('SELECT id,option_text FROM options WHERE question_id=?'); $os->execute([$qid]); $all=$os->fetchAll(); $map=[]; foreach($all as $o) $map[$o['id']]=$o; $ordered=[]; foreach(($order['options'][$qid]??[]) as $oid) if(isset($map[$oid])) $ordered[]=$map[$oid]; $q['options']=$ordered; $qs[]=$q; }
$ans=db()->prepare('SELECT question_id,selected_option_id,marked_review FROM student_answers WHERE attempt_id=?'); $ans->execute([$aid]); $saved=[]; foreach($ans->fetchAll() as $r) $saved[$r['question_id']]=['opt'=>$r['selected_option_id'],'rev'=>(int)$r['marked_review']];
$remaining=max(0, $a['duration_minutes']*60 - (time()-strtotime($a['started_at'])));
$page_title='Taking: '.$a['title']; include 'includes/header.php'; ?>
<h1><?= e($a['title']) ?> <span class="badge"><?= e(ucfirst($a['mode'])) ?> Mode</span></h1>
<p><?= t('time_left') ?>: <span class="timer" id="timer"></span> <span class="muted" id="counts"></span></p>
<div class="legend"><span><i style="background:#dbeafe"></i><?= t('answered') ?></span><span><i style="background:#fef3c7"></i><?= t('review') ?></span><span><i style="background:#fff;border:1px solid #cbd5e1"></i><?= t('unanswered') ?></span><span><i style="background:#eef4ff;outline:3px solid #1565d8"></i><?= t('current') ?></span></div>
<div class="palette" id="palette"></div>
<div class="card" id="qbox"></div>
<div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn2" id="prevBtn">← Previous</button><button class="btn2" id="skipBtn">Skip</button><button class="btn2" id="markBtn">Mark for Review</button><button class="btn" id="nextBtn">Next →</button></div>
<form method="post" action="submit.php" id="submitForm" onsubmit="return window.__autoSubmit || confirm('<?= addslashes(t('submit_confirm')) ?>')"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="attempt_id" value="<?= $aid ?>"><button class="btn" style="margin-top:14px;background:#137333">Submit Test</button></form>
<script>
const QS=<?= json_encode($qs) ?>, SAVED=<?= json_encode($saved) ?>, AID=<?= $aid ?>, CSRF=<?= json_encode(csrf_token()) ?>;
let idx=0, remaining=<?= $remaining ?>;
function render(){
 const q=QS[idx]; if(!q){document.getElementById('qbox').innerHTML='<p>No questions in this test.</p>';return;}
 let html='<h3>Question '+(idx+1)+' of '+QS.length+'</h3><p>'+q.question_text.replace(/</g,'&lt;')+'</p>';
 q.options.forEach(o=>{ const sel=SAVED[q.id]&&SAVED[q.id].opt==o.id; html+='<label class="opt '+(sel?'sel':'')+'"><input type="radio" name="opt" value="'+o.id+'" '+(sel?'checked':'')+' onchange="saveAns('+q.id+','+o.id+')"> '+o.option_text.replace(/</g,'&lt;')+'</label>'; });
 document.getElementById('qbox').innerHTML=html;
 let pal=''; QS.forEach((qq,i)=>{ let cls='pal'; if(SAVED[qq.id]&&SAVED[qq.id].opt) cls+=' done'; if(SAVED[qq.id]&&SAVED[qq.id].rev) cls+=' review'; if(i===idx) cls+=' current'; pal+='<button type="button" class="'+cls+'" onclick="idx='+i+';render()">'+(i+1)+'</button>'; });
 document.getElementById('palette').innerHTML=pal;
 let ans=0,rev=0; QS.forEach(qq=>{ if(SAVED[qq.id]&&SAVED[qq.id].opt) ans++; if(SAVED[qq.id]&&SAVED[qq.id].rev) rev++; });
 document.getElementById('counts').textContent='<?= t('answered') ?>: '+ans+' · <?= t('unanswered') ?>: '+(QS.length-ans)+' · <?= t('review') ?>: '+rev;
}
async function saveAns(qid,oid,rev){
 const fd=new FormData(); fd.append('csrf',CSRF); fd.append('attempt_id',AID); fd.append('question_id',qid); if(oid) fd.append('option_id',oid); if(rev!==undefined) fd.append('marked_review',rev?1:0);
 await fetch('save_answer.php',{method:'POST',body:fd});
 if(oid) SAVED[qid]={opt:oid,rev:(SAVED[qid]?SAVED[qid].rev:0)}; if(rev!==undefined){ if(!SAVED[qid]) SAVED[qid]={opt:null,rev:rev?1:0}; else SAVED[qid].rev=rev?1:0; }
 render();
}
document.getElementById('prevBtn').onclick=()=>{ if(idx>0){idx--;render();} };
document.getElementById('nextBtn').onclick=()=>{ if(idx<QS.length-1){idx++;render();} };
document.getElementById('skipBtn').onclick=()=>{ if(idx<QS.length-1){idx++;render();} };
document.getElementById('markBtn').onclick=()=>{ const q=QS[idx]; const cur=SAVED[q.id]?SAVED[q.id].rev:0; saveAns(q.id,null,cur?0:1); };
function tick(){ if(remaining<=0){ window.__autoSubmit=true; document.getElementById('submitForm').submit(); return; } const m=Math.floor(remaining/60), s=remaining%60; document.getElementById('timer').textContent=m+':'+String(s).padStart(2,'0'); remaining--; setTimeout(tick,1000); }
render(); tick();
</script>
<?php include 'includes/footer.php'; ?>
