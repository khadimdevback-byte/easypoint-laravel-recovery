<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EasyPoint — Notre identité</title>

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
      --bc: #fafdfc;
      --t: all 0.32s cubic-bezier(.4,0,.2,1);
    }

    html { scroll-behavior: smooth; }
    body { font-family:'Inter',sans-serif; background:#fff; color:#1a2a2e; overflow-x:hidden; }
    h1,h2,h3,h4,h5,h6 { font-family:'Syne',sans-serif; font-weight:700; letter-spacing:-.02em; }

    /* ── NAVBAR ── */
    .navbar-glass {
      position:sticky; top:0; z-index:1100;
      background:rgba(255,255,255,.96);
      backdrop-filter:blur(16px);
      border-bottom:1px solid var(--gl);
      transition:var(--t);
    }
    .navbar-glass.scrolled { box-shadow:0 4px 20px rgba(0,0,0,.08); }
    .logo { font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:var(--gf); text-decoration:none; }
    .logo span { color:var(--vp); }
    .nav-link-custom { font-weight:500; color:var(--gm); text-decoration:none; font-size:.95rem; transition:var(--t); position:relative; padding-bottom:2px; }
    .nav-link-custom::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:2px; background:var(--vp); border-radius:2px; transition:width .3s ease; }
    .nav-link-custom:hover::after, .nav-link-custom.active::after { width:100%; }
    .nav-link-custom:hover, .nav-link-custom.active { color:var(--vp); }
    .btn-prim { background:var(--vp); color:#fff; border:none; border-radius:9999px; padding:.65rem 1.7rem; font-weight:600; font-size:.88rem; display:inline-flex; align-items:center; gap:.45rem; text-decoration:none; transition:var(--t); }
    .btn-prim:hover { background:var(--vf); transform:translateY(-2px); box-shadow:0 8px 24px rgba(46,125,100,.3); color:#fff; }
    .btn-out { background:transparent; border:1.5px solid var(--gl); border-radius:9999px; padding:.65rem 1.7rem; font-weight:600; font-size:.88rem; display:inline-flex; align-items:center; gap:.45rem; text-decoration:none; color:var(--gf); transition:var(--t); }
    .btn-out:hover { border-color:var(--vp); color:var(--vp); transform:translateY(-2px); }

    /* ── HERO MINIMAL FLUIDE ── */
    .hero-minimal {
      padding: 80px 0 72px;
      background: #fff;
      border-bottom: 1px solid var(--gl);
      position: relative;
      overflow: hidden;
    }
    .hero-minimal-bg {
      position: absolute;
      inset: 0;
      background-image:
        radial-gradient(ellipse 60% 60% at 90% 50%, rgba(46,125,100,.05) 0%, transparent 70%);
      pointer-events: none;
    }
    .hero-minimal-inner {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 20px;
    }
    .hero-minimal-tag {
      display: inline-flex; align-items: center; gap: 8px;
      font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      color: var(--vp);
    }
    .hero-minimal-tag::before {
      content: '';
      width: 22px; height: 2px;
      background: var(--vp);
      border-radius: 2px;
    }
    .hero-minimal-h1 {
      font-size: clamp(2.4rem, 5vw, 3.8rem);
      font-weight: 900;
      color: var(--gf);
      line-height: 1.05;
      letter-spacing: -.03em;
    }
    .hero-minimal-h1 em {
      font-style: normal;
      color: var(--vp);
    }
    .hero-minimal-row {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 48px;
      width: 100%;
      flex-wrap: wrap;
    }
    .hero-minimal-sub {
      font-size: .97rem;
      color: var(--gm);
      line-height: 1.75;
      max-width: 500px;
    }
    .hero-minimal-kpis {
      display: flex;
      gap: 40px;
      flex-shrink: 0;
    }
    .hero-mini-kpi {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 2px;
    }
    .hero-mini-kpi-val {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem;
      font-weight: 900;
      color: var(--gf);
      line-height: 1;
    }
    .hero-mini-kpi-val sup {
      font-size: .9rem;
      font-weight: 700;
      color: var(--vp);
      margin-left: 1px;
      vertical-align: super;
    }
    .hero-mini-kpi-lbl {
      font-size: .68rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--gm);
    }
    .hero-mini-kpi-sep {
      width: 1px;
      height: 40px;
      background: var(--gl);
      align-self: center;
    }

    @media(max-width: 768px) {
      .hero-minimal { padding: 56px 0 52px; }
      .hero-minimal-row { flex-direction: column; align-items: flex-start; gap: 28px; }
      .hero-minimal-kpis { gap: 24px; }
      .hero-mini-kpi-val { font-size: 1.4rem; }
    }

    /* ── BREADCRUMB STRIP ── */
    .breadcrumb-strip {
      background:var(--gtc);
      border-bottom:1px solid var(--gl);
      padding:12px 0;
    }
    .bc-item { font-size:.78rem; color:var(--gm); }
    .bc-item.active { color:var(--vp); font-weight:600; }
    .bc-sep { color:var(--gl); margin:0 6px; font-size:.7rem; }

    /* ── SECTION TAGS ── */
    .stag { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:var(--vp); margin-bottom:8px; display:flex; align-items:center; gap:8px; }
    .stag::before { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; display:inline-block; }
    .stitle { font-size:clamp(1.5rem,2.5vw,2.2rem); font-weight:900; color:var(--gf); line-height:1.15; }

    /* ── MISSION / VISION ── */
    .mv-band { padding:80px 0; background:#fff; }
    .mv-split { display:grid; grid-template-columns:120px 1fr 1px 1fr; gap:0 48px; align-items:start; }
    .mv-label-col { writing-mode:vertical-rl; text-orientation:mixed; transform:rotate(180deg); font-family:'Syne',sans-serif; font-weight:900; font-size:4rem; color:var(--vc); line-height:1; user-select:none; align-self:center; }
    .mv-divider { width:1px; background:var(--gl); height:100%; align-self:stretch; }
    .mv-block { padding:0 0 0 8px; }
    .mv-block-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; margin-bottom:20px; }
    .mv-block-icon.miss { background:var(--vc); color:var(--vp); }
    .mv-block-icon.vis  { background:var(--gf); color:#6ecfb5; }
    .mv-block-title { font-size:1.2rem; font-weight:800; color:var(--gf); margin-bottom:12px; }
    .mv-block-text  { font-size:.9rem; color:var(--gm); line-height:1.75; }
    .mv-pill { display:inline-block; padding:4px 14px; border-radius:9999px; font-size:.68rem; font-weight:700; letter-spacing:.06em; margin-top:16px; }
    .mv-pill.miss { background:var(--vc); color:var(--vp); }
    .mv-pill.vis  { background:rgba(46,125,100,.15); color:#6ecfb5; }

    @media(max-width:768px){
      .mv-split { grid-template-columns:1fr; gap:32px; }
      .mv-label-col { writing-mode:horizontal-tb; transform:none; font-size:2rem; }
      .mv-divider { display:none; }
    }

    /* ── VALEURS ── */
    .valeurs-band { background:var(--gtc); padding:80px 0; }
    .valeurs-header { margin-bottom:52px; }
    .valeurs-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
    .valeur-card { background:#fff; border:1px solid var(--gl); border-radius:16px; padding:28px 24px; transition:var(--t); position:relative; overflow:hidden; }
    .valeur-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:var(--vp); transform:scaleY(0); transform-origin:bottom; transition:transform .35s ease; }
    .valeur-card:hover { transform:translateY(-5px); box-shadow:0 12px 32px rgba(0,0,0,.08); }
    .valeur-card:hover::before { transform:scaleY(1); }
    .valeur-num { font-family:'Syne',sans-serif; font-weight:900; font-size:2.4rem; color:var(--vc); line-height:1; margin-bottom:12px; transition:color .3s; }
    .valeur-title { font-size:1rem; font-weight:800; color:var(--gf); margin-bottom:8px; }
    .valeur-text  { font-size:.85rem; color:var(--gm); line-height:1.65; }
    .valeur-card.big { grid-column:span 2; background:var(--gf); border-color:var(--gf); }
    .valeur-card.big .valeur-num  { color:rgba(64,160,136,.3); }
    .valeur-card.big .valeur-title{ color:#fff; }
    .valeur-card.big .valeur-text { color:rgba(255,255,255,.55); }
    .valeur-card.big::before { background:#6ecfb5; }

    @media(max-width:768px){
      .valeurs-grid { grid-template-columns:1fr; }
      .valeur-card.big { grid-column:span 1; }
    }

    /* ── TIMELINE ── */
    .timeline-band { background:#fff; padding:80px 0; }
    .timeline-wrap { display:grid; grid-template-columns:280px 1fr; gap:64px; align-items:start; }
    .timeline-sticky { position:sticky; top:100px; }
    .timeline-sticky p { font-size:.9rem; color:var(--gm); line-height:1.7; margin-top:16px; }
    .timeline-list { position:relative; padding-left:32px; }
    .timeline-list::before { content:''; position:absolute; left:6px; top:8px; bottom:8px; width:2px; background:linear-gradient(to bottom,var(--vp),var(--vc)); }
    .tl-item { position:relative; margin-bottom:32px; opacity:0; transform:translateX(20px); transition:opacity .5s ease,transform .5s ease; }
    .tl-item.vis { opacity:1; transform:translateX(0); }
    .tl-dot { position:absolute; left:-30px; top:6px; width:14px; height:14px; border-radius:50%; background:#fff; border:3px solid var(--vp); box-shadow:0 0 0 3px rgba(46,125,100,.15); }
    .tl-dot.done { background:var(--vp); }
    .tl-card { background:var(--gtc); border:1px solid var(--gl); border-radius:14px; padding:20px 22px; transition:var(--t); }
    .tl-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.07); transform:translateX(4px); }
    .tl-year { display:inline-block; font-size:.68rem; font-weight:800; color:var(--vp); background:var(--vc); padding:3px 10px; border-radius:99px; margin-bottom:8px; }
    .tl-title { font-size:.95rem; font-weight:800; color:var(--gf); margin-bottom:4px; }
    .tl-desc  { font-size:.82rem; color:var(--gm); line-height:1.6; margin:0; }

    @media(max-width:900px){
      .timeline-wrap { grid-template-columns:1fr; gap:40px; }
      .timeline-sticky { position:static; }
    }

    /* ── ÉQUIPE ── */
    .equipe-band { background:var(--gtc); padding:80px 0; }
    .equipe-header-row { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px; gap:20px; flex-wrap:wrap; }
    .equipe-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .eq-card { background:#fff; border-radius:18px; overflow:hidden; border:1px solid var(--gl); transition:var(--t); }
    .eq-card:hover { transform:translateY(-6px); box-shadow:0 16px 40px rgba(0,0,0,.1); }
    .eq-card-top { height:100px; position:relative; overflow:hidden; }
    .eq-card-top-stripe { position:absolute; inset:0; background-image:linear-gradient(rgba(46,125,100,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(46,125,100,.1) 1px,transparent 1px); background-size:20px 20px; }
    .eq-avatar { position:absolute; bottom:-28px; left:24px; width:60px; height:60px; border-radius:50%; border:3px solid #fff; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-weight:900; font-size:1.1rem; color:#fff; }
    .eq-card-body { padding:40px 24px 24px; }
    .eq-name   { font-size:1rem; font-weight:800; color:var(--gf); margin-bottom:2px; }
    .eq-role   { font-size:.78rem; color:var(--vp); font-weight:600; margin-bottom:12px; }
    .eq-divider{ height:1px; background:var(--gl); margin-bottom:12px; }
    .eq-socials a { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; background:var(--gtc); color:var(--gm); font-size:.75rem; text-decoration:none; transition:var(--t); margin-right:6px; }
    .eq-socials a:hover { background:var(--vp); color:#fff; }

    /* ── CTA BAND ── */
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

    @media(max-width:768px){
      .cta-inner { grid-template-columns:1fr; }
      .cta-left  { padding:40px 24px; }
      .cta-right { padding:36px 24px; }
      .equipe-grid { grid-template-columns:1fr; }
      .equipe-header-row { flex-direction:column; align-items:flex-start; }
    }
    @media(max-width:992px) and (min-width:769px){
      .equipe-grid { grid-template-columns:repeat(2,1fr); }
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
    .reveal { opacity:0; transform:translateY(28px); transition:opacity .65s ease,transform .65s ease; }
    .reveal.vis { opacity:1; transform:none; }
  </style>
</head>
<body>

<!-- ═══════════════════════ NAVBAR ═══════════════════════ -->
<nav class="navbar-glass py-3" id="topnav">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a  href="{{ route('siteVitrine.acceuil') }}" class="block px-8 py-2 m-0 whitespace-nowrap text-slate-700" href="">
            <img src="{{ asset('images/logo.png') }}"
                class="inline transition-all duration-200 ease-nav-brand"
                alt="main_logo"
                style="height: 40px; width: auto;" />
        </a>
      <div class="d-none d-md-flex gap-4">
        <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom">Accueil</a>
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom active">À propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom">Fonctionnalités</a>
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
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom py-2 active">À propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom py-2">Fonctionnalités</a>
        {{-- <a href="{{route('siteVitrine.demonstration')}}" class="nav-link-custom py-2">Démonstration</a> --}}
        <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom py-2">Tarifs</a>
        <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom py-2">Contact</a>
        <hr>
        <a href="{{route('login')}}" class="btn-prim text-center justify-content-center">Connexion</a>
      </div>
    </div>
  </div>
</nav>

<!-- ═══════════════════════ BREADCRUMB ═══════════════════════ -->
<div class="breadcrumb-strip">
  <div class="container">
    <span class="bc-item">Accueil</span>
    <span class="bc-sep"><i class="fas fa-chevron-right" style="font-size:9px;"></i></span>
    <span class="bc-item active">Notre ADN</span>
  </div>
</div>

<!-- ═══════════════════════ HERO MINIMAL FLUIDE ═══════════════════════ -->
<section class="hero-minimal">
  <div class="hero-minimal-bg"></div>
  <div class="container">
    <div class="hero-minimal-inner">
      <span class="hero-minimal-tag">Essence & héritage</span>
      <h1 class="hero-minimal-h1">L'âme<br>d'<em>EasyPoint</em></h1>
      <div class="hero-minimal-row">
        <p class="hero-minimal-sub">Un collectif d'ingénieurs et de stratèges bâtissant des ponts entre vision humaine et performance technologique — fondé sur des convictions, porté par l'impact.</p>
        <div class="hero-minimal-kpis">
          <div class="hero-mini-kpi">
            <span class="hero-mini-kpi-val">100<sup>%</sup></span>
            <span class="hero-mini-kpi-lbl">Engagement terrain</span>
          </div>
          <div class="hero-mini-kpi-sep"></div>
          <div class="hero-mini-kpi">
            <span class="hero-mini-kpi-val">24<sup>/7</sup></span>
            <span class="hero-mini-kpi-lbl">Support humain</span>
          </div>
          <div class="hero-mini-kpi-sep"></div>
          <div class="hero-mini-kpi">
            <span class="hero-mini-kpi-val">99,9<sup>%</sup></span>
            <span class="hero-mini-kpi-lbl">Fiabilité opérationnelle</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ MISSION / VISION ═══════════════════════ -->
<section class="mv-band">
  <div class="container">
    <div class="mv-split reveal">
      <div class="mv-label-col" aria-hidden="true">MV</div>
      <div class="mv-block">
        <div class="stag">Mission</div>
        <div class="mv-block-icon miss"><i class="fas fa-bullseye"></i></div>
        <h3 class="mv-block-title">Révéler le potentiel des équipes</h3>
        <p class="mv-block-text">Offrir des outils de gestion du temps et des présences qui libèrent les managers des tâches répétitives, pour qu'ils se consacrent à l'essentiel : l'humain et la stratégie.</p>
        <span class="mv-pill miss">Notre raison d'être</span>
      </div>
      <div class="mv-divider"></div>
      <div class="mv-block">
        <div class="stag">Vision</div>
        <div class="mv-block-icon vis"><i class="fas fa-eye"></i></div>
        <h3 class="mv-block-title">L'excellence opérationnelle accessible à tous</h3>
        <p class="mv-block-text">Construire un écosystème où chaque organisation, quelle que soit sa taille, peut piloter son capital humain avec fluidité, éthique et intelligence.</p>
        <span class="mv-pill vis">Notre cap 2030</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ TIMELINE ═══════════════════════ -->
<section class="timeline-band">
  <div class="container">
    <div class="timeline-wrap">
      <div class="timeline-sticky reveal">
        <div class="stag">Notre évolution</div>
        <h2 class="stitle">Les jalons<br>d'une conviction</h2>
        <p>Chaque étape a façonné notre identité : de l'idée fondatrice à la plateforme nouvelle génération, guidée par une soif d'innovation discrète mais radicale.</p>
        <div style="margin-top:28px; display:flex; flex-direction:column; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px;"><div style="width:12px;height:12px;border-radius:50%;background:var(--vp);flex-shrink:0;"></div><span style="font-size:.8rem;color:var(--gm);">Chapitre accompli</span></div>
          <div style="display:flex; align-items:center; gap:10px;"><div style="width:12px;height:12px;border-radius:50%;background:#fff;border:2.5px solid var(--vp);flex-shrink:0;"></div><span style="font-size:.8rem;color:var(--gm);">Tournant actuel</span></div>
        </div>
      </div>
      <div class="timeline-list">
        <div class="tl-item"><div class="tl-dot done"></div><div class="tl-card"><span class="tl-year">2014</span><h4 class="tl-title">Naissance d'une idée</h4><p class="tl-desc">Première ébauche d'une solution pensée pour fluidifier la gestion des présences, avec une approche centrée sur les utilisateurs.</p></div></div>
        <div class="tl-item"><div class="tl-dot done"></div><div class="tl-card"><span class="tl-year">2016</span><h4 class="tl-title">Première maturité produit</h4><p class="tl-desc">Lancement d'une version web robuste, intégrant les premiers retours terrain pour affiner l'expérience.</p></div></div>
        <div class="tl-item"><div class="tl-dot done"></div><div class="tl-card"><span class="tl-year">2018</span><h4 class="tl-title">Mobilité et liberté</h4><p class="tl-desc">Déploiement des applications natives pour permettre un suivi agile et décentralisé.</p></div></div>
        <div class="tl-item"><div class="tl-dot done"></div><div class="tl-card"><span class="tl-year">2020</span><h4 class="tl-title">Fiabilité & résilience</h4><p class="tl-desc">Migration vers une architecture cloud hautement sécurisée, garantissant une disponibilité constante.</p></div></div>
        <div class="tl-item"><div class="tl-dot done"></div><div class="tl-card"><span class="tl-year">2022</span><h4 class="tl-title">IA & anticipation</h4><p class="tl-desc">Intégration de briques d'intelligence artificielle pour détecter les anomalies et enrichir les décisions RH.</p></div></div>
        <div class="tl-item"><div class="tl-dot"></div><div class="tl-card" style="border-color:var(--vp);background:var(--vc);"><span class="tl-year">2025</span><h4 class="tl-title">EasyPoint 360°</h4><p class="tl-desc">Écosystème complet : analytique prédictive, API ouverte, et une expérience utilisateur repensée.</p></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ VALEURS ═══════════════════════ -->
<section class="valeurs-band">
  <div class="container">
    <div class="valeurs-header text-center reveal"><div class="stag justify-content-center">Notre boussole</div><h2 class="stitle">Valeurs cardinales</h2></div>
    <div class="valeurs-grid reveal">
      <div class="valeur-card"><div class="valeur-num">01</div><div class="valeur-title">Écoute active</div><div class="valeur-text">Chaque fonctionnalité naît des besoins réels. Nous restons à l'écoute permanente de nos communautés pour innover avec justesse.</div></div>
      <div class="valeur-card"><div class="valeur-num">02</div><div class="valeur-title">Intégrité radicale</div><div class="valeur-text">Transparence, éthique et respect des données : des piliers non négociables dans nos choix techniques et humains.</div></div>
      <div class="valeur-card"><div class="valeur-num">03</div><div class="valeur-title">Audace tranquille</div><div class="valeur-text">Nous osons repenser les standards sans bruit, avec des solutions robustes qui changent durablement les habitudes.</div></div>
      <div class="valeur-card big"><div class="valeur-num">04</div><div class="valeur-title">Symbiose collective</div><div class="valeur-text">Nous croyons que la meilleure technologie naît de la diversité des regards. Notre équipe pluridisciplinaire et nos partenariats enrichissent chaque projet.</div></div>
      <div class="valeur-card"><div class="valeur-num">05</div><div class="valeur-title">Simplicité profonde</div><div class="valeur-text">La complexité reste en coulisses. Nos interfaces respirent l'élégance et l'intuitivité pour libérer l'utilisateur.</div></div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ ÉQUIPE ═══════════════════════ -->
<section class="equipe-band">
  <div class="container">
    <div class="equipe-header-row reveal"><div><div class="stag">Les bâtisseurs</div><h2 class="stitle">L'équipe d'âme</h2></div><a href="#" class="btn-out"><i class="fas fa-users"></i> Rencontrez-nous</a></div>
    <div class="equipe-grid">
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#2e7d64;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#1a5a48;">JK</div></div><div class="eq-card-body"><div class="eq-name">Jean Koffi</div><div class="eq-role">Fondateur & vision</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-linkedin-in"></i></a><a href="#"><i class="fab fa-twitter"></i></a></div></div></div>
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#1a5a48;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#40a088;">AD</div></div><div class="eq-card-body"><div class="eq-name">Aïcha Diop</div><div class="eq-role">Directrice innovation</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-linkedin-in"></i></a><a href="#"><i class="fab fa-github"></i></a></div></div></div>
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#40a088;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#2e7d64;">MJ</div></div><div class="eq-card-body"><div class="eq-name">Malik Johnson</div><div class="eq-role">Expérience & adoption</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-linkedin-in"></i></a><a href="#"><i class="fab fa-twitter"></i></a></div></div></div>
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#2d3a3e;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#1a5a48;">FS</div></div><div class="eq-card-body"><div class="eq-name">Fatou Sall</div><div class="eq-role">Stratégie & impact</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-linkedin-in"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div></div></div>
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#1a5a48;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#2e7d64;">KM</div></div><div class="eq-card-body"><div class="eq-name">Khalil Mbaye</div><div class="eq-role">Architecte logiciel</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-github"></i></a><a href="#"><i class="fab fa-linkedin-in"></i></a></div></div></div>
      <div class="eq-card reveal"><div class="eq-card-top" style="background:#40a088;"><div class="eq-card-top-stripe"></div><div class="eq-avatar" style="background:#1a5a48;">CB</div></div><div class="eq-card-body"><div class="eq-name">Claire B.</div><div class="eq-role">Relation & confiance</div><div class="eq-divider"></div><div class="eq-socials"><a href="#"><i class="fab fa-linkedin-in"></i></a><a href="#"><i class="fab fa-twitter"></i></a></div></div></div>
    </div>
  </div>
</section>



<!-- ═══════════════════════ FOOTER ═══════════════════════ -->
<footer class="footer-modern">
  <div class="container">
    <div class="row g-4">
      <!-- Colonne Logo & Présentation -->
      <div class="col-lg-4 col-md-6">
        <a href="{{ route('siteVitrine.acceuil') }}" class="footer-logo">Easy<span>Point</span></a>
        <p class="footer-description mt-3">La solution de suivi de présence intelligente pour les structures exigeantes. Performance, simplicité et rigueur.</p>
        <div class="social-icons d-flex gap-3 mt-4">
          <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.linkedin.com/"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://x.com/@"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <!-- Colonne Produit -->
        <div class="col-lg-2 col-md-6">
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
          @endif
          @if($footer->phone)
            <p><i class="fas fa-phone-alt"></i> {{ $footer->phone }}</p>
          @endif
          @if($footer->address)
            <p><i class="fas fa-map-marker-alt"></i> {{ $footer->address }}</p>
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
    {{-- <div class="footer-divider"></div> --}}

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
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileBtn) mobileBtn.addEventListener('click', () => { mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block'; });
  document.querySelectorAll('#mobileMenu a').forEach(a => a.addEventListener('click', () => mobileMenu.style.display = 'none'));

  const allReveals = document.querySelectorAll('.reveal');
  const allTlItems = document.querySelectorAll('.tl-item');

  function onScroll() {
    const wh = window.innerHeight;
    allReveals.forEach(el => { if (el.getBoundingClientRect().top < wh - 80) el.classList.add('vis'); });
    allTlItems.forEach((el, i) => { if (el.getBoundingClientRect().top < wh - 60) setTimeout(() => el.classList.add('vis'), i * 70); });
    document.getElementById('topnav').classList.toggle('scrolled', window.scrollY > 50);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  setTimeout(onScroll, 200);
</script>
</body>
</html>
