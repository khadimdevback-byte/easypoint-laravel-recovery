<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EasyPoint — Fonctionnalités</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

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
      --t: all 0.32s cubic-bezier(.4,0,.2,1);
    }

    html { scroll-behavior:smooth; }
    body { font-family:'Inter',sans-serif; background:#fff; color:#1a2a2e; overflow-x:hidden; }
    h1,h2,h3,h4,h5,h6 { font-family:'Syne',sans-serif; font-weight:700; letter-spacing:-.02em; }

    /* ── NAVBAR ── */
    .navbar-glass { position:sticky; top:0; z-index:1100; background:rgba(255,255,255,.96); backdrop-filter:blur(16px); border-bottom:1px solid var(--gl); transition:var(--t); }
    .navbar-glass.scrolled { box-shadow:0 4px 20px rgba(0,0,0,.08); }
    .logo { font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:var(--gf); text-decoration:none; }
    .logo span { color:var(--vp); }
    .nav-link-custom { font-weight:500; color:var(--gm); text-decoration:none; font-size:.95rem; transition:var(--t); position:relative; padding-bottom:2px; }
    .nav-link-custom::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:2px; background:var(--vp); border-radius:2px; transition:width .3s ease; }
    .nav-link-custom:hover::after,.nav-link-custom.active::after { width:100%; }
    .nav-link-custom:hover,.nav-link-custom.active { color:var(--vp); }
    .btn-prim { background:var(--vp); color:#fff; border:none; border-radius:9999px; padding:.65rem 1.7rem; font-weight:600; font-size:.88rem; display:inline-flex; align-items:center; gap:.45rem; text-decoration:none; transition:var(--t); }
    .btn-prim:hover { background:var(--vf); transform:translateY(-2px); box-shadow:0 8px 24px rgba(46,125,100,.3); color:#fff; }
    .btn-out { background:transparent; border:1.5px solid var(--gl); border-radius:9999px; padding:.65rem 1.7rem; font-weight:600; font-size:.88rem; display:inline-flex; align-items:center; gap:.45rem; text-decoration:none; color:var(--gf); transition:var(--t); }
    .btn-out:hover { border-color:var(--vp); color:var(--vp); transform:translateY(-2px); }

    /* ── BREADCRUMB ── */
    .breadcrumb-strip { background:var(--gtc); border-bottom:1px solid var(--gl); padding:12px 0; }
    .bc-item { font-size:.78rem; color:var(--gm); }
    .bc-item.active { color:var(--vp); font-weight:600; }
    .bc-sep { color:var(--gl); margin:0 6px; font-size:.7rem; }

    /* ── SECTION TAGS ── */
    .stag { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:var(--vp); margin-bottom:8px; display:flex; align-items:center; gap:8px; }
    .stag::before { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; display:inline-block; }
    .stag.center { justify-content:center; }
    .stag.center::before { display:none; }
    .stag.center::after { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; display:inline-block; }
    .stitle { font-size:clamp(1.5rem,2.5vw,2.2rem); font-weight:900; color:var(--gf); line-height:1.15; }

    /* ════════════════════════════════════
       1. HERO — centré, sobre, aéré
    ════════════════════════════════════ */
    .hero-feat {
      padding: 88px 0 72px;
      text-align: center;
      background: #fff;
      border-bottom: 1px solid var(--gl);
      position: relative;
      overflow: hidden;
    }
    .hero-feat-dots {
      position: absolute; inset: 0;
      background-image: radial-gradient(circle, var(--gl) 1px, transparent 1px);
      background-size: 32px 32px;
      opacity: .45;
      pointer-events: none;
    }
    .hero-feat-fade {
      position: absolute; inset: 0;
      background: radial-gradient(ellipse 70% 80% at 50% 50%, #fff 40%, transparent 100%);
      pointer-events: none;
    }
    .hero-feat-content { position: relative; z-index: 1; }
    .hero-feat-pill {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--vc); border: 1px solid rgba(46,125,100,.2);
      color: var(--vp); font-size: .7rem; font-weight: 700;
      letter-spacing: .12em; text-transform: uppercase;
      padding: .4rem 1.1rem; border-radius: 9999px; margin-bottom: 28px;
    }
    .hero-feat-pill i { font-size: .65rem; }
    .hero-feat-h1 {
      font-size: clamp(2.2rem, 5vw, 3.6rem);
      font-weight: 900; color: var(--gf);
      line-height: 1.06; letter-spacing: -.03em;
      margin-bottom: 20px;
    }
    .hero-feat-h1 em { font-style: normal; color: var(--vp); }
    .hero-feat-sub {
      font-size: 1rem; color: var(--gm);
      line-height: 1.75; max-width: 540px;
      margin: 0 auto 36px;
    }
    .hero-feat-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .hero-feat-scroll {
      margin-top: 52px;
      display: flex; flex-direction: column; align-items: center; gap: 8px;
    }
    .hero-feat-scroll span { font-size: .7rem; color: var(--gm); font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
    .scroll-line {
      width: 1px; height: 40px;
      background: linear-gradient(to bottom, var(--vp), transparent);
      animation: scrollpulse 2s ease-in-out infinite;
    }
    @keyframes scrollpulse { 0%,100%{opacity:.3;transform:scaleY(1)} 50%{opacity:1;transform:scaleY(1.1)} }

    /* ════════════════════════════════════
       2. NAV ANCRE — onglets sticky sous le hero
    ════════════════════════════════════ */
    .feat-nav {
      position: sticky; top: 73px; z-index: 900;
      background: #fff; border-bottom: 1px solid var(--gl);
      padding: 0;
    }
    @media(max-width: 768px) {
      .feat-nav { top: 62px; }
    }
    .feat-nav-inner {
      display: flex; gap: 0; overflow-x: auto;
      scrollbar-width: none;
    }
    .feat-nav-inner::-webkit-scrollbar { display: none; }
    .feat-nav-btn {
      flex-shrink: 0;
      padding: 16px 24px;
      font-size: .82rem; font-weight: 600;
      color: var(--gm); text-decoration: none;
      border-bottom: 2px solid transparent;
      transition: var(--t); white-space: nowrap;
    }
    .feat-nav-btn:hover { color: var(--vp); }
    .feat-nav-btn.active { color: var(--vp); border-bottom-color: var(--vp); }

    /* ════════════════════════════════════
       3. SPOTLIGHT — alternance gauche/droite
    ════════════════════════════════════ */
    .spotlight-section { padding: 96px 0; }
    .spotlight-section:nth-child(even) { background: var(--gtc); }
    .spotlight-section:nth-child(odd)  { background: #fff; }

    .spotlight-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    .spotlight-row.reverse { direction: rtl; }
    .spotlight-row.reverse > * { direction: ltr; }

    .spotlight-text {}
    .spotlight-num {
      font-family: 'Syne', sans-serif;
      font-size: 5rem; font-weight: 900;
      color: var(--vc); line-height: 1;
      margin-bottom: -12px;
      user-select: none;
    }
    .spotlight-title {
      font-size: clamp(1.4rem, 2.5vw, 2rem);
      font-weight: 900; color: var(--gf);
      line-height: 1.12; margin-bottom: 16px;
    }
    .spotlight-desc {
      font-size: .92rem; color: var(--gm);
      line-height: 1.8; margin-bottom: 24px;
    }
    .spotlight-checks { list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 10px; }
    .spotlight-checks li {
      display: flex; align-items: flex-start; gap: 10px;
      font-size: .85rem; color: var(--gf);
    }
    .spotlight-checks li i { color: var(--vp); font-size: .75rem; margin-top: 3px; flex-shrink: 0; }
    .spotlight-link {
      display: inline-flex; align-items: center; gap: 8px;
      font-size: .82rem; font-weight: 700; color: var(--vp);
      text-decoration: none; transition: var(--t);
    }
    .spotlight-link:hover { gap: 12px; }
    .spotlight-link i { font-size: .75rem; }

    /* Visuel mockup */
    .spotlight-visual {
      background: var(--gf);
      border-radius: 20px;
      overflow: hidden;
      aspect-ratio: 4/3;
      position: relative;
      box-shadow: 0 24px 64px rgba(0,0,0,.12);
    }
    .spotlight-section:nth-child(odd) .spotlight-visual { background: var(--gf); }
    .spotlight-section:nth-child(even) .spotlight-visual { background: #fff; border: 1px solid var(--gl); }

    /* Mockup 1 — Tableau de bord présences */
    .mock-dash {
      padding: 20px;
      height: 100%;
      display: flex; flex-direction: column; gap: 12px;
    }
    .mock-topbar {
      display: flex; justify-content: space-between; align-items: center;
      padding: 10px 14px;
      background: rgba(255,255,255,.06);
      border-radius: 10px;
    }
    .mock-topbar-title { font-size: .7rem; font-weight: 700; color: #6ecfb5; letter-spacing: .08em; }
    .mock-topbar-date { font-size: .65rem; color: rgba(255,255,255,.35); }
    .mock-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; }
    .mock-stat {
      background: rgba(255,255,255,.06);
      border-radius: 8px; padding: 10px 12px;
      border: 1px solid rgba(255,255,255,.06);
    }
    .mock-stat-val { font-family:'Syne',sans-serif; font-size: 1.3rem; font-weight: 900; color: #fff; line-height: 1; }
    .mock-stat-lbl { font-size: .55rem; color: rgba(255,255,255,.4); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }
    .mock-stat.green .mock-stat-val { color: #6ecfb5; }
    .mock-stat.amber .mock-stat-val { color: #f0b429; }
    .mock-list { display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .mock-row {
      display: flex; align-items: center; gap: 10px;
      background: rgba(255,255,255,.04);
      border-radius: 8px; padding: 8px 12px;
    }
    .mock-avatar {
      width: 28px; height: 28px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: .6rem; font-weight: 800; color: #fff; flex-shrink: 0;
    }
    .mock-name { font-size: .72rem; color: rgba(255,255,255,.75); flex: 1; }
    .mock-badge {
      font-size: .58rem; font-weight: 700; padding: 2px 8px;
      border-radius: 99px; text-transform: uppercase; letter-spacing: .04em;
    }
    .mock-badge.present { background: rgba(64,160,136,.2); color: #6ecfb5; }
    .mock-badge.absent  { background: rgba(240,180,41,.15); color: #f0b429; }
    .mock-badge.retard  { background: rgba(220,80,60,.15); color: #e06050; }
    .mock-progress-bar {
      height: 4px; border-radius: 2px;
      background: rgba(255,255,255,.08);
      overflow: hidden; margin-top: auto;
    }
    .mock-progress-fill {
      height: 100%; border-radius: 2px;
      background: linear-gradient(90deg, var(--vp), #6ecfb5);
      width: 78%;
    }

    /* Mockup 2 — Pointage mobile */
    .mock-mobile {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      height: 100%; padding: 24px; gap: 16px;
      background: var(--gtc);
      min-height: 260px;
    }
    .mock-phone {
      width: 160px; max-width: 90%;
      background: var(--gf);
      border-radius: 24px;
      padding: 16px 12px;
      box-shadow: 0 16px 40px rgba(0,0,0,.15);
    }
    .mock-phone-notch {
      width: 40px; height: 5px;
      background: rgba(255,255,255,.15);
      border-radius: 99px; margin: 0 auto 12px;
    }
    .mock-phone-time {
      text-align: center;
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem; font-weight: 900; color: #fff;
      margin-bottom: 4px;
    }
    .mock-phone-date {
      text-align: center; font-size: .6rem;
      color: rgba(255,255,255,.4); margin-bottom: 14px;
    }
    .mock-phone-btn {
      width: 100%; padding: 9px;
      background: var(--vp); border-radius: 10px;
      text-align: center; font-size: .65rem;
      font-weight: 700; color: #fff; margin-bottom: 8px;
    }
    .mock-phone-loc {
      display: flex; align-items: center; gap: 5px;
      background: rgba(255,255,255,.06); border-radius: 8px;
      padding: 7px 10px;
    }
    .mock-phone-loc i { font-size: .6rem; color: var(--va); }
    .mock-phone-loc span { font-size: .58rem; color: rgba(255,255,255,.5); }
    .mock-gps-chip {
      display: flex; align-items: center; gap: 8px;
      background: #fff; border-radius: 10px;
      padding: 8px 14px;
      box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    .mock-gps-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--vp); }
    .mock-gps-txt { font-size: .68rem; color: var(--gf); font-weight: 600; }

    /* Mockup 3 — Rapport analytique */
    .mock-report {
      padding: 24px;
      height: 100%;
      background: #fff;
      display: flex; flex-direction: column; gap: 14px;
    }
    .mock-report-header {
      display: flex; justify-content: space-between; align-items: center;
    }
    .mock-report-title { font-size: .75rem; font-weight: 800; color: var(--gf); }
    .mock-report-period {
      font-size: .6rem; color: var(--vp); font-weight: 700;
      background: var(--vc); padding: 3px 10px; border-radius: 99px;
    }
    .mock-chart-bars {
      display: flex; align-items: flex-end; gap: 8px;
      height: 80px; padding-bottom: 2px;
    }
    .mock-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .mock-bar { width: 100%; border-radius: 4px 4px 0 0; background: var(--vc); transition: var(--t); }
    .mock-bar.active { background: var(--vp); }
    .mock-bar-lbl { font-size: .52rem; color: var(--gm); }
    .mock-kpi-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .mock-kpi-box {
      background: var(--gtc); border-radius: 8px; padding: 10px 12px;
    }
    .mock-kpi-val { font-family:'Syne',sans-serif; font-size: 1.2rem; font-weight: 900; color: var(--gf); }
    .mock-kpi-lbl { font-size: .58rem; color: var(--gm); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }
    .mock-alert {
      display: flex; align-items: center; gap: 8px;
      background: rgba(46,125,100,.08); border: 1px solid rgba(46,125,100,.2);
      border-radius: 8px; padding: 9px 12px;
      font-size: .65rem; color: var(--vp); font-weight: 600;
    }
    .mock-alert i { font-size: .65rem; }

    @media(max-width: 900px) {
      .spotlight-section { padding: 56px 0; }
      .spotlight-row { grid-template-columns: 1fr; gap: 36px; }
      .spotlight-row.reverse { direction: ltr; }
      .spotlight-num { font-size: 3.5rem; margin-bottom: -6px; }
      .spotlight-visual { aspect-ratio: 3/2; }
    }
    @media(max-width: 576px) {
      .spotlight-section { padding: 44px 0; }
      .spotlight-num { font-size: 2.8rem; }
      .spotlight-visual { aspect-ratio: auto; min-height: 240px; }
      .mock-topbar-date { display: none; }
      .mock-stats { grid-template-columns: repeat(3,1fr); }
      .mock-stat-val { font-size: 1rem; }
    }

    /* ════════════════════════════════════
       4. GRILLE SECONDAIRE — 6 cards
    ════════════════════════════════════ */
    .secondary-band { padding: 80px 0; background: var(--gf); }
    .secondary-header { margin-bottom: 48px; }
    .secondary-header .stag { color: #6ecfb5; }
    .secondary-header .stag::before { background: #6ecfb5; }
    .secondary-header .stitle { color: #fff; }
    .sec-grid {
      display: grid;
      grid-template-columns: repeat(3,1fr);
      gap: 1px;
      background: rgba(255,255,255,.07);
      border-radius: 16px;
      overflow: hidden;
    }
    .sec-card {
      background: rgba(255,255,255,.03);
      padding: 32px 28px;
      transition: var(--t);
      cursor: default;
    }
    .sec-card:hover { background: rgba(255,255,255,.07); }
    .sec-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: rgba(46,125,100,.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; color: #6ecfb5;
      margin-bottom: 18px;
    }
    .sec-title { font-size: .95rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
    .sec-text  { font-size: .82rem; color: rgba(255,255,255,.45); line-height: 1.65; }

    @media(max-width: 768px) {
      .secondary-band { padding: 52px 0; }
      .sec-grid { grid-template-columns: 1fr; }
      .sec-card { padding: 24px 20px; }
    }
    @media(max-width: 992px) and (min-width: 769px) {
      .sec-grid { grid-template-columns: repeat(2,1fr); }
    }

    /* ════════════════════════════════════
       5. TABLEAU COMPARATIF PLANS
    ════════════════════════════════════ */
    .plans-band { padding: 96px 0; background: #fff; }
    .plans-header { text-align: center; margin-bottom: 56px; }
    .plans-table-wrap { overflow-x: auto; }
    .plans-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      min-width: 640px;
    }
    .plans-table thead tr th {
      padding: 20px 24px;
      font-size: .82rem;
      font-weight: 800;
      text-align: left;
      border-bottom: 2px solid var(--gl);
    }
    .plans-table thead tr th:first-child {
      color: var(--gm);
      font-weight: 600;
    }
    .plans-table thead tr th:not(:first-child) {
      text-align: center;
      color: var(--gf);
    }
    .plan-th-name { font-family: 'Syne', sans-serif; font-size: .95rem; display: block; margin-bottom: 4px; }
    .plan-th-price { font-size: .72rem; color: var(--gm); font-weight: 500; }
    .plan-th-badge {
      display: inline-block; margin-top: 6px;
      font-size: .58rem; font-weight: 800;
      padding: 2px 10px; border-radius: 99px;
      background: var(--vp); color: #fff;
      text-transform: uppercase; letter-spacing: .05em;
    }
    .plans-table tbody tr { transition: background .2s; }
    .plans-table tbody tr:hover { background: var(--gtc); }
    .plans-table tbody td {
      padding: 14px 24px;
      font-size: .83rem;
      border-bottom: 1px solid var(--gl);
      color: var(--gf);
    }
    .plans-table tbody td:not(:first-child) { text-align: center; }
    .plans-table tbody tr:last-child td { border-bottom: none; }
    .feat-cat {
      background: var(--gtc);
      font-size: .68rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .1em;
      color: var(--gm);
      padding: 12px 24px !important;
      border-bottom: 1px solid var(--gl) !important;
    }
    .feat-cat td { padding: 12px 24px; color: var(--gm); }
    .check-yes { color: var(--vp); font-size: .85rem; }
    .check-no  { color: var(--gl); font-size: .85rem; }
    .check-partial { font-size: .7rem; font-weight: 700; color: var(--gm); }
    .col-pro { background: rgba(46,125,100,.04); }
    .col-pro-head { background: rgba(46,125,100,.06) !important; border-bottom: 2px solid var(--vp) !important; }

    /* ════════════════════════════════════
       6. CTA BAND
    ════════════════════════════════════ */
    .cta-band { background:var(--gf); padding:0; overflow:hidden; }
    .cta-inner { display:grid; grid-template-columns:1fr 420px; min-height:200px; }
    .cta-left { padding:56px 64px; position:relative; overflow:hidden; }
    .cta-left-bg { position:absolute; inset:0; background-image:linear-gradient(rgba(46,125,100,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(46,125,100,.06) 1px,transparent 1px); background-size:40px 40px; }
    .cta-left > * { position:relative; z-index:1; }
    .cta-title { font-size:clamp(1.4rem,2.2vw,1.9rem); font-weight:900; color:#fff; margin-bottom:6px; }
    .cta-sub   { font-size:.9rem; color:rgba(255,255,255,.5); margin-bottom:24px; }
    .cta-right { background:var(--vp); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; gap:12px; text-align:center; }
    .cta-right-icon { font-size:2rem; color:rgba(255,255,255,.3); margin-bottom:4px; }
    .cta-right p { font-size:.82rem; color:rgba(255,255,255,.65); line-height:1.5; }

    @media(max-width:992px) {
      .cta-inner { grid-template-columns: 1fr; }
      .cta-left  { padding: 48px 32px; }
      .cta-right { padding: 36px 32px; }
    }
    @media(max-width:768px){
      .cta-left  { padding: 40px 24px; }
      .cta-right { padding: 32px 24px; }
      .hero-feat { padding: 56px 0 48px; }
      .hero-feat-scroll { margin-top: 36px; }
      .plans-band { padding: 56px 0; }
    }
    @media(max-width:480px) {
      .hero-feat-actions { flex-direction: column; align-items: center; }
      .hero-feat-actions .btn-prim,
      .hero-feat-actions .btn-out { width: 100%; justify-content: center; }
      .feat-nav-btn { padding: 14px 16px; font-size: .76rem; }
    }

    /* ── FOOTER ── */
    .footer-modern { background:var(--gf); color:var(--gl); padding:4rem 0 2rem; }
    .footer-logo { font-family:'Syne',sans-serif; font-size:1.6rem; font-weight:800; color:#fff; text-decoration:none; }
    .footer-logo span { color:var(--vp); }
    .footer-desc { color:#9baeb3; line-height:1.6; margin-top:1rem; font-size:.88rem; }
    .social-icons a { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; background:rgba(255,255,255,.08); border-radius:50%; color:var(--gl); font-size:.8rem; transition:var(--t); text-decoration:none; }
    .social-icons a:hover { background:var(--vp); color:#fff; transform:translateY(-3px); }
    .footer-title { color:#fff; font-size:.95rem; font-weight:700; margin-bottom:1.1rem; }
    .footer-links { list-style:none; padding:0; margin:0; }
    .footer-links li { margin-bottom:.6rem; }
    .footer-links a { color:#9baeb3; text-decoration:none; font-size:.83rem; transition:var(--t); display:inline-flex; align-items:center; gap:.4rem; }
    .footer-links a:hover { color:var(--vp); transform:translateX(4px); }
    .footer-links a i { font-size:9px; color:var(--vp); }
    .footer-contact-info p { margin-bottom:.55rem; font-size:.83rem; color:#9baeb3; }
    .footer-contact-info i { width:22px; color:var(--vp); }
    .newsletter-form { display:flex; gap:.5rem; margin-top:1rem; }
    .newsletter-form input { flex:1; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.1); border-radius:9999px; padding:.65rem 1rem; color:#fff; font-size:.83rem; outline:none; }
    .newsletter-form input::placeholder { color:#7a949b; }
    .newsletter-form input:focus { border-color:var(--vp); }
    .newsletter-form button { background:var(--vp); border:none; border-radius:9999px; padding:.65rem 1.1rem; color:#fff; font-weight:600; cursor:pointer; transition:var(--t); }
    .newsletter-form button:hover { background:var(--vf); }
    .footer-bottom { border-top:1px solid rgba(255,255,255,.08); padding-top:1.4rem; margin-top:2rem; }
    .footer-copy { color:#7a949b; font-size:.75rem; }
    .footer-copy a { color:#7a949b; text-decoration:none; transition:var(--t); }
    .footer-copy a:hover { color:var(--vp); }

    /* ── REVEAL ── */
    .reveal { opacity:0; transform:translateY(28px); transition:opacity .65s ease, transform .65s ease; }
    .reveal.vis { opacity:1; transform:none; }
  </style>
</head>
<body>

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="navbar-glass py-3" id="topnav">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a href="{{ route('siteVitrine.acceuil') }}" class="block px-8 py-2 m-0 whitespace-nowrap text-slate-700" href="">
            <img src="{{ asset('images/logo.png') }}"
                class="inline transition-all duration-200 ease-nav-brand"
                alt="main_logo"
                style="height: 40px; width: auto;" />
        </a>
      <div class="d-none d-md-flex gap-4">
        <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom">Accueil</a>
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom">À propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom active">Fonctionnalités</a>
        {{-- <a href="{{route('siteVitrine.demonstration')}}" class="nav-link-custom">Démonstration</a> --}}
        <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom">Tarifs</a>
        <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom">Contact</a>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <a href="{{route('login')}}" class="btn-prim"><i class="fas fa-lock"></i> Connexion</a>
        <button class="d-md-none btn btn-link text-dark p-0" id="mobileMenuBtn"><i class="fas fa-bars fs-4"></i></button>
      </div>
    </div>
    <div id="mobileMenu" class="d-md-none mt-3" style="display:none;">
      <div class="d-flex flex-column gap-2 py-2">
        <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom py-2">Accueil</a>
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom py-2">À propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom py-2 active">Fonctionnalités</a>
        {{-- <a href="{{route('siteVitrine.demonstration')}}" class="nav-link-custom py-2">Démonstration</a> --}}
        <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom py-2">Tarifs</a>
        <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom py-2">Contact</a>
        <hr>
        <a href="{{route('login')}}" class="btn-prim text-center justify-content-center">Connexion</a>
      </div>
    </div>
  </div>
</nav>

<!-- ═══════════ BREADCRUMB ═══════════ -->
<div class="breadcrumb-strip">
  <div class="container">
    <span class="bc-item">Accueil</span>
    <span class="bc-sep"><i class="fas fa-chevron-right" style="font-size:9px;"></i></span>
    <span class="bc-item active">Fonctionnalités</span>
  </div>
</div>

<!-- ═══════════ 1. HERO CENTRÉ ═══════════ -->
<section class="hero-feat">
  <div class="hero-feat-dots"></div>
  <div class="hero-feat-fade"></div>
  <div class="container hero-feat-content">
    <div class="hero-feat-pill"><i class="fas fa-layer-group"></i> Plateforme complète</div>
    <h1 class="hero-feat-h1">Tout ce dont<br>vous avez <em>besoin</em></h1>
    <p class="hero-feat-sub">De la pointeuse au rapport analytique — EasyPoint centralise la gestion du temps, des présences et du capital humain dans un seul écosystème fluide.</p>
    <div class="hero-feat-actions">
      <a href="#spotlight" class="btn-prim"><i class="fas fa-compass"></i> Explorer les fonctions</a>
      {{-- <a href="#" class="btn-out"><i class="fas fa-play-circle"></i> Voir la démo</a> --}}
    </div>
    <div class="hero-feat-scroll">
      <span>Découvrir</span>
      <div class="scroll-line"></div>
    </div>
  </div>
</section>

<!-- ═══════════ NAV ANCRE ═══════════ -->
<nav class="feat-nav">
  <div class="container">
    <div class="feat-nav-inner">
      <a href="#pointage"    class="feat-nav-btn active">Pointage intelligent</a>
      <a href="#mobilite"    class="feat-nav-btn">Mobilité & GPS</a>
      <a href="#analytique"  class="feat-nav-btn">Analytique RH</a>
      <a href="#secondaires" class="feat-nav-btn">Autres fonctions</a>
      <a href="#plans"       class="feat-nav-btn">Comparatif plans</a>
    </div>
  </div>
</nav>

<!-- ═══════════ 2. SPOTLIGHT 1 — Pointage ═══════════ -->
<section class="spotlight-section" id="pointage">
  <div class="container">
    <div class="spotlight-row reveal" id="spotlight">
      <div class="spotlight-text">
        <div class="stag">Fonctionnalité phare</div>
        <div class="spotlight-num">01</div>
        <h2 class="spotlight-title">Tableau de bord<br>des présences en temps réel</h2>
        <p class="spotlight-desc">Suivez instantanément la présence de chaque collaborateur. Identifiez les absences, retards et anomalies sans effort — toutes les données s'actualisent automatiquement.</p>
        <ul class="spotlight-checks">
          <li><i class="fas fa-check-circle"></i>Vue d'ensemble par département, site ou équipe</li>
          <li><i class="fas fa-check-circle"></i>Alertes instantanées sur absences non justifiées</li>
          <li><i class="fas fa-check-circle"></i>Historique consultable jusqu'à 5 ans en arrière</li>
          <li><i class="fas fa-check-circle"></i>Export CSV, PDF ou API vers votre SIRH</li>
        </ul>
        <a href="#" class="spotlight-link">En savoir plus <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="spotlight-visual">
        <div class="mock-dash">
          <div class="mock-topbar">
            <span class="mock-topbar-title">Dashboard Présences</span>
            <span class="mock-topbar-date">Lundi 19 Mai · 09:14</span>
          </div>
          <div class="mock-stats">
            <div class="mock-stat green"><div class="mock-stat-val">142</div><div class="mock-stat-lbl">Présents</div></div>
            <div class="mock-stat amber"><div class="mock-stat-val">8</div><div class="mock-stat-lbl">Absents</div></div>
            <div class="mock-stat"><div class="mock-stat-val">5</div><div class="mock-stat-lbl">En retard</div></div>
          </div>
          <div class="mock-list">
            <div class="mock-row"><div class="mock-avatar" style="background:#2e7d64;">KA</div><span class="mock-name">Kossi Amevor</span><span class="mock-badge present">Présent</span></div>
            <div class="mock-row"><div class="mock-avatar" style="background:#40a088;">AF</div><span class="mock-name">Akosua Fiagbe</span><span class="mock-badge present">Présent</span></div>
            <div class="mock-row"><div class="mock-avatar" style="background:#f0b429;">YD</div><span class="mock-name">Yao Dogbé</span><span class="mock-badge absent">Absent</span></div>
            <div class="mock-row"><div class="mock-avatar" style="background:#e06050;">MT</div><span class="mock-name">Mawuli Togbé</span><span class="mock-badge retard">Retard</span></div>
          </div>
          <div class="mock-progress-bar"><div class="mock-progress-fill"></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ SPOTLIGHT 2 — Mobilité ═══════════ -->
<section class="spotlight-section" id="mobilite">
  <div class="container">
    <div class="spotlight-row reverse reveal">
      <div class="spotlight-text">
        <div class="stag">Sur le terrain</div>
        <div class="spotlight-num">02</div>
        <h2 class="spotlight-title">Pointage mobile<br>& géolocalisation</h2>
        <p class="spotlight-desc">Vos équipes terrain pointent depuis leur smartphone. La géolocalisation certifie que chaque pointage correspond bien à un site autorisé — fiabilité et flexibilité réunies.</p>
        <ul class="spotlight-checks">
          <li><i class="fas fa-check-circle"></i>Application iOS & Android, hors connexion inclus</li>
          <li><i class="fas fa-check-circle"></i>Périmètres GPS configurables par site</li>
          <li><i class="fas fa-check-circle"></i>Reconnaissance faciale optionnelle</li>
          <li><i class="fas fa-check-circle"></i>Synchronisation automatique au retour réseau</li>
        </ul>
        <a href="#" class="spotlight-link">Voir la démo mobile <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="spotlight-visual">
        <div class="mock-mobile">
          <div class="mock-phone">
            <div class="mock-phone-notch"></div>
            <div class="mock-phone-time">09:07</div>
            <div class="mock-phone-date">Lundi 19 Mai 2026</div>
            <div class="mock-phone-btn"><i class="fas fa-fingerprint"></i> Pointer l'arrivée</div>
            <div class="mock-phone-loc">
              <i class="fas fa-map-marker-alt"></i>
              <span>Siège Lomé — Zone autorisée</span>
            </div>
          </div>
          <div class="mock-gps-chip">
            <div class="mock-gps-dot"></div>
            <span class="mock-gps-txt">GPS vérifié · 6°07'N 1°13'E</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ SPOTLIGHT 3 — Analytique ═══════════ -->
<section class="spotlight-section" id="analytique">
  <div class="container">
    <div class="spotlight-row reveal">
      <div class="spotlight-text">
        <div class="stag">Intelligence RH</div>
        <div class="spotlight-num">03</div>
        <h2 class="spotlight-title">Rapports analytiques<br>& prédictifs</h2>
        <p class="spotlight-desc">Transformez vos données de présence en décisions stratégiques. Identifiez les tendances d'absentéisme, anticipez les pics de charge et optimisez vos plannings à l'avance.</p>
        <ul class="spotlight-checks">
          <li><i class="fas fa-check-circle"></i>Taux de présence hebdo, mensuel, annuel</li>
          <li><i class="fas fa-check-circle"></i>Détection IA des patterns anormaux</li>
          <li><i class="fas fa-check-circle"></i>Rapports personnalisables par manager</li>
          <li><i class="fas fa-check-circle"></i>Intégration paie — calcul automatique des heures</li>
        </ul>
        <a href="#" class="spotlight-link">Voir les rapports <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="spotlight-visual">
        <div class="mock-report">
          <div class="mock-report-header">
            <span class="mock-report-title">Analyse mensuelle</span>
            <span class="mock-report-period">Mai 2026</span>
          </div>
          <div class="mock-chart-bars">
            <div class="mock-bar-wrap"><div class="mock-bar" style="height:55%;"></div><span class="mock-bar-lbl">S1</span></div>
            <div class="mock-bar-wrap"><div class="mock-bar" style="height:72%;"></div><span class="mock-bar-lbl">S2</span></div>
            <div class="mock-bar-wrap"><div class="mock-bar active" style="height:88%;"></div><span class="mock-bar-lbl">S3</span></div>
            <div class="mock-bar-wrap"><div class="mock-bar" style="height:65%;"></div><span class="mock-bar-lbl">S4</span></div>
            <div class="mock-bar-wrap"><div class="mock-bar" style="height:45%;"></div><span class="mock-bar-lbl">S5</span></div>
          </div>
          <div class="mock-kpi-row">
            <div class="mock-kpi-box"><div class="mock-kpi-val">94,2%</div><div class="mock-kpi-lbl">Taux présence</div></div>
            <div class="mock-kpi-box"><div class="mock-kpi-val">−12%</div><div class="mock-kpi-lbl">Absentéisme</div></div>
          </div>
          <div class="mock-alert"><i class="fas fa-brain"></i> IA : pic prédit semaine 3 juin (+18%)</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ 4. GRILLE SECONDAIRE ═══════════ -->
<section class="secondary-band" id="secondaires">
  <div class="container">
    <div class="secondary-header reveal">
      <div class="stag">L'écosystème complet</div>
      <h2 class="stitle" style="color:#fff;">Toutes les fonctions<br>en un coup d'œil</h2>
    </div>
    <div class="sec-grid reveal">
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="sec-title">Planification & congés</div>
        <div class="sec-text">Gérez les demandes de congés, validez les plannings et visualisez les disponibilités sur un calendrier partagé en temps réel.</div>
      </div>
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-bell"></i></div>
        <div class="sec-title">Notifications intelligentes</div>
        <div class="sec-text">Alertes push, email ou SMS automatiques pour les absences, retards, validations de congés et anomalies détectées par l'IA.</div>
      </div>
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-plug"></i></div>
        <div class="sec-title">API & intégrations</div>
        <div class="sec-text">Connectez EasyPoint à votre SIRH, logiciel de paie ou ERP via une API REST documentée et des webhooks configurables.</div>
      </div>
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="sec-title">Sécurité & RGPD</div>
        <div class="sec-text">Chiffrement AES-256, hébergement souverain, journaux d'audit et gestion des droits d'accès granulaires par rôle.</div>
      </div>
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-users-cog"></i></div>
        <div class="sec-title">Multi-sites & multi-entités</div>
        <div class="sec-text">Gérez plusieurs établissements, filiales ou clients dans un seul espace avec des droits et vues personnalisés par site.</div>
      </div>
      <div class="sec-card">
        <div class="sec-icon"><i class="fas fa-file-invoice"></i></div>
        <div class="sec-title">Export & conformité légale</div>
        <div class="sec-text">Génération automatique des registres légaux, bulletins de présence et documents conformes aux normes du travail locales.</div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════ 5. TABLEAU COMPARATIF ═══════════ -->
<section class="plans-band" id="plans">
  <div class="container">
    <div class="plans-header reveal">
      <div class="stag center">Choisissez votre formule</div>
      <h2 class="stitle">Fonctionnalités par plan</h2>
    </div>
    <div class="plans-table-wrap reveal">
      <table class="plans-table">
        <thead>
          <tr>
            <th style="width:38%;">Fonctionnalité</th>
            <th style="width:20%;">
              <span class="plan-th-name">Starter</span>
              <span class="plan-th-price">Gratuit · jusqu'à 10 users</span>
            </th>
            <th class="col-pro-head" style="width:22%;">
              <span class="plan-th-name">Pro</span>
              <span class="plan-th-price">À partir de 15 000 F/mois</span>
              <span class="plan-th-badge">Populaire</span>
            </th>
            <th style="width:20%;">
              <span class="plan-th-name">Enterprise</span>
              <span class="plan-th-price">Sur devis</span>
            </th>
          </tr>
        </thead>
        <tbody>
          <!-- Présences -->
          <tr class="feat-cat"><td colspan="4">Gestion des présences</td></tr>
          <tr><td>Pointage web & badge</td><td><i class="fas fa-check check-yes"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Pointage mobile (iOS / Android)</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Géolocalisation & périmètres GPS</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Reconnaissance faciale</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><span class="check-partial">Option</span></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <!-- Analytique -->
          <tr class="feat-cat"><td colspan="4">Analytique & rapports</td></tr>
          <tr><td>Tableau de bord temps réel</td><td><span class="check-partial">Basique</span></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Rapports avancés & exports</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Prédictions IA & alertes</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-times check-no"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Rapports personnalisés par manager</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <!-- Intégrations -->
          <tr class="feat-cat"><td colspan="4">Intégrations & API</td></tr>
          <tr><td>API REST documentée</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Connecteur paie & SIRH</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><span class="check-partial">2 connecteurs</span></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Webhooks & automatisations</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-times check-no"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <!-- Support -->
          <tr class="feat-cat"><td colspan="4">Support & SLA</td></tr>
          <tr><td>Support par email</td><td><i class="fas fa-check check-yes"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Support prioritaire (chat & tél.)</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>Account manager dédié</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-times check-no"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
          <tr><td>SLA garanti 99,9 %</td><td><i class="fas fa-times check-no"></i></td><td class="col-pro"><i class="fas fa-check check-yes"></i></td><td><i class="fas fa-check check-yes"></i></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ═══════════ 6. CTA ═══════════ -->
{{-- <section class="cta-band">
  <div class="cta-inner">
    <div class="cta-left">
      <div class="cta-left-bg"></div>
      <h2 class="cta-title">Prêt à simplifier<br>votre gestion RH ?</h2>
      <p class="cta-sub">Démarrez gratuitement — aucune carte bancaire requise.</p>
      <div class="d-flex gap-3 flex-wrap">
        <a href="#" class="btn-prim" style="background:#fff;color:var(--vp);"><i class="fas fa-rocket"></i> Essai gratuit 14 jours</a>
        <a href="#" class="btn-out" style="border-color:rgba(255,255,255,.25);color:rgba(255,255,255,.75);"><i class="fas fa-headset"></i> Parler à un conseiller</a>
      </div>
    </div>
    <div class="cta-right">
      <div class="cta-right-icon"><i class="fas fa-shield-alt"></i></div>
      <p><strong style="color:#fff;display:block;font-size:1rem;margin-bottom:6px;">Sans engagement</strong>Annulation à tout moment · Données exportables · Migration accompagnée.</p>
    </div>
  </div>
</section> --}}

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="footer-modern">
  <div class="container">
    <div class="row g-4">
      <!-- Colonne Logo & Présentation -->
      <div class="col-lg-4 col-md-6">
        <a href="{{ route('siteVitrine.acceuil') }}" class="footer-logo">Easy<span>Point</span></a>
        <p class="footer-description mt-3">
          {{ $footer->description ?? 'La solution de suivi de présence intelligente pour les structures exigeantes. Performance, simplicité et rigueur.' }}
        </p>
        <div class="social-icons d-flex gap-3 mt-4">
          <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.linkedin.com/"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://x.com/@"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <!-- Colonne Produit -->
      <div class="col-lg-4 col-md-6">
        <h5 class="footer-title">Produit</h5>
        <ul class="footer-links">
            <li><a href="{{route('siteVitrine.acceuil')}}"><i class="fas fa-chevron-right"></i>Acceuil</a></li>
            <li><a href="{{route('siteVitrine.apropos')}}"><i class="fas fa-chevron-right"></i>A Propos</a></li>
            <li><a href="{{ route('siteVitrine.fonctionnalites') }}"><i class="fas fa-chevron-right"></i>Fonctionnalités</a></li>
            <li><a href="{{ route('siteVitrine.tarifs') }}"><i class="fas fa-chevron-right"></i>Tarifs & Offres</a></li>
        </ul>
      </div>

      <!-- Colonne Ressources -->
      {{-- <div class="col-lg-2 col-md-6">
        <h5 class="footer-title">Ressources</h5>
        <ul class="footer-links">
          <li><a href="#"><i class="fas fa-chevron-right"></i>Centre d'aide</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i>Documentation API</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i>Blog & Actualités</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i>Webinaires</a></li>
        </ul>
      </div> --}}

      <!-- Colonne Contact & Newsletter -->
      <div class="col-lg-4 col-md-6">
        <h5 class="footer-title">Contactez-nous</h5>
        <div class="footer-contact-info">
          @if($footer->email)
            <p><i class="fas fa-envelope"></i> {{ $footer->email }}</p>
          @else
            <p><i class="fas fa-envelope"></i> contact@easypoint.com</p>
          @endif

          @if($footer->phone)
            <p><i class="fas fa-phone-alt"></i> {{ $footer->phone }}</p>
          @else
            <p><i class="fas fa-phone-alt"></i> +228 90 00 00 00</p>
          @endif

          @if($footer->address)
            <p><i class="fas fa-map-marker-alt"></i> {{ $footer->address }}</p>
          @else
            <p><i class="fas fa-map-marker-alt"></i> Lomé, Togo</p>
          @endif
        </div>

        {{-- <h5 class="footer-title mt-4">Newsletter</h5>
        <div class="newsletter-form">
          <input type="email" placeholder="Votre adresse email" aria-label="Email newsletter">
          <button type="button" aria-label="S'inscrire"><i class="fas fa-paper-plane"></i></button>
        </div> --}}
        <p class="small-text mt-2">Recevez nos offres et actualités</p>
      </div>
    </div>

    <!-- Séparateur -->
    <div class="footer-divider"></div>

    <!-- Bas de page -->
    <div class="footer-bottom">
      <div class="footer-copyright">
        <i class="far fa-copyright"></i> {{ $footer->copyright_text ?? '2026 EasyPoint. Tous droits réservés.' }}
      </div>
      {{-- <div class="footer-bottom-links">
        <a href="#">Conditions générales</a>
        <span class="separator">|</span>
        <a href="#">Confidentialité</a>
        <span class="separator">|</span>
        <a href="#">Mentions légales</a>
      </div> --}}
      {{-- <div class="footer-credit">
        Propulsé avec <i class="fas fa-heart footer-heart"></i> pour les équipes modernes
      </div> --}}
    </div>
  </div>
</footer>

<style>
  /* Styles améliorés pour le footer */
  .footer-modern {
    background: var(--gf);
    color: var(--gl);
    padding: 4rem 0 2rem;
    position: relative;
    border-top: 1px solid rgba(255,255,255,0.08);
  }

  .footer-logo {
    font-family: 'Syne', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: #fff;
    text-decoration: none;
    display: inline-block;
    transition: var(--t);
  }

  .footer-logo:hover {
    color: #fff;
    transform: translateY(-2px);
  }

  .footer-logo span {
    color: var(--vp);
  }

  .footer-description {
    font-size: 0.85rem;
    line-height: 1.6;
    color: rgba(255,255,255,0.65);
    margin-bottom: 0;
  }

  .footer-title {
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
    font-family: 'Syne', sans-serif;
    position: relative;
    display: inline-block;
  }

  .footer-title::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--vp);
    border-radius: 2px;
  }

  .footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .footer-links li {
    margin-bottom: 0.7rem;
  }

  .footer-links a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.85rem;
    transition: var(--t);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .footer-links a i {
    font-size: 0.7rem;
    color: var(--vp);
    transition: var(--t);
  }

  .footer-links a:hover {
    color: #fff;
    transform: translateX(5px);
  }

  .footer-links a:hover i {
    transform: translateX(3px);
  }

  .footer-contact-info p {
    margin-bottom: 0.7rem;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.6);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .footer-contact-info p i {
    width: 25px;
    color: var(--vp);
    font-size: 0.9rem;
  }

  .social-icons a {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    transition: var(--t);
    font-size: 1rem;
  }

  .social-icons a:hover {
    background: var(--vp);
    color: #fff;
    transform: translateY(-3px);
  }

  .newsletter-form {
    display: flex;
    gap: 10px;
    margin-top: 0.5rem;
  }

  .newsletter-form input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.05);
    border-radius: 50px;
    color: #fff;
    font-size: 0.85rem;
    transition: var(--t);
  }

  .newsletter-form input:focus {
    outline: none;
    border-color: var(--vp);
    background: rgba(255,255,255,0.1);
  }

  .newsletter-form input::placeholder {
    color: rgba(255,255,255,0.4);
  }

  .newsletter-form button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: var(--vp);
    color: #fff;
    cursor: pointer;
    transition: var(--t);
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .newsletter-form button:hover {
    background: var(--vf);
    transform: translateY(-2px);
  }

  .small-text {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.4);
    margin-top: 0.5rem;
  }

  .footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    margin: 2rem 0 1.5rem;
  }

  .footer-bottom {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
  }

  .footer-bottom-links {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.8rem;
  }

  .footer-bottom-links a {
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    transition: var(--t);
  }

  .footer-bottom-links a:hover {
    color: var(--vp);
  }

  .separator {
    color: rgba(255,255,255,0.2);
  }

  .footer-credit {
    color: rgba(255,255,255,0.4);
  }

  .footer-heart {
    color: #ff5e5e;
    margin: 0 2px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .footer-bottom {
      flex-direction: column;
      text-align: center;
    }

    .footer-bottom-links {
      justify-content: center;
    }

    .footer-title::after {
      left: 50%;
      transform: translateX(-50%);
    }

    .footer-title {
      text-align: center;
      display: block;
    }

    .footer-contact-info p {
      justify-content: center;
    }

    .social-icons {
      justify-content: center;
    }
  }

  @media (max-width: 576px) {
    .footer-modern {
      padding: 3rem 0 1.5rem;
    }

    .newsletter-form {
      flex-direction: column;
    }

    .newsletter-form button {
      width: 100%;
      border-radius: 50px;
      padding: 10px;
    }
  }
</style>

<script>
  /* Mobile menu */
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileBtn) mobileBtn.addEventListener('click', () => { mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block'; });
  document.querySelectorAll('#mobileMenu a').forEach(a => a.addEventListener('click', () => mobileMenu.style.display = 'none'));

  /* Scroll reveal */
  const allReveals = document.querySelectorAll('.reveal');
  function onScroll() {
    const wh = window.innerHeight;
    allReveals.forEach(el => { if (el.getBoundingClientRect().top < wh - 80) el.classList.add('vis'); });
    document.getElementById('topnav').classList.toggle('scrolled', window.scrollY > 50);
  }
  window.addEventListener('scroll', onScroll, { passive:true });
  setTimeout(onScroll, 200);

  /* Feat nav active state on scroll */
  const sections = ['pointage','mobilite','analytique','secondaires','plans'];
  const navBtns  = document.querySelectorAll('.feat-nav-btn');
  window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(id => {
      const el = document.getElementById(id);
      if (el && el.getBoundingClientRect().top < 160) current = id;
    });
    navBtns.forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('href') === '#' + current);
    });
  }, { passive:true });
</script>
</body>
</html>
