// Small helpers: confirm for destructive actions, loading state on submit.
document.addEventListener('click',function(ev){
  var el=ev.target.closest('[data-confirm]'); if(el && !confirm(el.getAttribute('data-confirm'))) ev.preventDefault();
});
document.addEventListener('submit',function(ev){
  var b=ev.target.querySelector('button[type=submit]'); if(b){ b.disabled=true; b.textContent='Please wait…'; }
});
