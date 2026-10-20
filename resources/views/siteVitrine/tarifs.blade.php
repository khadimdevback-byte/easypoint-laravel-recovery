<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EasyPoint — Tarifs</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
  .plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
  }

  /* Style des cartes */
  .plan-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--gl, #e5e7eb);
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }

  .plan-card.featured {
    border: 2px solid var(--vp, #2e7d64);
    transform: scale(1.02);
  }

  .plan-card.featured:hover {
    transform: scale(1.02) translateY(-5px);
  }

  .plan-popular-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #f59e0b;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    z-index: 10;
  }

  .plan-header {
    padding: 24px 20px 20px;
    text-align: center;
    border-bottom: 1px solid var(--gl, #e5e7eb);
  }

  .plan-name {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--gf, #1f2937);
    margin-bottom: 12px;
    font-family: 'Syne', sans-serif;
  }

  .plan-price-wrap {
    margin-bottom: 10px;
  }

  .plan-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--vp, #2e7d64);
    font-family: 'Syne', sans-serif;
  }

  .plan-price-cur {
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 2px;
  }

  .plan-price-per {
    font-size: 0.7rem;
    font-weight: 400;
    color: var(--gm, #6b7280);
  }

  .plan-users {
    font-size: 0.75rem;
    color: var(--gm, #6b7280);
  }

  .plan-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .plan-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 20px;
    background: var(--vp, #2e7d64);
    color: white;
    border: none;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }

  .plan-cta:hover {
    background: var(--vf, #1a5a48);
    transform: translateY(-2px);
  }

  .plan-info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 0.8rem;
  }

  .plan-info-label {
    color: var(--gm, #6b7280);
  }

  .plan-info-value {
    font-weight: 600;
    color: var(--gf, #1f2937);
  }

  .plan-divider {
    height: 1px;
    background: var(--gl, #e5e7eb);
    margin: 15px 0 12px;
  }

  .plan-feature-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--gm, #6b7280);
    margin-bottom: 12px;
  }

  .plan-features {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .plan-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    font-size: 0.75rem;
  }

  .plan-feature.yes {
    color: var(--gf, #1f2937);
  }

  .plan-feature.yes i {
    color: #10b981;
  }

  .plan-feature.no {
    color: var(--gm, #9ca3af);
  }

  .plan-feature.no i {
    color: #ef4444;
  }

  /* ============================================ */
  /* BOUTON PLAN SUR MESURE CENTRÉ               */
  /* ============================================ */

  .custom-plan-cta {
    margin-top: 60px;
    display: flex;
    justify-content: center;
  }

  .custom-plan-card {
    max-width: 700px;
    width: 100%;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 32px;
    padding: 40px;
    text-align: center;
    border: 1px solid var(--gl, #e5e7eb);
    transition: all 0.3s ease;
  }

  .custom-plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border-color: var(--vp, #2e7d64);
  }

  .custom-plan-icon {
    width: 70px;
    height: 70px;
    background: var(--vp, #2e7d64);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
  }

  .custom-plan-icon i {
    font-size: 2rem;
    color: white;
  }

  .custom-plan-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--gf, #1f2937);
    margin-bottom: 12px;
    font-family: 'Syne', sans-serif;
  }

  .custom-plan-desc {
    font-size: 0.9rem;
    color: var(--gm, #6b7280);
    margin-bottom: 25px;
    line-height: 1.6;
  }

  .custom-plan-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: var(--vp, #2e7d64);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 14px 32px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 25px;
  }

  .custom-plan-btn:hover {
    background: var(--vf, #1a5a48);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(46,125,100,0.3);
  }

  .custom-plan-features {
    display: flex;
    justify-content: center;
    gap: 24px;
    flex-wrap: wrap;
  }

  .custom-plan-features span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.75rem;
    color: var(--gm, #6b7280);
  }

  .custom-plan-features span i {
    color: var(--vp, #2e7d64);
    font-size: 0.8rem;
  }

  @media (max-width: 768px) {
    .plans-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }

    .custom-plan-card {
      padding: 25px 20px;
    }

    .custom-plan-title {
      font-size: 1.2rem;
    }

    .custom-plan-features {
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }
  }
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
      --t: all 0.32s cubic-bezier(.4,0,.2,1);
    }

    html { scroll-behavior: smooth; }
    body { font-family:'Inter',sans-serif; background:#fff; color:#1a2a2e; overflow-x:hidden; }
    h1,h2,h3,h4,h5,h6 { font-family:'Syne',sans-serif; font-weight:700; letter-spacing:-.02em; }

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

    .breadcrumb-strip { background:var(--gtc); border-bottom:1px solid var(--gl); padding:12px 0; }
    .bc-item { font-size:.78rem; color:var(--gm); }
    .bc-item.active { color:var(--vp); font-weight:600; }
    .bc-sep { color:var(--gl); margin:0 6px; font-size:.7rem; }

    .stag { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:var(--vp); margin-bottom:8px; display:flex; align-items:center; gap:8px; }
    .stag::before { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; display:inline-block; }
    .stag.center { justify-content:center; }
    .stag.center::before { display:none; }
    .stag.center::after { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; display:inline-block; }
    .stitle { font-size:clamp(1.6rem,3vw,2.4rem); font-weight:900; color:var(--gf); line-height:1.1; }

    .hero-tarifs {
      padding: 80px 0 64px;
      background: #fff;
      border-bottom: 1px solid var(--gl);
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .hero-tarifs-grid {
      position: absolute; inset: 0;
      background-image:
        linear-gradient(var(--gl) 1px, transparent 1px),
        linear-gradient(90deg, var(--gl) 1px, transparent 1px);
      background-size: 48px 48px;
      opacity: .3;
      pointer-events: none;
    }
    .hero-tarifs-fade {
      position: absolute; inset: 0;
      background: radial-gradient(ellipse 80% 90% at 50% 50%, #fff 35%, transparent 100%);
      pointer-events: none;
    }
    .hero-tarifs-content { position: relative; z-index: 1; }
    .hero-tarifs-pill {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--vc); border: 1px solid rgba(46,125,100,.2);
      color: var(--vp); font-size: .7rem; font-weight: 700;
      letter-spacing: .12em; text-transform: uppercase;
      padding: .4rem 1.1rem; border-radius: 9999px; margin-bottom: 24px;
    }
    .hero-tarifs-h1 {
      font-size: clamp(2rem, 5vw, 3.6rem);
      font-weight: 900; color: var(--gf);
      line-height: 1.06; letter-spacing: -.03em;
      margin-bottom: 18px;
    }
    .hero-tarifs-h1 em { font-style: normal; color: var(--vp); }
    .hero-tarifs-sub {
      font-size: .97rem; color: var(--gm);
      line-height: 1.75; max-width: 520px;
      margin: 0 auto 36px;
    }

    .billing-toggle {
      display: inline-flex; align-items: center; gap: 12px;
      background: var(--gtc); border: 1px solid var(--gl);
      border-radius: 9999px; padding: 6px;
      margin-bottom: 56px;
    }
    .billing-btn {
      padding: 8px 20px; border-radius: 9999px;
      font-size: .8rem; font-weight: 600;
      cursor: pointer; border: none; transition: var(--t);
      background: transparent; color: var(--gm);
    }
    .billing-btn.active { background: #fff; color: var(--gf); box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .billing-badge {
      font-size: .62rem; font-weight: 800;
      background: var(--vp); color: #fff;
      padding: 2px 8px; border-radius: 99px;
      letter-spacing: .04em; text-transform: uppercase;
    }

    .plans-section { padding: 0 0 96px; background: #fff; }
    .plans-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 24px;
      align-items: start;
    }
    .plan-card {
      border-radius: 20px;
      border: 1.5px solid var(--gl);
      background: #fff;
      overflow: hidden;
      transition: var(--t);
      position: relative;
    }
    .plan-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 48px rgba(0,0,0,.09);
    }
    .plan-card.featured {
      border-color: var(--vp);
      background: var(--gf);
      transform: translateY(-10px);
      box-shadow: 0 24px 56px rgba(46,125,100,.22);
    }
    .plan-card.featured:hover { transform: translateY(-16px); }
    .plan-popular-badge {
      position: absolute; top: -1px; left: 50%; transform: translateX(-50%);
      background: var(--vp); color: #fff;
      font-size: .6rem; font-weight: 800;
      letter-spacing: .1em; text-transform: uppercase;
      padding: 4px 16px;
      border-radius: 0 0 10px 10px;
      white-space: nowrap;
    }
    .plan-header {
      padding: 28px 24px 20px;
      border-bottom: 1px solid var(--gl);
    }
    .plan-card.featured .plan-header { border-bottom-color: rgba(255,255,255,.1); }
    .plan-name {
      font-size: .7rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .12em;
      color: var(--vp); margin-bottom: 10px;
    }
    .plan-card.featured .plan-name { color: #6ecfb5; }
    .plan-price-wrap { display: flex; align-items: baseline; gap: 4px; margin-bottom: 4px; }
    .plan-price {
      font-family: 'Syne', sans-serif;
      font-size: 2rem; font-weight: 900;
      color: var(--gf); line-height: 1;
    }
    .plan-card.featured .plan-price { color: #fff; }
    .plan-price-cur {
      font-size: .75rem; font-weight: 700;
      color: var(--gm); align-self: flex-start; padding-top: 4px;
    }
    .plan-card.featured .plan-price-cur { color: rgba(255,255,255,.5); }
    .plan-price-per {
      font-size: .7rem; color: var(--gm);
    }
    .plan-card.featured .plan-price-per { color: rgba(255,255,255,.45); }
    .plan-users {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: .72rem; font-weight: 600;
      color: var(--gm); margin-top: 8px;
    }
    .plan-card.featured .plan-users { color: rgba(255,255,255,.55); }
    .plan-users i { color: var(--vp); font-size: .65rem; }
    .plan-card.featured .plan-users i { color: #6ecfb5; }

    .plan-body { padding: 20px 24px 24px; }
    .plan-cta {
      display: flex; align-items: center; justify-content: center;
      width: 100%; padding: 11px 0;
      border-radius: 9999px; font-size: .82rem; font-weight: 700;
      text-decoration: none; transition: var(--t); margin-bottom: 20px;
    }
    .plan-card:not(.featured) .plan-cta {
      border: 1.5px solid var(--gl); color: var(--gf); background: transparent;
    }
    .plan-card:not(.featured) .plan-cta:hover { border-color: var(--vp); color: var(--vp); background: rgba(46,125,100,.04); }
    .plan-card.featured .plan-cta {
      background: var(--vp); color: #fff; border: 1.5px solid var(--vp);
    }
    .plan-card.featured .plan-cta:hover { background: var(--vf); border-color: var(--vf); }

    .plan-info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      font-size: .78rem;
      border-bottom: 1px dashed var(--gl);
    }
    .plan-info-label {
      color: var(--gm);
      font-weight: 500;
    }
    .plan-info-value {
      color: var(--gf);
      font-weight: 600;
    }
    .plan-card.featured .plan-info-value {
      color: rgba(255,255,255,.8);
    }
    .plan-card.featured .plan-info-label {
      color: rgba(255,255,255,.5);
    }

    .plan-features { list-style: none; padding: 0; margin: 12px 0 0 0; display: flex; flex-direction: column; gap: 9px; }
    .plan-feature {
      display: flex; align-items: flex-start; gap: 9px;
      font-size: .75rem; color: var(--gm); line-height: 1.4;
    }
    .plan-card.featured .plan-feature { color: rgba(255,255,255,.55); }
    .plan-feature i {
      font-size: .7rem; flex-shrink: 0; margin-top: 2px;
    }
    .plan-feature.yes i { color: var(--vp); }
    .plan-feature.no  i { color: var(--gl); }
    .plan-card.featured .plan-feature.yes i { color: #6ecfb5; }
    .plan-card.featured .plan-feature.no  i { color: rgba(255,255,255,.2); }
    .plan-divider {
      height: 1px; background: var(--gl);
      margin: 14px 0;
    }
    .plan-card.featured .plan-divider { background: rgba(255,255,255,.1); }
    .plan-feature-label {
      font-size: .62rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .1em;
      color: var(--gm); margin-bottom: 10px;
    }
    .plan-card.featured .plan-feature-label { color: rgba(255,255,255,.3); }

    @media(max-width: 768px) {
      .plans-grid { gap: 16px; }
    }

    .compare-section { padding: 80px 0 96px; background: var(--gtc); }
    .compare-header { text-align: center; margin-bottom: 52px; }
    .compare-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .compare-table {
      width: 100%; border-collapse: separate; border-spacing: 0;
      min-width: 680px;
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.06);
    }
    .compare-table thead th {
      padding: 18px 20px;
      font-size: .78rem; font-weight: 800;
      text-align: center; color: var(--gf);
      background: #fff;
      border-bottom: 2px solid var(--gl);
    }
    .compare-table thead th:first-child {
      text-align: left; color: var(--gm);
      font-weight: 600; width: 28%;
    }
    .compare-table thead th.col-feat {
      background: var(--gf); color: #fff;
      border-bottom-color: var(--vp);
    }
    .cth-name { font-family:'Syne',sans-serif; font-size: .9rem; display: block; margin-bottom: 2px; }
    .cth-price { font-size: .65rem; color: var(--gm); font-weight: 500; }
    .compare-table thead th.col-feat .cth-price { color: rgba(255,255,255,.4); }
    .compare-table tbody td {
      padding: 12px 20px;
      font-size: .8rem; color: var(--gf);
      border-bottom: 1px solid var(--gl);
      text-align: center; vertical-align: middle;
    }
    .compare-table tbody td:first-child { text-align: left; color: var(--gm); font-size: .78rem; }
    .compare-table tbody tr:last-child td { border-bottom: none; }
    .compare-table tbody tr:hover td { background: var(--gtc); }

    .faq-section { padding: 80px 0; background: #fff; }
    .faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .faq-item { border: 1.5px solid var(--gl); border-radius: 14px; overflow: hidden; }
    .faq-q {
      display: flex; justify-content: space-between; align-items: center;
      padding: 18px 22px; cursor: pointer;
      font-size: .88rem; font-weight: 700; color: var(--gf);
      transition: var(--t);
    }
    .faq-q i { font-size: .7rem; color: var(--vp); transition: transform .3s ease; }
    .faq-item.open .faq-q i { transform: rotate(180deg); }
    .faq-a {
      max-height: 0; overflow: hidden;
      transition: max-height .4s ease;
      padding: 0 22px;
      font-size: .83rem; color: var(--gm);
    }
    .faq-item.open .faq-a { max-height: 200px; padding: 0 22px 18px; }
    @media(max-width: 768px) { .faq-grid { grid-template-columns: 1fr; } }

    .guarantees-band { background: var(--gtc); border-top: 1px solid var(--gl); border-bottom: 1px solid var(--gl); padding: 48px 0; }
    .guarantees-grid { display: flex; justify-content: center; flex-wrap: wrap; gap: 40px; }
    .guarantee-item { display: flex; align-items: center; gap: 12px; font-size: .85rem; color: var(--gf); font-weight: 600; }
    .guarantee-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--vc); color: var(--vp); display: flex; align-items: center; justify-content: center; }

    .cta-band { background:var(--gf); padding:0; overflow:hidden; }
    .cta-inner { display:grid; grid-template-columns:1fr 420px; min-height:200px; }
    .cta-left { padding:56px 64px; position:relative; overflow:hidden; }
    .cta-left-bg { position:absolute; inset:0; background-image:linear-gradient(rgba(46,125,100,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(46,125,100,.06) 1px,transparent 1px); background-size:40px 40px; }
    .cta-title { font-size:clamp(1.4rem,2.2vw,1.9rem); font-weight:900; color:#fff; margin-bottom:6px; }
    .cta-sub { font-size:.9rem; color:rgba(255,255,255,.5); margin-bottom:24px; }
    .cta-right { background:var(--vp); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; text-align:center; }
    @media(max-width:992px) { .cta-inner { grid-template-columns:1fr; } }

    .footer-modern { background:var(--gf); color:var(--gl); padding:4rem 0 2rem; }
    .footer-logo { font-family:'Syne',sans-serif; font-size:1.6rem; font-weight:800; color:#fff; text-decoration:none; }
    .footer-logo span { color:var(--vp); }
    .reveal { opacity:0; transform:translateY(28px); transition:opacity .65s ease, transform .65s ease; }
    .reveal.vis { opacity:1; transform:none; }
  </style>
</head>
<body>

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
                <a href="{{ route('siteVitrine.acceuil') }}" class="nav-link-custom">Accueil</a>
                <a href="{{ route('siteVitrine.apropos') }}" class="nav-link-custom">À propos</a>
                <a href="{{ route('siteVitrine.fonctionnalites') }}" class="nav-link-custom">Fonctionnalités</a>
                {{-- <a href="{{ route('siteVitrine.demonstration') }}" class="nav-link-custom">Démonstration</a> --}}
                <a href="{{ route('siteVitrine.tarifs') }}" class="nav-link-custom active">Tarifs</a>
                <a href="{{ route('siteVitrine.contact') }}" class="nav-link-custom">Contact</a>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('login') }}" class="btn-prim"><i class="fas fa-lock"></i> Connexion</a>
                <button class="d-md-none btn btn-link text-dark p-0" id="mobileMenuBtn"><i class="fas fa-bars fs-4"></i></button>
            </div>
        </div>
        <div id="mobileMenu" class="d-md-none mt-3" style="display:none;">
        <div class="d-flex flex-column gap-2 py-2">
            <a href="{{ route('siteVitrine.acceuil') }}" class="nav-link-custom py-2">Accueil</a>
            <a href="{{ route('siteVitrine.apropos') }}" class="nav-link-custom py-2">À propos</a>
            <a href="{{ route('siteVitrine.fonctionnalites') }}" class="nav-link-custom py-2">Fonctionnalités</a>
            {{-- <a href="{{ route('siteVitrine.demonstration') }}" class="nav-link-custom py-2">Démonstration</a> --}}
            <a href="{{ route('siteVitrine.tarifs') }}" class="nav-link-custom py-2 active">Tarifs</a>
            <a href="{{ route('siteVitrine.contact') }}" class="nav-link-custom py-2">Contact</a>
            <hr>
            <a href="{{ route('login') }}" class="btn-prim text-center justify-content-center">Connexion</a>
        </div>
        </div>
    </div>
</nav>

<div class="breadcrumb-strip">
  <div class="container">
    <span class="bc-item">Accueil</span>
    <span class="bc-sep"><i class="fas fa-chevron-right" style="font-size:9px;"></i></span>
    <span class="bc-item active">Tarifs</span>
  </div>
</div>

<section class="hero-tarifs">
    <div class="hero-tarifs-grid"></div>
    <div class="hero-tarifs-fade"></div>
    <div class="container hero-tarifs-content">
    <div class="hero-tarifs-pill"><i class="fas fa-tags"></i> Tarification transparente</div>
    <h1 class="hero-tarifs-h1">Un plan pour<br>chaque <em>organisation</em></h1>
    <p class="hero-tarifs-sub">De la startup de 3 personnes à la multinationale de 100+, EasyPoint s'adapte à votre taille et à vos ambitions.</p>
    <div class="billing-toggle" id="billingToggle">
      <button class="billing-btn active" data-billing="monthly">Mensuel</button>
      <button class="billing-btn" data-billing="yearly">Annuel <span class="billing-badge">−20%</span></button>
    </div>
  </div>
</section>
<br><br>
<section class="plans-section">
  <div class="container">
    <div class="plans-grid reveal" id="plansGrid">
      @foreach($plans as $plan)
      <div class="plan-card {{ $plan->is_popular ?? false ? 'featured' : '' }}">
        @if($plan->is_popular ?? false)
        <div class="plan-popular-badge">Populaire</div>
        @endif
        <div class="plan-header">
          <div class="plan-name">{{ $plan->name }}</div>
          <div class="plan-price-wrap">
            <span class="plan-price" data-id="{{ $plan->id }}" data-monthly="{{ $plan->price }}" data-yearly="{{ round($plan->price * 0.8) }}">
              {{ $plan->price == 0 ? 'Gratuit' : number_format($plan->price, 0, ',', ' ') }}
            </span>
            @if($plan->price > 0)
            <span class="plan-price-cur">{{ $plan->currency ?? 'XOF' }}</span>
            <span class="plan-price-per">/mois</span>
            @endif
          </div>
          <div class="plan-users">
            <i class="fas fa-users"></i> {{ $plan->max_employees === -1 ? 'Illimité' : $plan->max_employees . ' employés' }}
          </div>
        </div>
        <div class="plan-body">
            <a href="{{ route('siteVitrine.demonstration', ['plan' => $plan->id, 'billing' => 'monthly']) }}"
                class="btn-prim plan-cta"
                data-plan-id="{{ $plan->id }}">
                <i class="fas fa-rocket"></i>
                @if($plan->price == 0)
                Choisir ce plan
                @elseif($plan->name == 'Enterprise' || $plan->name == 'Entreprise')
                Nous contacter
                @else
                Choisir ce plan
                @endif
            </a>

            <div class="plan-info-row">
                <span class="plan-info-label">Dashboard</span>
                <span class="plan-info-value">{{ ucfirst($plan->dashboard_type ?? 'Standard') }}</span>
            </div>
            {{-- <div class="plan-info-row">
                <span class="plan-info-label">Historique</span>
                <span class="plan-info-value">{{ $plan->history_duration ?? 'Standard' }}</span>
            </div> --}}
            <div class="plan-info-row">
                <span class="plan-info-label">Support</span>
                <span class="plan-info-value">{{ $plan->support_type ?? 'Email' }}</span>
            </div>

            <div class="plan-divider"></div>
            <div class="plan-feature-label">Fonctionnalités clés</div>
            <ul class="plan-features">
            <li class="plan-feature yes">
              <i class="fas fa-check-circle"></i> Pointage QR code
            </li>
            <li class="plan-feature {{ $plan->max_employees >= 10 || $plan->max_employees === -1 ? 'yes' : 'no' }}">
              <i class="fas {{ $plan->max_employees >= 10 || $plan->max_employees === -1 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
              Badge + scan
            </li>
            <li class="plan-feature {{ ($plan->dashboard_type == 'avancé' || $plan->dashboard_type == 'complet') ? 'yes' : 'no' }}">
              <i class="fas {{ ($plan->dashboard_type == 'avancé' || $plan->dashboard_type == 'complet') ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
              Dashboard avancé
            </li>
            <li class="plan-feature {{ $plan->support_type == 'Dédié' ? 'yes' : 'no' }}">
              <i class="fas {{ $plan->support_type == 'Dédié' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
              Support dédié
            </li>
          </ul>
        </div>
      </div>
      @endforeach
    </div>
    {{-- BOUTON PLAN SUR MESURE CENTRÉ --}}
    <div class="custom-plan-cta reveal">
        <div class="custom-plan-card">
        <div class="custom-plan-icon">
          <i class="fas fa-magic"></i>
        </div>
        <h3 class="custom-plan-title">Vous avez des besoins spécifiques ?</h3>
        <p class="custom-plan-desc">
          Nos experts peuvent créer un plan sur mesure adapté parfaitement à votre entreprise.
        </p>
        <a href="{{ route('siteVitrine.contact') }}" class="custom-plan-btn">
          <i class="fas fa-headset"></i>
          Contactez-nous pour un devis personnalisé
        </a>
        <div class="custom-plan-features">
          <span><i class="fas fa-check-circle"></i> Installation dédiée</span>
          <span><i class="fas fa-check-circle"></i> Support prioritaire</span>
          <span><i class="fas fa-check-circle"></i> Formations incluses</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="guarantees-band reveal">
  <div class="container">
    <div class="guarantees-grid">
      <div class="guarantee-item">
        <div class="guarantee-icon"><i class="fas fa-shield-alt"></i></div>
        <span>Données sécurisées & hébergées en Afrique</span>
      </div>
      <div class="guarantee-item">
        <div class="guarantee-icon"><i class="fas fa-undo-alt"></i></div>
        <span>Annulation à tout moment, sans frais</span>
      </div>
      <div class="guarantee-item">
        <div class="guarantee-icon"><i class="fas fa-file-export"></i></div>
        <span>Export complet de vos données garanti</span>
      </div>
      <div class="guarantee-item">
        <div class="guarantee-icon"><i class="fas fa-users"></i></div>
        <span>Migration accompagnée par notre équipe</span>
      </div>
    </div>
  </div>
</div>

<section class="compare-section">
  <div class="container">
    <div class="compare-header reveal">
      <div class="stag center">Comparer en détail</div>
      <h2 class="stitle">Toutes les fonctionnalités,<br>plan par plan</h2>
    </div>
    <div class="compare-table-wrap reveal">
      <table class="compare-table">
        <thead>
          <tr>
            <th>Fonctionnalité</th>
            <th>FREE</th>
            <th>STARTER</th>
            <th>GROWTH</th>
            <th class="col-feat">BUSINESS</th>
            <th>ENTERPRISE</th>
          </tr>
        </thead>
        <tbody>
            <tr>
              <td>Prix / mois</td>
              <td>0 XOF</td>
              <td>10 000 XOF</td>
              <td>25 000 XOF</td>
              <td class="col-feat">50 000 XOF</td>
              <td>Sur mesure</td>
            </tr>
            <tr>
              <td>Nombre d'employés</td>
              <td>1 – 3</td>
              <td>1 – 10</td>
              <td>10 – 30</td>
              <td class="col-feat">30 – 80</td>
              <td>100+</td>
            </tr>
            <tr>
              <td>Authentification & comptes</td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
              <td>Gestion des employés</td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
              <td>Pointage QR code</td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
              <td>Badge + scan (mobile / USB)</td>
              <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
              <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
                <td>Détection retard / absence</td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
                <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
                <td>Gestion des congés</td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
                <td>Dashboard</td>
                <td>Basique</td>
                <td>Basique</td>
                <td>Avancé</td>
                <td class="col-feat">Complet</td>
                <td>Complet</td>
            </tr>
            <tr>
                <td>Export Excel</td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td class="col-feat"><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
                <td>Multi-sites</td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td class="col-feat"><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
            <tr>
                <td>Support</td>
                <td>Email</td>
                <td>WhatsApp</td>
                <td>Prioritaire</td>
                <td class="col-feat">Prioritaire</td>
                <td>Dédié</td>
            </tr>
            <tr>
                <td>Installation / formation</td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td class="col-feat"><i class="fas fa-times-circle" style="color: var(--gl);"></i></td>
                <td><i class="fas fa-check-circle" style="color: var(--vp);"></i></td>
            </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="faq-section">
  <div class="container">
    <div class="faq-header reveal">
      <div class="stag center">Questions fréquentes</div>
      <h2 class="stitle">Tout ce que vous<br>voulez savoir</h2>
    </div>
    <div class="faq-grid reveal">
      <div class="faq-item">
        <div class="faq-q">Puis-je changer de plan à tout moment ?<i class="fas fa-chevron-down"></i></div>
        <div class="faq-a">Oui, vous pouvez upgrader ou downgrader votre plan à n'importe quel moment depuis votre espace client.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Le plan Free est-il vraiment gratuit ?<i class="fas fa-chevron-down"></i></div>
        <div class="faq-a">Oui, le plan Free est 100% gratuit et sans limite de durée.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Mes données sont-elles sécurisées ?<i class="fas fa-chevron-down"></i></div>
        <div class="faq-a">Absolument. Toutes vos données sont chiffrées et hébergées localement.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q">Y a-t-il des frais de mise en place ?<i class="fas fa-chevron-down"></i></div>
        <div class="faq-a">Non, aucun frais d'activation pour les plans standards.</div>
      </div>
    </div>
  </div>
</section>

{{-- <section class="cta-band">
  <div class="cta-inner">
    <div class="cta-left">
      <div class="cta-left-bg"></div>
      <h2 class="cta-title">Pas encore décidé ?<br>Parlons-en.</h2>
      <p class="cta-sub">Un conseiller vous aide à trouver le plan qui correspond exactement à votre organisation.</p>
      <div class="d-flex gap-3 flex-wrap">
        <a href="{{ route('siteVitrine.demonstration') }}" class="btn-prim" style="background:#fff;color:var(--vp);"><i class="fas fa-rocket"></i> Choisir ce Plan</a>
        <a href="#" class="btn-out" style="border-color:rgba(255,255,255,.25);color:rgba(255,255,255,.75);"><i class="fas fa-headset"></i> Contacter un conseiller</a>
      </div>
    </div>
    <div class="cta-right">
      <div class="cta-right-icon"><i class="fas fa-calendar-check"></i></div>
      <p><strong style="color:#fff;">Démo personnalisée</strong><br>Réservez une démonstration de 30 min</p>
    </div>
  </div>
</section> --}}

<footer class="footer-modern">
  <div class="container">
    <div class="row g-4">
      <!-- Colonne Logo & Présentation -->
      <div class="col-lg-4 col-md-6">
        <a href="{{ route('siteVitrine.acceuil') }}" class="footer-logo">Easy<span>Point</span></a>
        <p class="footer-description mt-3">{{ $footer->description ?? 'La solution de suivi de présence intelligente pour les structures exigeantes. Performance, simplicité et rigueur.' }}</p>
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
          <p><i class="fas fa-envelope"></i> {{ $footer->email ?? 'contact@easypoint.com' }}</p>
          <p><i class="fas fa-phone-alt"></i> {{ $footer->phone ?? '+228 90 00 00 00' }}</p>
          <p><i class="fas fa-map-marker-alt"></i> {{ $footer->address ?? 'Lomé, Togo' }}</p>
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

    /* Plan Sur mesure statique */
    .plan-card.plan-custom {
        position: relative;
        border: 2px solid transparent;
        background: linear-gradient(white, white) padding-box,
            linear-gradient(135deg, #667eea, #764ba2) border-box;
        animation: customPulse 2s infinite;
        overflow: hidden;
    }

    .plan-card.plan-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s;
    }

    .plan-card.plan-custom:hover::before {
        left: 100%;
    }

    @keyframes customPulse {
        0% {
            transform: translateY(0);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        50% {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        }
        100% {
            transform: translateY(0);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
    }

    .plan-card.plan-custom:hover {
        animation: none;
        transform: translateY(-10px);
    }

    /* Responsive pour le plan sur mesure */
    @media (max-width: 768px) {
        .plan-card.plan-custom {
            margin: 20px;
            animation: none;
        }

        .plan-card.plan-custom:hover {
            transform: translateY(-5px);
        }
    }
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
            ustify-content: center;
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
  if(mobileBtn) mobileBtn.onclick = () => mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';

  window.onscroll = () => {
    document.getElementById('topnav').classList.toggle('scrolled', window.scrollY > 50);
    document.querySelectorAll('.reveal').forEach(el => {
      if(el.getBoundingClientRect().top < window.innerHeight - 80) el.classList.add('vis');
    });
  };

  const billingBtns = document.querySelectorAll('.billing-btn');
  const prices = document.querySelectorAll('.plan-price[data-monthly]');
  const ctaButtons = document.querySelectorAll('.plan-cta');

  billingBtns.forEach(btn => {
    btn.onclick = () => {
      const billing = btn.dataset.billing;
      billingBtns.forEach(b => b.classList.toggle('active', b === btn));

      // 1. Mettre à jour l'affichage des prix
      prices.forEach(el => {
        const val = el.dataset[billing];
        if(val && val != '0') {
          el.textContent = Number(val).toLocaleString('fr-FR');
        } else if(val == '0') {
          el.textContent = 'Gratuit';
        }
      });

      // 2. Mettre à jour dynamiquement les liens d'inscription pour inclure la facturation
      ctaButtons.forEach(button => {
        const planId = button.dataset.planId;
        const registerUrl = "{{ route('siteVitrine.demonstration') }}";
        if (billing === 'yearly') {
          button.setAttribute('href', `${registerUrl}?plan=${planId}&billing=yearly`);
        } else {
          button.setAttribute('href', `${registerUrl}?plan=${planId}&billing=monthly`);
        }
      });
    };
  });

  document.querySelectorAll('.faq-item').forEach(item => {
    item.querySelector('.faq-q').onclick = () => {
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      item.classList.add('open');
    };
  });

  window.dispatchEvent(new Event('scroll'));
</script>
</body>
</html>
