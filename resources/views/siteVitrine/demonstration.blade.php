<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>EasyPoint — Créer mon espace</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    :root {
      --vp: #2e7d64;
      --vc: #e8f5f1;
      --vf: #1a5a48;
      --va: #40a088;
      --gf: #2d3a3e;
      --gm: #5a6b6f;
      --gl: #d4dfe2;
      --gtc: #eef3f5;
      --blanc: #ffffff;
      --t: all 0.28s cubic-bezier(.4,0,.2,1);
      --radius: 14px;
    }

    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--gtc);
      color: #1a2a2e;
      overflow-x: hidden;
      min-height: 100vh;
    }
    h1,h2,h3,h4,h5,h6 { font-family:'Syne',sans-serif; font-weight:700; letter-spacing:-.02em; }

    .navbar-glass {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1100;
      height: 68px;
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--gl);
      display: flex; align-items: center;
      transition: var(--t);
    }
    .navbar-glass.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .navbar-inner {
      width: 100%; max-width: 1400px;
      margin: 0 auto;
      padding: 0 40px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 16px;
    }

    .logo {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem; font-weight: 800;
      color: var(--gf); text-decoration: none;
      flex-shrink: 0;
    }
    .logo span { color: var(--vp); }

    .nav-links {
      display: flex; align-items: center; gap: 32px;
    }
    .nav-link-custom {
      font-weight: 500; color: var(--gm);
      text-decoration: none; font-size: .93rem;
      transition: var(--t); position: relative; padding-bottom: 2px;
    }
    .nav-link-custom::after {
      content: ''; position: absolute;
      bottom: -4px; left: 0; width: 0; height: 2px;
      background: var(--vp); border-radius: 2px;
      transition: width .3s ease;
    }
    .nav-link-custom:hover::after,
    .nav-link-custom.active::after { width: 100%; }
    .nav-link-custom:hover,
    .nav-link-custom.active { color: var(--vp); }

    .btn-prim {
      background: var(--vp); color: #fff; border: none;
      border-radius: 9999px; padding: .65rem 1.7rem;
      font-weight: 600; font-size: .88rem;
      display: inline-flex; align-items: center; gap: .45rem;
      text-decoration: none; transition: var(--t); cursor: pointer;
      font-family: 'Inter', sans-serif;
    }
    .btn-prim:hover {
      background: var(--vf); transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(46,125,100,.3); color: #fff;
    }
    .btn-out {
      background: transparent; border: 1.5px solid var(--gl);
      border-radius: 9999px; padding: .6rem 1.5rem;
      font-weight: 600; font-size: .88rem;
      display: inline-flex; align-items: center; gap: .45rem;
      text-decoration: none; color: var(--gf); transition: var(--t);
      font-family: 'Inter', sans-serif;
    }
    .btn-out:hover { border-color: var(--vp); color: var(--vp); transform: translateY(-2px); }

    .nav-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--vc); border: 1px solid rgba(46,125,100,.2);
      color: var(--vp); font-size: .7rem; font-weight: 700;
      letter-spacing: .08em; text-transform: uppercase;
      padding: .3rem .85rem; border-radius: 99px;
    }
    .nav-badge-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: var(--vp); animation: blink 2s infinite;
    }

    .hamburger {
      display: none;
      background: none; border: none; cursor: pointer;
      padding: 4px; color: var(--gf);
    }
    .mobile-menu {
      display: none;
      position: fixed;
      top: 68px; left: 0; right: 0;
      background: rgba(255,255,255,.98);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--gl);
      padding: 16px 24px 24px;
      flex-direction: column; gap: 4px;
      box-shadow: 0 12px 32px rgba(0,0,0,.08);
      z-index: 1100;
    }
    .mobile-menu.open { display: flex; }
    .mobile-menu .nav-link-custom {
      padding: 10px 0;
      border-bottom: 1px solid var(--gtc);
      font-size: .95rem;
    }
    .mobile-menu .nav-link-custom:last-of-type { border-bottom: none; }
    .mobile-menu-actions {
      display: flex; gap: 10px; flex-wrap: wrap;
      margin-top: 12px; padding-top: 12px;
      border-top: 1px solid var(--gl);
    }

    @media (max-width: 960px) {
      .nav-links { display: none; }
      .hamburger { display: block; }
      .navbar-inner { padding: 0 20px; }
    }

    .page-shell {
      display: flex;
      min-height: 100vh;
      padding-top: 68px;
    }

    .sidebar {
      background: var(--gf);
      position: fixed;
      top: 68px; left: 0;
      width: 320px;
      height: calc(100vh - 68px);
      overflow-y: auto;
      scrollbar-width: none;
      display: flex; flex-direction: column;
      padding: 48px 36px;
      z-index: 100;
    }
    .sidebar::-webkit-scrollbar { display: none; }

    .sb-deco { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
    .sb-deco-grid {
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(46,125,100,.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(46,125,100,.06) 1px, transparent 1px);
      background-size: 40px 40px;
    }
    .sb-deco-orb1 {
      position: absolute; bottom: -60px; right: -60px;
      width: 220px; height: 220px; border-radius: 50%;
      background: radial-gradient(circle, rgba(46,125,100,.2) 0%, transparent 70%);
    }
    .sb-deco-orb2 {
      position: absolute; top: 40px; left: -80px;
      width: 180px; height: 180px; border-radius: 50%;
      background: radial-gradient(circle, rgba(64,160,136,.1) 0%, transparent 70%);
    }
    .sidebar > * { position: relative; z-index: 1; }

    .sb-brand { margin-bottom: 40px; }
    .sb-brand-pill {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(46,125,100,.2); border: 1px solid rgba(46,125,100,.3);
      color: #6ecfb5; font-size: .65rem; font-weight: 800;
      letter-spacing: .12em; text-transform: uppercase;
      padding: .3rem .9rem; border-radius: 99px; margin-bottom: 16px;
    }
    .sb-brand-pill span {
      width: 5px; height: 5px; border-radius: 50%;
      background: #6ecfb5; animation: blink 2s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }
    .sb-title { font-size: 1.5rem; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 10px; }
    .sb-title em { font-style: normal; color: #6ecfb5; }
    .sb-desc { font-size: .8rem; color: rgba(255,255,255,.45); line-height: 1.7; }

    .sb-steps { display: flex; flex-direction: column; gap: 0; margin-bottom: 40px; }
    .sb-step {
      display: flex; align-items: flex-start; gap: 16px;
      padding: 14px 0; position: relative;
      cursor: pointer;
      transition: var(--t);
    }
    .sb-step::after {
      content: '';
      position: absolute; left: 17px; top: 46px;
      width: 2px; height: calc(100% - 14px);
      background: rgba(255,255,255,.08);
    }
    .sb-step:last-child::after { display: none; }

    .sb-step-num {
      width: 36px; height: 36px; border-radius: 50%;
      border: 2px solid rgba(255,255,255,.15);
      display: flex; align-items: center; justify-content: center;
      font-size: .75rem; font-weight: 800; color: rgba(255,255,255,.4);
      flex-shrink: 0; transition: var(--t); background: transparent;
    }
    .sb-step.done .sb-step-num { background: var(--vp); border-color: var(--vp); color: #fff; }
    .sb-step.active .sb-step-num {
      background: rgba(46,125,100,.25); border-color: var(--vp); color: #6ecfb5;
      box-shadow: 0 0 0 4px rgba(46,125,100,.15);
    }
    .sb-step-info { padding-top: 6px; }
    .sb-step-label {
      font-size: .7rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .1em;
      color: rgba(255,255,255,.28); margin-bottom: 2px; transition: var(--t);
    }
    .sb-step.active .sb-step-label { color: #6ecfb5; }
    .sb-step.done  .sb-step-label { color: rgba(255,255,255,.45); }
    .sb-step-name {
      font-size: .88rem; font-weight: 700;
      color: rgba(255,255,255,.3); transition: var(--t);
    }
    .sb-step.active .sb-step-name { color: #fff; }
    .sb-step.done  .sb-step-name { color: rgba(255,255,255,.55); }

    .sb-perks { margin-top: auto; display: flex; flex-direction: column; gap: 10px; }
    .sb-perk { display: flex; align-items: center; gap: 10px; font-size: .78rem; color: rgba(255,255,255,.4); }
    .sb-perk i { color: var(--va); font-size: .7rem; width: 14px; text-align: center; }

    .form-zone {
      margin-left: 320px;
      flex: 1;
      min-height: calc(100vh - 68px);
      display: flex; align-items: flex-start;
      padding: 52px 60px;
      background: var(--gtc);
    }
    .form-shell { width: 100%; max-width: 760px; margin: 0 auto; }

    .progress-bar-wrap {
      background: var(--gl); border-radius: 99px;
      height: 4px; margin-bottom: 48px; overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%; border-radius: 99px;
      background: linear-gradient(90deg, var(--vp), #6ecfb5);
      transition: width .5s cubic-bezier(.4,0,.2,1);
      width: 33.33%;
    }

    .step-header { margin-bottom: 36px; }
    .step-header-tag {
      font-size: .68rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .14em;
      color: var(--vp); display: flex; align-items: center; gap: 8px;
      margin-bottom: 10px;
    }
    .step-header-tag::before { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; }
    .step-header-title {
      font-size: clamp(1.5rem, 2.5vw, 2rem);
      font-weight: 900; color: var(--gf);
      line-height: 1.1; margin-bottom: 8px;
    }
    .step-header-desc { font-size: .88rem; color: var(--gm); line-height: 1.7; }

    .form-card {
      background: #fff; border-radius: 20px;
      padding: 40px;
      box-shadow: 0 2px 16px rgba(0,0,0,.04), 0 0 0 1px var(--gl);
      margin-bottom: 20px;
      transition: box-shadow .3s;
    }
    .form-card:focus-within {
      box-shadow: 0 4px 28px rgba(0,0,0,.07), 0 0 0 1px rgba(46,125,100,.2);
    }
    .form-card-title {
      font-size: .72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .12em;
      color: var(--gm); margin-bottom: 24px;
      display: flex; align-items: center; gap: 10px;
    }
    .form-card-title::after { content:''; flex:1; height:1px; background:var(--gl); }
    .form-card-title i { color: var(--vp); }

    .fg { display: grid; gap: 18px; margin-bottom: 0; }
    .fg + .fg { margin-top: 18px; }
    .fg-2 { grid-template-columns: 1fr 1fr; }
    .fg-3 { grid-template-columns: 1fr 1fr 1fr; }
    .fg-1 { grid-template-columns: 1fr; }
    .fg .span2 { grid-column: span 2; }
    .fg .span3 { grid-column: span 3; }

    .field { display: flex; flex-direction: column; gap: 6px; }
    .field-label {
      font-size: .72rem; font-weight: 700; color: var(--gf);
      display: flex; align-items: center; gap: 5px;
    }
    .field-label .req { color: var(--vp); }
    .field-label .opt {
      font-size: .62rem; font-weight: 500; color: var(--gm);
      background: var(--gtc); padding: 1px 6px; border-radius: 4px;
    }
    .field-input, .field-select, .field-textarea {
      width: 100%; background: var(--gtc);
      border: 1.5px solid transparent; border-radius: 10px;
      padding: 11px 14px; font-size: .875rem; color: var(--gf);
      font-family: 'Inter', sans-serif;
      outline: none; transition: var(--t); -webkit-appearance: none;
    }
    .field-input::placeholder, .field-textarea::placeholder { color: var(--gm); opacity:.5; }
    .field-input:focus, .field-select:focus, .field-textarea:focus {
      background: #fff; border-color: var(--vp);
      box-shadow: 0 0 0 3px rgba(46,125,100,.1);
    }
    .field-input.error, .field-select.error {
      border-color: #e05050; box-shadow: 0 0 0 3px rgba(224,80,80,.08);
    }
    .field-error { font-size: .68rem; color: #e05050; font-weight: 600; display: none; margin-top: 2px; }
    .field-error.show { display: block; }
    .field-hint { font-size: .68rem; color: var(--gm); line-height: 1.4; margin-top: 2px; }

    .field-select-wrap { position: relative; }
    .field-select-wrap::after {
      content: '\f078'; font-family:'Font Awesome 6 Free'; font-weight:900;
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      font-size: .55rem; color: var(--gm); pointer-events: none;
    }
    .field-select { cursor: pointer; padding-right: 32px; }

    .logo-upload {
      border: 2px dashed var(--gl); border-radius: 12px;
      padding: 28px 20px;
      display: flex; flex-direction: column; align-items: center; gap: 12px;
      cursor: pointer; transition: var(--t); position: relative; text-align: center;
    }
    .logo-upload:hover { border-color: var(--vp); background: var(--vc); }
    .logo-upload input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; }
    .logo-preview {
      width: 68px; height: 68px; border-radius: 16px;
      background: var(--gtc); display: flex; align-items: center; justify-content: center;
      overflow: hidden; border: 1px solid var(--gl);
    }
    .logo-preview img { width:100%; height:100%; object-fit:cover; display:none; }
    .logo-preview i { font-size: 1.4rem; color: var(--gm); }
    .logo-upload-txt { font-size: .78rem; color: var(--gm); }
    .logo-upload-txt strong { color: var(--vp); }

    .pwd-strength { margin-top: 8px; }
    .pwd-bar { display: flex; gap: 4px; margin-bottom: 4px; }
    .pwd-seg {
      flex: 1; height: 3px; border-radius: 2px;
      background: var(--gl); transition: background .3s;
    }
    .pwd-seg.weak   { background: #e05050; }
    .pwd-seg.medium { background: #f0b429; }
    .pwd-seg.strong { background: var(--vp); }
    .pwd-label { font-size: .68rem; color: var(--gm); font-weight: 600; }

    .pwd-wrap { position: relative; }
    .pwd-toggle {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      cursor: pointer; color: var(--gm); font-size: .8rem;
      background: none; border: none; padding: 4px; transition: color .2s;
    }
    .pwd-toggle:hover { color: var(--vp); }
    .pwd-wrap .field-input { padding-right: 44px; }

    .rgpd-box {
      display: flex; align-items: flex-start; gap: 12px;
      background: var(--vc); border: 1px solid rgba(46,125,100,.2);
      border-radius: 12px; padding: 16px;
    }
    .rgpd-box input[type="checkbox"] {
      width: 18px; height: 18px; flex-shrink:0; margin-top:1px;
      accent-color: var(--vp); cursor: pointer;
    }
    .rgpd-txt { font-size: .78rem; color: var(--gm); line-height: 1.6; }
    .rgpd-txt a { color: var(--vp); font-weight: 600; text-decoration: none; }
    .rgpd-txt a:hover { text-decoration: underline; }

    .info-note {
      display: flex; align-items: flex-start; gap: 12px;
      border-radius: 12px; padding: 16px 18px; margin-bottom: 20px;
    }
    .info-note.green {
      background: rgba(46,125,100,.07);
      border: 1px solid rgba(46,125,100,.18);
    }
    .info-note.blue {
      background: var(--gtc);
      border: 1px solid var(--gl);
    }
    .info-note i { color: var(--vp); font-size: .85rem; margin-top: 2px; flex-shrink: 0; }
    .info-note-body { font-size: .78rem; color: var(--gm); line-height: 1.6; }
    .info-note-body strong { color: var(--gf); display: block; margin-bottom: 2px; }

    .step-actions {
      display: flex; justify-content: space-between; align-items: center;
      margin-top: 12px; gap: 12px; flex-wrap: wrap;
    }
    .btn-prev {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 12px 24px; border-radius: 10px;
      font-size: .85rem; font-weight: 700;
      border: 1.5px solid var(--gl); color: var(--gm);
      background: #fff; cursor: pointer; transition: var(--t);
      font-family: 'Inter', sans-serif;
    }
    .btn-prev:hover { border-color: var(--vp); color: var(--vp); }
    .btn-next {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 13px 32px; border-radius: 10px;
      font-size: .88rem; font-weight: 700;
      background: var(--vp); color: #fff; border: none;
      cursor: pointer; transition: var(--t);
      font-family: 'Syne', sans-serif; letter-spacing: .01em;
    }
    .btn-next:hover { background: var(--vf); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(46,125,100,.3); }
    .btn-next:active { transform: none; }
    .btn-next:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }

    .step-panel { display: none; }
    .step-panel.active { display: block; animation: fadeSlide .35s ease; }
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateX(16px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    .success-screen {
      display: none; flex-direction: column; align-items: center;
      text-align: center; padding: 72px 32px;
    }
    .success-screen.active { display: flex; animation: fadeSlide .4s ease; }
    .success-circle {
      width: 92px; height: 92px; border-radius: 50%;
      background: var(--vc); color: var(--vp);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.4rem; margin-bottom: 28px;
      animation: popIn .5s cubic-bezier(.34,1.56,.64,1) .1s both;
      box-shadow: 0 0 0 12px rgba(46,125,100,.08);
    }
    @keyframes popIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
    .success-title { font-size: 2rem; font-weight: 900; color: var(--gf); margin-bottom: 12px; }
    .success-sub { font-size: .92rem; color: var(--gm); line-height: 1.75; max-width: 480px; margin-bottom: 8px; }
    .success-pending {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(240,180,41,.1); border: 1px solid rgba(240,180,41,.3);
      color: #b07d00; font-size: .78rem; font-weight: 700;
      padding: 8px 16px; border-radius: 99px; margin: 16px 0 28px;
    }
    .btn-login {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 14px 32px; border-radius: 10px;
      background: var(--vp); color: #fff;
      font-size: .9rem; font-weight: 700;
      text-decoration: none; transition: var(--t);
      font-family: 'Syne', sans-serif;
    }
    .btn-login:hover { background: var(--vf); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(46,125,100,.3); }

    .req-items-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 12px;
    }
    .req-item {
      display: flex; align-items: center; gap: 6px;
      font-size: .72rem; color: var(--gm); transition: color .2s;
    }
    .req-item i { font-size: .4rem; transition: color .2s; }

    /* ── TOAST NOTIFICATIONS ── */
    .toast-notification {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 99999;
      min-width: 320px;
      max-width: 450px;
      padding: 20px 24px;
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      gap: 12px;
      transform: translateX(calc(100% + 40px));
      transition: transform 0.4s cubic-bezier(.34,1.56,.64,1);
      pointer-events: auto;
      position: relative;
    }
    .toast-notification.show {
      transform: translateX(0);
    }
    .toast-notification .toast-header {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }
    .toast-notification .toast-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 1.1rem;
    }
    .toast-notification.success .toast-icon {
      background: #e8f5f1;
      color: #2e7d64;
    }
    .toast-notification.error .toast-icon {
      background: #fde8e8;
      color: #dc3545;
    }
    .toast-notification.info .toast-icon {
      background: #e8f0fe;
      color: #1a73e8;
    }
    .toast-notification .toast-body {
      flex: 1;
    }
    .toast-notification .toast-title {
      font-size: 0.88rem;
      font-weight: 800;
      color: #2d3a3e;
      margin-bottom: 2px;
    }
    .toast-notification .toast-message {
      font-size: 0.82rem;
      color: #5a6b6f;
      line-height: 1.5;
    }
    .toast-notification .toast-actions {
      display: flex;
      gap: 10px;
      margin-top: 4px;
      flex-wrap: wrap;
    }
    .toast-notification .toast-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 18px;
      border-radius: 99px;
      font-size: 0.78rem;
      font-weight: 700;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      font-family: 'Inter', sans-serif;
    }
    .toast-notification .toast-btn-primary {
      background: #2e7d64;
      color: #fff;
    }
    .toast-notification .toast-btn-primary:hover {
      background: #1a5a48;
      transform: translateY(-1px);
    }
    .toast-notification .toast-btn-secondary {
      background: #eef3f5;
      color: #2d3a3e;
    }
    .toast-notification .toast-btn-secondary:hover {
      background: #d4dfe2;
    }
    .toast-notification .toast-close {
      position: absolute;
      top: 12px;
      right: 14px;
      background: none;
      border: none;
      color: #5a6b6f;
      cursor: pointer;
      font-size: 0.9rem;
      padding: 4px;
      transition: color 0.2s;
    }
    .toast-notification .toast-close:hover {
      color: #2d3a3e;
    }
    .toast-notification .toast-progress {
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      background: #2e7d64;
      border-radius: 0 0 16px 16px;
      transition: width 0.1s linear;
    }
    .toast-notification.success .toast-progress { background: #2e7d64; }
    .toast-notification.error .toast-progress { background: #dc3545; }
    .toast-notification.info .toast-progress { background: #1a73e8; }

    /* ── SUCCESS MODAL OVERLAY ── */
    .success-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(8px);
      z-index: 99999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .success-overlay.show { display: flex; }
    .success-modal {
      background: #fff;
      border-radius: 24px;
      max-width: 500px;
      width: 100%;
      padding: 48px 40px 40px;
      text-align: center;
      box-shadow: 0 40px 80px rgba(0,0,0,0.3);
      animation: modalPopIn 0.5s cubic-bezier(.34,1.56,.64,1);
      position: relative;
    }
    @keyframes modalPopIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .success-modal .modal-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 2.4rem;
    }
    .success-modal .modal-icon.free { background: #e8f5f1; color: #2e7d64; }
    .success-modal .modal-icon.paid { background: #e8f0fe; color: #1a73e8; }

    .success-modal .modal-close {
      position: absolute;
      top: 16px;
      right: 20px;
      background: none;
      border: none;
      font-size: 1.3rem;
      color: #5a6b6f;
      cursor: pointer;
      padding: 4px 8px;
      transition: color 0.2s;
    }
    .success-modal .modal-close:hover { color: #2d3a3e; }

    .btn-modal-primary {
      background: #2e7d64;
      color: #fff;
      padding: 14px 32px;
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
      flex: 1;
      justify-content: center;
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
    }
    .btn-modal-primary:hover {
      background: #1a5a48;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(46,125,100,.3);
      color: #fff;
    }
    .btn-modal-secondary {
      background: transparent;
      border: 2px solid #d4dfe2;
      color: #2d3a3e;
      padding: 14px 32px;
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
      flex: 1;
      justify-content: center;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
    }
    .btn-modal-secondary:hover {
      border-color: var(--vp);
      color: var(--vp);
      transform: translateY(-2px);
    }

    @media(max-width: 1100px) {
      .sidebar { width: 280px; padding: 40px 28px; }
      .form-zone { margin-left: 280px; padding: 40px 40px; }
    }
    @media(max-width: 860px) {
      .sidebar {
        position: relative;
        top: 0;
        width: 100%;
        height: auto;
        padding: 32px 24px;
        margin-top: 68px;
      }
      .page-shell { flex-direction: column; }
      .form-zone { margin-left: 0; padding: 32px 20px; }
      .fg-2 { grid-template-columns: 1fr; }
      .fg-3 { grid-template-columns: 1fr; }
      .fg .span2, .fg .span3 { grid-column: span 1; }
      .sb-steps { flex-direction: row; overflow-x: auto; padding-bottom: 8px; gap: 16px; }
      .sb-step { flex-direction: column; align-items: center; text-align: center; padding: 8px 16px; min-width: 100px; }
      .sb-step::after { display: none; }
      .sb-step-label { font-size: .58rem; }
      .sb-perks { display: none; }
      .form-card { padding: 24px 20px; }
    }
    @media(max-width: 480px) {
      .form-zone { padding: 20px 16px; }
      .step-actions { flex-direction: column-reverse; }
      .btn-prev, .btn-next { width: 100%; justify-content: center; }
      .req-items-grid { grid-template-columns: 1fr; }
      .toast-notification {
        min-width: unset;
        width: calc(100% - 24px);
        right: 12px;
        bottom: 12px;
        padding: 16px 18px;
      }
      .success-modal { padding: 32px 20px 28px; }
      .btn-modal-primary, .btn-modal-secondary { width: 100%; }
    }
  </style>
</head>
<body>

<nav class="navbar-glass" id="topnav">
  <div class="navbar-inner">
    <a href="{{ route('siteVitrine.acceuil') }}" class="block px-8 py-2 m-0 whitespace-nowrap text-slate-700">
        <img src="{{ asset('images/logo.png') }}"
            class="inline transition-all duration-200 ease-nav-brand"
            alt="main_logo"
            style="height: 40px; width: auto;" />
    </a>

    <div class="nav-links">
      <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom">Accueil</a>
      <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom">À propos</a>
      <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom">Fonctionnalités</a>
      <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom">Tarifs</a>
      <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom">Contact</a>
    </div>

    <div style="display:flex;align-items:center;gap:12px;">
      <a href="{{route('login')}}" class="btn-out"><i class="fas fa-sign-in-alt"></i> Connexion</a>
      <button class="hamburger" id="mobileMenuBtn" aria-label="Menu">
        <i class="fas fa-bars" style="font-size:1.3rem;"></i>
      </button>
    </div>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom">Accueil</a>
    <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom">À propos</a>
    <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom">Fonctionnalités</a>
    <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom">Tarifs</a>
    <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom">Contact</a>
    <div class="mobile-menu-actions">
      <a href="{{route('login')}}" class="btn-prim" style="flex:1;justify-content:center;">
        <i class="fas fa-sign-in-alt"></i> Connexion
      </a>
    </div>
  </div>
</nav>

<div class="page-shell">

  <aside class="sidebar">
    <div class="sb-deco">
      <div class="sb-deco-grid"></div>
      <div class="sb-deco-orb1"></div>
      <div class="sb-deco-orb2"></div>
    </div>

    <div class="sb-brand">
      <h2 class="sb-title">Lancez votre<br><em>espace EasyPoint</em></h2>
      <p class="sb-desc">Configurez votre organisation en 2 étapes simples — activez votre compte dès validation par nos équipes.</p>
    </div>

    <div class="sb-steps">
      <div class="sb-step active" data-target="1">
        <div class="sb-step-num">1</div>
        <div class="sb-step-info">
          <div class="sb-step-label">Étape 1</div>
          <div class="sb-step-name">Votre entreprise</div>
        </div>
      </div>
      <div class="sb-step" data-target="2">
        <div class="sb-step-num">2</div>
        <div class="sb-step-info">
          <div class="sb-step-label">Étape 2</div>
          <div class="sb-step-name">Compte & sécurité</div>
        </div>
      </div>
    </div>

    <div class="sb-perks">
      <div class="sb-perk"><i class="fas fa-check-circle"></i> Activation sous 24h ouvrées</div>
      <div class="sb-perk"><i class="fas fa-check-circle"></i> Données hébergées localement</div>
      <div class="sb-perk"><i class="fas fa-check-circle"></i> Annulation à tout moment</div>
    </div>
  </aside>

  <main class="form-zone">
    <div class="form-shell">

      <div class="progress-bar-wrap">
        <div class="progress-bar-fill" id="progressFill"></div>
      </div>

      @if (session('success'))
        <div id="session-success-alert-div"
          style="background: #2e7d64; color: white; padding: 16px; border-radius: 10px; margin-bottom: 20px; position: relative;">
          {{ session('success') }}
          <button type="button"
            onclick="document.getElementById('session-success-alert-div').style.display='none'"
            style="position: absolute; top: 12px; right: 16px; background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
            ×
          </button>
        </div>
      @endif

      @if (session('error'))
        <div id="session-error-alert-div"
          style="background: #e05050; color: white; padding: 16px; border-radius: 10px; margin-bottom: 20px; position: relative;">
          {{ session('error') }}
          <button type="button"
            onclick="document.getElementById('session-error-alert-div').style.display='none'"
            style="position: absolute; top: 12px; right: 16px; background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
            ×
          </button>
        </div>
      @endif

      <form id="mainForm" method="POST" action="{{route('companies.store.company')}}" enctype="multipart/form-data">
        @csrf
        @method("POST")

        <!-- ════════ CHAMPS CACHÉS POUR LE PLAN ════════ -->
        <input type="hidden" name="plan_id" value="{{ $selectedPlanId ?? '' }}">
        <input type="hidden" name="plan_name" value="{{ $planName ?? 'Standard' }}">
        <input type="hidden" name="plan_price" value="{{ $planPrice ?? 0 }}">
        <input type="hidden" name="is_free" value="{{ $isFree ?? 'false' }}">
        <input type="hidden" name="duration_months" value="{{ $billing == 'yearly' ? 12 : 1 }}">
        <input type="hidden" name="payment_status" value="pending">

        <div class="step-panel active" id="step1">
          <div class="step-header">
            <div class="step-header-tag">Étape 1 sur 2</div>
            <h1 class="step-header-title">Votre entreprise</h1>
            <p class="step-header-desc">Commencez par les informations générales de votre organisation.</p>
          </div>

          <!-- ════════ RÉSUMÉ DU PLAN CHOISI ════════ -->
          <div class="info-note green" style="margin-bottom:20px;">
            <i class="fas fa-crown"></i>
            <div class="info-note-body">
              <strong>Plan sélectionné :
                @php
                  $displayPlanName = $planName ?? 'Standard';
                  $planNameMapping = [
                    'starter' => 'STARTER',
                    'growth' => 'GROWTH',
                    'business' => 'BUSINESS',
                    'free' => 'FREE',
                    'company-1782911409' => 'COMPANY',
                    'company-offre-1782916262' => 'COMPANY OFFRE',
                  ];
                  if (isset($planNameMapping[strtolower($displayPlanName)])) {
                    $displayPlanName = $planNameMapping[strtolower($displayPlanName)];
                  }
                @endphp
                {{ $displayPlanName }}
              </strong>

              @php
                $displayPrice = $planPrice ?? 0;
                $isFreePlan = $isFree ?? false;
                if ($displayPrice == 0 || $isFreePlan === true || $isFreePlan === 'true') {
                  $isFreePlan = true;
                }
              @endphp

              @if($isFreePlan)
                <span style="display:block;font-size:0.75rem;color:var(--vp);margin-top:4px;">
                  <i class="fas fa-check-circle"></i> Plan gratuit — aucun paiement requis
                </span>
              @else
                <span style="color:var(--vp);font-weight:700;display:block;margin-top:4px;">
                  {{ number_format($displayPrice, 0, ',', ' ') }} {{ $currency ?? 'XOF' }}
                  @if($billing == 'yearly')
                    <span style="font-weight:400;color:var(--gm);">/ an</span>
                  @else
                    <span style="font-weight:400;color:var(--gm);">/ mois</span>
                  @endif
                </span>
                <span style="display:block;font-size:0.75rem;color:var(--gm);margin-top:2px;">
                  <i class="fas fa-info-circle"></i> Un paiement sera requis après validation de votre inscription
                </span>
              @endif
            </div>
          </div>

          <div class="form-card">
            <div class="form-card-title"><i class="fas fa-building"></i> Identité de l'entreprise</div>
            <div class="fg fg-2">
              <div class="field span2">
                <label class="field-label">Nom de l'entreprise <span class="req">*</span></label>
                <input type="text" name="compagny_name" class="field-input"
                       placeholder="Ex : AGRO SARL, Clinique du Bénin..."
                       required id="f_company_name" />
                <span class="field-error" id="err_company_name">Ce champ est requis.</span>
              </div>
              <div class="field">
                <label class="field-label">Email professionnel <span class="req">*</span></label>
                <input type="email" name="compagny_email" class="field-input"
                       placeholder="contact@monentreprise.com"
                       required id="f_company_email" />
                <span class="field-error" id="err_company_email">Adresse email invalide.</span>
              </div>
              <div class="field">
                <label class="field-label">Téléphone <span class="opt">Optionnel</span></label>
                <input type="tel" name="phone" class="field-input" placeholder="+228 90 00 00 00" />
              </div>
              <div class="field">
                <label class="field-label">Site web <span class="opt">Optionnel</span></label>
                <input type="url" name="website" class="field-input" placeholder="https://monentreprise.com" />
              </div>
              <div class="field">
                <label class="field-label">Adresse <span class="opt">Optionnel</span></label>
                <input type="text" name="address" class="field-input" placeholder="Rue, quartier, ville..." />
              </div>
            </div>
          </div>

          <div class="form-card">
            <div class="form-card-title"><i class="fas fa-globe-africa"></i> Localisation & paramètres</div>
            <div class="fg fg-3">
              <div class="field">
                <label class="field-label">Pays <span class="req">*</span></label>
                <div class="field-select-wrap">
                  <select name="country" class="field-select field-input" required id="f_country">
                    <option value="" disabled selected>Sélectionner</option>
                    <option value="TG">🇹🇬 Togo</option>
                    <option value="BJ">🇧🇯 Bénin</option>
                    <option value="CI">🇨🇮 Côte d'Ivoire</option>
                    <option value="SN">🇸🇳 Sénégal</option>
                    <option value="ML">🇲🇱 Mali</option>
                    <option value="BF">🇧🇫 Burkina Faso</option>
                    <option value="GH">🇬🇭 Ghana</option>
                    <option value="NG">🇳🇬 Nigeria</option>
                    <option value="CM">🇨🇲 Cameroun</option>
                    <option value="FR">🇫🇷 France</option>
                    <option value="Other">Autre</option>
                  </select>
                </div>
                <span class="field-error" id="err_country">Veuillez sélectionner un pays.</span>
              </div>
              <div class="field">
                <label class="field-label">Devise <span class="req">*</span></label>
                <div class="field-select-wrap">
                  <select name="currency" class="field-select field-input" required id="f_currency">
                    <option value="" disabled selected>Sélectionner</option>
                    <option value="XOF">XOF — Franc CFA UEMOA</option>
                    <option value="XAF">XAF — Franc CFA CEMAC</option>
                    <option value="GHS">GHS — Cedi ghanéen</option>
                    <option value="NGN">NGN — Naira nigérian</option>
                    <option value="EUR">EUR — Euro</option>
                    <option value="USD">USD — Dollar américain</option>
                  </select>
                </div>
                <span class="field-error" id="err_currency">Veuillez sélectionner une devise.</span>
              </div>
              <div class="field">
                <label class="field-label">Fuseau horaire <span class="req">*</span></label>
                <div class="field-select-wrap">
                  <select name="timezone" class="field-select field-input" required id="f_fuseau">
                    <option value="" disabled selected>Sélectionner</option>
                    @foreach($timezones ?? [] as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <span class="field-error" id="err_fuseau">Veuillez sélectionner un fuseau horaire.</span>
              </div>
            </div>
          </div>

          <div class="form-card">
            <div class="form-card-title">
              <i class="fas fa-image"></i> Logo de l'entreprise
              <span style="font-weight:400;color:var(--gm);margin-left:2px;text-transform:none;letter-spacing:0;">(optionnel)</span>
            </div>
            <div class="logo-upload" id="logoZone">
              <input type="file" name="logo" accept="image/*" id="logoInput" />
              <div class="logo-preview" id="logoPreview">
                <i class="fas fa-image"></i>
                <img id="logoImg" src="" alt="logo" />
              </div>
              <div class="logo-upload-txt" id="logoTxt">
                Glissez votre logo ou <strong>cliquez pour parcourir</strong><br>
                <span style="font-size:.67rem;opacity:.55;">PNG, JPG, SVG — max 2 Mo</span>
              </div>
            </div>
          </div>

          <div class="step-actions">
            <div></div>
            <button type="button" class="btn-next" id="next1">
              Étape suivante <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <div class="step-panel" id="step2">
          <div class="step-header">
            <div class="step-header-tag">Étape 2 sur 2</div>
            <h2 class="step-header-title">Compte & sécurité</h2>
            <p class="step-header-desc">Définissez votre mot de passe administrateur. Il sera utilisé pour accéder à votre espace EasyPoint.</p>
          </div>

          <div class="form-card">
            <div class="form-card-title"><i class="fas fa-key"></i> Accès administrateur</div>

            <div class="info-note blue" style="margin-bottom:20px;">
              <i class="fas fa-info-circle"></i>
              <div class="info-note-body">
                Le compte administrateur sera créé avec l'adresse
                <strong id="displayEmail" style="display:inline;color:var(--vp);">—</strong>
                renseignée à l'étape 1.
              </div>
            </div>

            <div class="fg fg-1">
              <div class="field">
                <label class="field-label">Mot de passe <span class="req">*</span></label>
                <div class="pwd-wrap">
                  <input type="password" name="password" class="field-input"
                         placeholder="Minimum 8 caractères"
                         id="f_password" required autocomplete="new-password" />
                  <button type="button" class="pwd-toggle" id="togglePwd">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                  </button>
                </div>
                <div class="pwd-strength" id="pwdStrength" style="display:none;">
                  <div class="pwd-bar">
                    <div class="pwd-seg" id="seg1"></div>
                    <div class="pwd-seg" id="seg2"></div>
                    <div class="pwd-seg" id="seg3"></div>
                    <div class="pwd-seg" id="seg4"></div>
                  </div>
                  <span class="pwd-label" id="pwdLabel"></span>
                </div>
                <span class="field-error" id="err_password">Mot de passe requis (min. 8 caractères).</span>
              </div>
            </div>

            <div class="req-items-grid">
              <div class="req-item" id="req_len">
                <i class="fas fa-circle"></i> Minimum 8 caractères
              </div>
              <div class="req-item" id="req_up">
                <i class="fas fa-circle"></i> Une majuscule
              </div>
              <div class="req-item" id="req_num">
                <i class="fas fa-circle"></i> Un chiffre
              </div>
              <div class="req-item" id="req_spec">
                <i class="fas fa-circle"></i> Un caractère spécial
              </div>
            </div>
          </div>

          <div class="rgpd-box" style="margin-bottom:20px;">
            <input type="checkbox" id="rgpd" name="rgpd" required />
            <label class="rgpd-txt" for="rgpd">
              En soumettant ce formulaire, j'accepte les
              <a href="#">Conditions Générales d'Utilisation</a> et la
              <a href="#">Politique de Confidentialité</a> d'EasyPoint.
              Je comprends que mon compte sera activé après validation par l'équipe EasyPoint
              (statut <em>en attente</em>).
            </label>
          </div>

          <div class="step-actions">
            <button type="button" class="btn-prev" id="prev2"><i class="fas fa-arrow-left"></i> Retour</button>
            <button type="submit" class="btn-next" id="submitBtn">
              <i class="fas fa-rocket"></i> Créer mon espace
            </button>
          </div>
        </div>
      </form>

      <div class="success-screen" id="successScreen">
        <div class="success-circle"><i class="fas fa-check"></i></div>
        <h2 class="success-title">Inscription envoyée !</h2>
        <p class="success-sub">
          Votre demande a bien été reçue. Notre équipe va analyser votre dossier
          et vous contacter sous <strong>24h ouvrées</strong>.
        </p>
        <div class="success-pending">
          <i class="fas fa-clock"></i>
          Statut actuel : <strong>En attente de validation</strong>
        </div>
        <p style="font-size:.82rem;color:var(--gm);margin-bottom:28px;">
          Un email de confirmation a été envoyé à
          <strong id="successEmail" style="color:var(--gf);"></strong>.
        </p>
        <a href="#" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Aller à la connexion
        </a>
      </div>

    </div>
  </main>
</div>

<!-- ════════════════════════════════════════════════════════════════ -->
<!-- SUCCESS MODAL - PLAN GRATUIT -->
<!-- ════════════════════════════════════════════════════════════════ -->
@if(session('registration_type') === 'free')
<div class="success-overlay show" id="successModalFree">
  <div class="success-modal">
    <button class="modal-close" onclick="closeSuccessModal('successModalFree')">
      <i class="fas fa-times"></i>
    </button>

    <div class="modal-icon free">
      <i class="fas fa-check"></i>
    </div>
    <h2 style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:900;color:#2d3a3e;margin-bottom:8px;">
      Félicitations !
    </h2>
    <p style="font-size:0.95rem;color:#5a6b6f;line-height:1.6;margin-bottom:4px;">
      Vous venez de créer votre compte
    </p>
    <p style="font-size:1.1rem;font-weight:800;color:#2e7d64;margin-bottom:4px;">
      "{{ session('registration_plan_name', 'Gratuit') }}"
    </p>
    <p style="font-size:0.85rem;color:#5a6b6f;">
      ✅ Plan gratuit — Aucun paiement requis
    </p>

    @if(session('registration_email'))
      <p style="font-size:0.8rem;color:#5a6b6f;margin-top:12px;padding:12px;background:#eef3f5;border-radius:8px;">
        <i class="fas fa-envelope" style="color:#2e7d64;margin-right:6px;"></i>
        Un email de confirmation a été envoyé à <strong style="color:#2d3a3e;">{{ session('registration_email') }}</strong>
      </p>
    @endif

    <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;justify-content:center;">
      <a href="{{ route('login') }}" class="btn-modal-primary">
        <i class="fas fa-arrow-right"></i> Accéder à mon Dashboard
      </a>
      <a href="{{ route('siteVitrine.demonstration') }}" class="btn-modal-secondary">
        <i class="fas fa-plus"></i> Ajouter une nouvelle compagnie
      </a>
    </div>
  </div>
</div>
@endif

<!-- ════════════════════════════════════════════════════════════════ -->
<!-- SUCCESS MODAL - PLAN PAYANT (Paiement requis) -->
<!-- ════════════════════════════════════════════════════════════════ -->
@if(session('registration_type') === 'pending_payment')
<div class="success-overlay show" id="successModalPaid">
  <div class="success-modal">
    <button class="modal-close" onclick="closeSuccessModal('successModalPaid')">
      <i class="fas fa-times"></i>
    </button>

    <div class="modal-icon paid">
      <i class="fas fa-credit-card"></i>
    </div>
    <h2 style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:900;color:#2d3a3e;margin-bottom:8px;">
      💳 Paiement requis
    </h2>
    <p style="font-size:0.95rem;color:#5a6b6f;line-height:1.6;margin-bottom:4px;">
      Vous avez choisi le plan
    </p>
    <p style="font-size:1.1rem;font-weight:800;color:#2e7d64;margin-bottom:4px;">
      "{{ session('registration_plan_name', 'Standard') }}"
    </p>
    @if(session('registration_plan_price') && session('registration_plan_price') > 0)
      <p style="font-size:0.85rem;color:#5a6b6f;">
        Montant : {{ number_format(session('registration_plan_price'), 0, ',', ' ') }} XOF
      </p>
    @endif
    <p style="font-size:0.85rem;color:#5a6b6f;margin-top:8px;">
      Pour activer votre espace, veuillez procéder au paiement.
    </p>

    @if(session('registration_email'))
      <p style="font-size:0.8rem;color:#5a6b6f;margin-top:12px;padding:12px;background:#eef3f5;border-radius:8px;">
        <i class="fas fa-envelope" style="color:#2e7d64;margin-right:6px;"></i>
        Un email de confirmation a été envoyé à <strong style="color:#2d3a3e;">{{ session('registration_email') }}</strong>
      </p>
    @endif

    <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;justify-content:center;">
      <a href="{{ route('subscription.payment', ['company' => session('registration_company_id')]) }}" class="btn-modal-primary">
        <i class="fas fa-credit-card"></i> Payer maintenant
      </a>
      <a href="{{ route('login') }}" class="btn-modal-secondary">
        <i class="fas fa-arrow-right"></i> Accéder à mon espace
      </a>
    </div>
  </div>
</div>
@endif

<!-- ════════════════════════════════════════════════════════════════ -->
<!-- SUCCESS MODAL - PLAN PAYANT (Paiement réussi) -->
<!-- ════════════════════════════════════════════════════════════════ -->
@if(session('registration_type') === 'paid_success')
<div class="success-overlay show" id="successModalPaidSuccess">
  <div class="success-modal">
    <button class="modal-close" onclick="closeSuccessModal('successModalPaidSuccess')">
      <i class="fas fa-times"></i>
    </button>

    <div class="modal-icon paid">
      <i class="fas fa-check-circle"></i>
    </div>
    <h2 style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:900;color:#2d3a3e;margin-bottom:8px;">
        Félicitations !
    </h2>
    <p style="font-size:0.95rem;color:#5a6b6f;line-height:1.6;margin-bottom:4px;">
      Vous venez d'effectuer le paiement pour le plan
    </p>
    <p style="font-size:1.1rem;font-weight:800;color:#2e7d64;margin-bottom:4px;">
      "{{ session('registration_plan_name', 'Standard') }}"
    </p>
    @if(session('registration_plan_price') && session('registration_plan_price') > 0)
      <p style="font-size:0.85rem;color:#5a6b6f;">
        Montant : {{ number_format(session('registration_plan_price'), 0, ',', ' ') }} XOF
      </p>
    @endif
    <p style="font-size:0.85rem;color:#5a6b6f;margin-top:8px;">
      ✅ Paiement confirmé — Bienvenue sur votre espace EasyPoint !
    </p>

    @if(session('registration_email'))
      <p style="font-size:0.8rem;color:#5a6b6f;margin-top:12px;padding:12px;background:#eef3f5;border-radius:8px;">
        <i class="fas fa-envelope" style="color:#2e7d64;margin-right:6px;"></i>
        Un email de confirmation a été envoyé à <strong style="color:#2d3a3e;">{{ session('registration_email') }}</strong>
      </p>
    @endif

    <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;justify-content:center;">
      <a href="{{ route('dashboard') }}" class="btn-modal-primary">
        <i class="fas fa-rocket"></i> Accéder à mon Dashboard
      </a>
    </div>
  </div>
</div>
@endif

<script>
  // ═══════════════════════════════════════════════
  // NAVBAR & MENU MOBILE
  // ═══════════════════════════════════════════════

  const topnav = document.getElementById('topnav');
  window.addEventListener('scroll', () => {
    topnav.classList.toggle('scrolled', window.scrollY > 10);
  }, { passive: true });

  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      mobileBtn.querySelector('i').className = open ? 'fas fa-times' : 'fas fa-bars';
      mobileBtn.querySelector('i').style.fontSize = '1.3rem';
    });
  }
  document.querySelectorAll('#mobileMenu a').forEach(a =>
    a.addEventListener('click', () => {
      mobileMenu.classList.remove('open');
      mobileBtn.querySelector('i').className = 'fas fa-bars';
    })
  );

  // ═══════════════════════════════════════════════
  // SUCCESS MODAL - FERMETURE
  // ═══════════════════════════════════════════════

  function closeSuccessModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // Fermer en cliquant à l'extérieur pour tous les modals
  document.addEventListener('DOMContentLoaded', function() {
    const modals = ['successModalFree', 'successModalPaid', 'successModalPaidSuccess'];
    modals.forEach(id => {
      const overlay = document.getElementById(id);
      if (overlay) {
        overlay.addEventListener('click', function(e) {
          if (e.target === this) {
            closeSuccessModal(id);
          }
        });
      }
    });
  });

  // ═══════════════════════════════════════════════
  // SYSTÈME DE NOTIFICATIONS TOAST
  // ═══════════════════════════════════════════════

  class ToastManager {
    constructor() {
      this.container = null;
      this.toasts = [];
      this.maxToasts = 3;
      this.initContainer();
    }

    initContainer() {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      this.container.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
      `;
      document.body.appendChild(this.container);
    }

    show(options) {
      const {
        title,
        message,
        type = 'success',
        actions = [],
        duration = 6000,
        icon = null,
        onAction = null,
      } = options;

      if (this.toasts.length >= this.maxToasts) {
        const oldest = this.toasts.shift();
        oldest.remove();
      }

      const toast = this.createToast({ title, message, type, actions, duration, icon, onAction });
      this.container.appendChild(toast);
      this.toasts.push(toast);

      requestAnimationFrame(() => {
        toast.classList.add('show');
      });

      return toast;
    }

    createToast({ title, message, type, actions, duration, icon, onAction }) {
      const toast = document.createElement('div');
      toast.className = `toast-notification ${type}`;
      toast.style.pointerEvents = 'auto';
      toast.style.position = 'relative';

      const defaultIcons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle',
      };
      const iconClass = icon || defaultIcons[type] || defaultIcons.info;

      toast.innerHTML = `
        <button class="toast-close" aria-label="Fermer">
          <i class="fas fa-times"></i>
        </button>
        <div class="toast-header">
          <div class="toast-icon">
            <i class="${iconClass}"></i>
          </div>
          <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
            <div class="toast-actions">
              ${actions.map(action => `
                <button class="toast-btn ${action.class || 'toast-btn-primary'}"
                        data-action="${action.id || 'default'}">
                  ${action.icon ? `<i class="${action.icon}"></i>` : ''}
                  ${action.label}
                </button>
              `).join('')}
            </div>
          </div>
        </div>
        <div class="toast-progress" style="width:100%;"></div>
      `;

      toast.querySelector('.toast-close').addEventListener('click', () => {
        this.removeToast(toast);
      });

      toast.querySelectorAll('.toast-actions .toast-btn').forEach((btn, index) => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const action = actions[index];
          if (action && action.handler) {
            action.handler(e);
          }
          if (onAction) {
            onAction(action?.id || 'default', toast);
          }
          if (action?.closeOnAction !== false) {
            this.removeToast(toast);
          }
        });
      });

      let startTime = Date.now();
      let remaining = duration;
      let progressInterval = null;

      const startProgress = () => {
        const progressBar = toast.querySelector('.toast-progress');
        const updateProgress = () => {
          const elapsed = Date.now() - startTime;
          const percent = Math.max(0, ((remaining - elapsed) / remaining) * 100);
          progressBar.style.width = Math.max(0, percent) + '%';
          if (elapsed >= remaining) {
            clearInterval(progressInterval);
            this.removeToast(toast);
          }
        };
        progressInterval = setInterval(updateProgress, 50);
      };

      toast.addEventListener('mouseenter', () => {
        const elapsed = Date.now() - startTime;
        remaining = Math.max(0, remaining - elapsed);
        clearInterval(progressInterval);
      });

      toast.addEventListener('mouseleave', () => {
        startTime = Date.now();
        startProgress();
      });

      startProgress();

      return toast;
    }

    removeToast(toast) {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.remove();
          const index = this.toasts.indexOf(toast);
          if (index > -1) this.toasts.splice(index, 1);
        }
      }, 400);
    }

    success(title, message, actions = [], duration = 6000) {
      return this.show({ title, message, type: 'success', actions, duration });
    }

    error(title, message, actions = [], duration = 6000) {
      return this.show({ title, message, type: 'error', actions, duration });
    }

    info(title, message, actions = [], duration = 6000) {
      return this.show({ title, message, type: 'info', actions, duration });
    }
  }

  // Instance globale
  const toastManager = new ToastManager();

  // ═══════════════════════════════════════════════
  // GESTION DES ÉTAPES
  // ═══════════════════════════════════════════════

  let currentStep = 1;
  const totalSteps = 2;
  const panels = [null,
    document.getElementById('step1'),
    document.getElementById('step2')
  ];
  const sbSteps = document.querySelectorAll('.sb-step');
  const progress = document.getElementById('progressFill');
  const form = document.getElementById('mainForm');

  function goTo(n) {
    if (n < 1 || n > totalSteps) return;
    panels[currentStep]?.classList.remove('active');
    sbSteps[currentStep - 1]?.classList.remove('active');
    if (n > currentStep) sbSteps[currentStep - 1]?.classList.add('done');
    else if (n < currentStep) sbSteps[currentStep - 1]?.classList.remove('done');
    currentStep = n;
    panels[currentStep].classList.add('active');
    sbSteps[currentStep - 1].classList.add('active');
    progress.style.width = (currentStep / totalSteps * 100) + '%';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  sbSteps.forEach((step, idx) => {
    step.addEventListener('click', () => {
      const target = parseInt(step.getAttribute('data-target'));
      if (target && target !== currentStep) {
        if (target > currentStep) {
          if (currentStep === 1 && validateStep1()) {
            goTo(target);
          }
        } else {
          goTo(target);
        }
      }
    });
  });

  document.getElementById('next1').addEventListener('click', () => { if (validateStep1()) goTo(2); });
  document.getElementById('prev2').addEventListener('click', () => goTo(1));

  // ═══════════════════════════════════════════════
  // VALIDATION DES ÉTAPES
  // ═══════════════════════════════════════════════

  function validateStep1() {
    let ok = true;
    ok = assertRequired(document.getElementById('f_company_name'), 'err_company_name') && ok;
    ok = assertEmail(document.getElementById('f_company_email'), 'err_company_email') && ok;
    ok = assertRequired(document.getElementById('f_country'), 'err_country') && ok;
    ok = assertRequired(document.getElementById('f_currency'), 'err_currency') && ok;
    ok = assertRequired(document.getElementById('f_fuseau'), 'err_fuseau') && ok;
    return ok;
  }

  function validateStep2() {
    let ok = true;
    const pwd = document.getElementById('f_password');
    const rgpd = document.getElementById('rgpd');
    if (!pwd.value || pwd.value.length < 8) { showErr(pwd, 'err_password'); ok = false; }
    else clearErr(pwd, 'err_password');
    if (!rgpd.checked) {
      alert('Veuillez accepter les conditions d\'utilisation pour continuer.');
      ok = false;
    }
    return ok;
  }

  function assertRequired(el, errId) {
    if (!el || !el.value || el.value.trim() === '') { showErr(el, errId); return false; }
    clearErr(el, errId); return true;
  }

  function assertEmail(el, errId) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(el.value)) { showErr(el, errId); return false; }
    clearErr(el, errId); return true;
  }

  function showErr(el, errId) {
    if (el) el.classList.add('error');
    const errEl = document.getElementById(errId);
    if (errEl) errEl.classList.add('show');
  }

  function clearErr(el, errId) {
    if (el) el.classList.remove('error');
    const errEl = document.getElementById(errId);
    if (errEl) errEl.classList.remove('show');
  }

  // ═══════════════════════════════════════════════
  // MOT DE PASSE - AFFICHAGE / MASQUAGE
  // ═══════════════════════════════════════════════

  function setupToggle(btnId, inputId, iconId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.addEventListener('click', () => {
      const inp = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (inp && icon) {
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
      }
    });
  }
  setupToggle('togglePwd', 'f_password', 'eyeIcon');

  // ═══════════════════════════════════════════════
  // INDICATEUR DE FORCE DU MOT DE PASSE
  // ═══════════════════════════════════════════════

  const pwdInput = document.getElementById('f_password');
  if (pwdInput) {
    pwdInput.addEventListener('input', function () {
      const v = this.value;
      const wrap = document.getElementById('pwdStrength');
      const label = document.getElementById('pwdLabel');
      if (!wrap) return;
      if (!v) { wrap.style.display = 'none'; return; }
      wrap.style.display = 'block';
      const checks = {
        len: v.length >= 8,
        up: /[A-Z]/.test(v),
        num: /[0-9]/.test(v),
        spec: /[^A-Za-z0-9]/.test(v),
      };
      ['len','up','num','spec'].forEach(k => {
        const el = document.getElementById('req_' + k);
        if (el) {
          el.style.color = checks[k] ? 'var(--vp)' : 'var(--gm)';
          const icon = el.querySelector('i');
          if (icon) icon.style.color = checks[k] ? 'var(--vp)' : 'var(--gl)';
        }
      });
      const score = Object.values(checks).filter(Boolean).length;
      ['seg1','seg2','seg3','seg4'].forEach((id, i) => {
        const el = document.getElementById(id);
        if (el) {
          el.className = 'pwd-seg';
          if (i < score) {
            if (score <= 1) el.classList.add('weak');
            else if (score <= 2) el.classList.add('medium');
            else el.classList.add('strong');
          }
        }
      });
      if (label) {
        const texts = ['','Faible','Moyen','Bon','Excellent'];
        label.textContent = texts[score] || '';
        label.style.color = score <= 1 ? '#e05050' : score <= 2 ? '#f0b429' : 'var(--vp)';
      }
    });
  }

  // ═══════════════════════════════════════════════
  // UPLOAD DU LOGO
  // ═══════════════════════════════════════════════

  const logoInput = document.getElementById('logoInput');
  if (logoInput) {
    logoInput.addEventListener('change', function () {
      const f = this.files[0];
      if (!f) return;
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.getElementById('logoImg');
        if (img) {
          img.src = e.target.result;
          img.style.display = 'block';
          const previewIcon = document.querySelector('#logoPreview i');
          if (previewIcon) previewIcon.style.display = 'none';
          const logoTxt = document.getElementById('logoTxt');
          if (logoTxt) {
            logoTxt.innerHTML = `<strong style="color:var(--vp)"><i class="fas fa-check-circle"></i> ${f.name}</strong><br>
             <span style="font-size:.67rem;opacity:.55;">Cliquez pour changer</span>`;
          }
        }
      };
      reader.readAsDataURL(f);
    });
  }

  const logoZone = document.getElementById('logoZone');
  if (logoZone) {
    logoZone.addEventListener('dragover', e => { e.preventDefault(); logoZone.style.borderColor = 'var(--vp)'; });
    logoZone.addEventListener('dragleave', () => { logoZone.style.borderColor = ''; });
    logoZone.addEventListener('drop', e => {
      e.preventDefault(); logoZone.style.borderColor = '';
      const f = e.dataTransfer.files[0];
      if (f && f.type.startsWith('image/') && logoInput) {
        logoInput.files = e.dataTransfer.files;
        logoInput.dispatchEvent(new Event('change'));
      }
    });
  }

  // ═══════════════════════════════════════════════
  // AFFICHAGE DE L'EMAIL DANS L'ÉTAPE 2
  // ═══════════════════════════════════════════════

  const emailInput = document.getElementById('f_company_email');
  const displayEmail = document.getElementById('displayEmail');
  if (emailInput && displayEmail) {
    emailInput.addEventListener('input', function () {
      displayEmail.textContent = this.value || '—';
    });
    displayEmail.textContent = emailInput.value || '—';
  }

  // ═══════════════════════════════════════════════
  // NETTOYAGE DES ERREURS AU FOCUS
  // ═══════════════════════════════════════════════

  document.querySelectorAll('.field-input, .field-select').forEach(el => {
    el.addEventListener('input', () => el.classList.remove('error'));
    el.addEventListener('change', () => el.classList.remove('error'));
  });

  // ═══════════════════════════════════════════════
  // GESTION DES PLANS ET NOTIFICATIONS
  // ═══════════════════════════════════════════════

  function formatPlanName(name) {
    if (!name) return 'Standard';

    const planNames = {
      'starter': 'STARTER',
      'growth': 'GROWTH',
      'business': 'BUSINESS',
      'free': 'Free',
      'company-1782911409': 'COMPANY',
      'company-offre-1782916262': 'COMPANY OFFRE',
    };

    if (planNames[name.toLowerCase()]) {
      return planNames[name.toLowerCase()];
    }

    return name.charAt(0).toUpperCase() + name.slice(1);
  }

  // ═══════════════════════════════════════════════
  // INTERCEPTEUR DE SOUMISSION DU FORMULAIRE
  // ═══════════════════════════════════════════════

  if (form) {
    form.addEventListener('submit', function(e) {
      // Le formulaire sera soumis normalement
      // Le contrôleur gère la redirection avec les sessions
    });
  }
</script>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
