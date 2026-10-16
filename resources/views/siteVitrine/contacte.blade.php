<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>EasyPoint — Contact</title>

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
      --t: all 0.32s cubic-bezier(.4,0,.2,1);
    }

    html { scroll-behavior: smooth; }
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

    /* ── BREADCRUMB ── */
    .breadcrumb-strip { background:var(--gtc); border-bottom:1px solid var(--gl); padding:12px 0; }
    .bc-item { font-size:.78rem; color:var(--gm); }
    .bc-item.active { color:var(--vp); font-weight:600; }
    .bc-sep { color:var(--gl); margin:0 6px; font-size:.7rem; }

    /* ═══════════════════════════════════════════
       LAYOUT PRINCIPAL — UNE SEULE COLONNE
    ═══════════════════════════════════════════ */
    .contact-layout {
      display: block;
      min-height: calc(100vh - 73px);
    }

    /* ── PANNEAU DROIT (formulaire) ── */
    .contact-right {
      background: #fff;
      padding: 64px 56px;
      max-width: 820px;
      margin: 0 auto;
      width: 100%;
    }

    .intent-tabs {
      display: flex; gap: 8px; flex-wrap: wrap;
      margin-bottom: 40px;
    }
    .intent-tab {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 9px 18px; border-radius: 9999px;
      font-size: .8rem; font-weight: 700;
      cursor: pointer; border: 1.5px solid var(--gl);
      background: transparent; color: var(--gm);
      transition: var(--t);
    }
    .intent-tab i { font-size: .75rem; }
    .intent-tab:hover { border-color: var(--vp); color: var(--vp); }
    .intent-tab.active { background: var(--vp); border-color: var(--vp); color: #fff; }

    .form-header { margin-bottom: 36px; }
    .form-stag {
      font-size: .68rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .14em;
      color: var(--vp); display: flex; align-items: center; gap: 8px;
      margin-bottom: 10px;
    }
    .form-stag::before { content:''; width:20px; height:2px; background:var(--vp); border-radius:2px; }
    .form-title {
      font-size: clamp(1.5rem, 2.5vw, 2.2rem);
      font-weight: 900; color: var(--gf);
      line-height: 1.1; margin-bottom: 10px;
    }
    .form-desc { font-size: .88rem; color: var(--gm); line-height: 1.7; }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }

    .form-label {
      font-size: .73rem; font-weight: 700;
      color: var(--gf); letter-spacing: .02em;
    }
    .form-label span { color: var(--vp); }

    .form-input, .form-select, .form-textarea {
      width: 100%;
      background: var(--gtc);
      border: 1.5px solid transparent;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: .88rem;
      color: var(--gf);
      font-family: 'Inter', sans-serif;
      outline: none;
      transition: var(--t);
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
      background: #fff;
      border-color: var(--vp);
      box-shadow: 0 0 0 3px rgba(46,125,100,.1);
    }
    .form-textarea { resize: vertical; min-height: 120px; line-height: 1.6; }

    .form-select-wrap { position: relative; }
    .form-select-wrap::after {
      content: '\f078';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
      font-size: .65rem; color: var(--gm); pointer-events: none;
    }
    .form-select { cursor: pointer; padding-right: 36px; }

    .file-zone {
      border: 1.5px dashed var(--gl);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: var(--t);
      position: relative;
    }
    .file-zone:hover { border-color: var(--vp); background: var(--vc); }
    .file-zone input[type="file"] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .file-zone-icon { font-size: 1.3rem; color: var(--gm); margin-bottom: 6px; }
    .file-zone-txt { font-size: .78rem; color: var(--gm); }
    .file-zone-txt strong { color: var(--vp); }

    .rgpd-row {
      display: flex; align-items: flex-start; gap: 10px;
      margin: 16px 0 24px;
    }
    .rgpd-check {
      width: 18px; height: 18px;
      border: 2px solid var(--gl);
      border-radius: 5px;
      flex-shrink: 0; margin-top: 1px;
      cursor: pointer;
    }
    .rgpd-label {
      font-size: .78rem; color: var(--gm); line-height: 1.5;
    }
    .rgpd-label a { color: var(--vp); text-decoration: none; }
    .rgpd-label a:hover { text-decoration: underline; }

    .btn-submit {
      width: 100%;
      background: var(--vp); color: #fff;
      border: none; border-radius: 12px;
      padding: 15px;
      font-size: .95rem; font-weight: 700;
      font-family: 'Syne', sans-serif;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      transition: var(--t);
    }
    .btn-submit:hover { background: var(--vf); transform: translateY(-2px); box-shadow: 0 10px 28px rgba(46,125,100,.3); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .form-success {
      display: none;
      text-align: center; padding: 48px 24px;
      flex-direction: column; align-items: center; gap: 16px;
    }
    .success-icon {
      width: 72px; height: 72px; border-radius: 50%;
      background: var(--vc);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.8rem; color: var(--vp);
      margin-bottom: 8px;
    }
    .success-title { font-size: 1.6rem; font-weight: 900; color: var(--gf); }
    .success-sub { font-size: .9rem; color: var(--gm); max-width: 380px; line-height: 1.7; }

    .loading-spinner {
      display: none;
      text-align: center;
      padding: 20px;
    }
    .loading-spinner i {
      font-size: 2rem;
      color: var(--vp);
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .error-message {
      color: #dc3545;
      font-size: 0.75rem;
      margin-top: 4px;
    }

    .toast-notification {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      padding: 15px 20px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      gap: 12px;
      transform: translateX(400px);
      transition: transform 0.3s ease;
    }
    .toast-notification.show {
      transform: translateX(0);
    }
    .toast-notification.success { border-left: 4px solid var(--vp); }
    .toast-notification.error { border-left: 4px solid #dc3545; }
    .toast-notification i { font-size: 1.2rem; }
    .toast-notification.success i { color: var(--vp); }
    .toast-notification.error i { color: #dc3545; }
    .toast-notification .toast-content { flex: 1; font-size: 0.85rem; color: var(--gf); }
    .toast-notification .toast-close { cursor: pointer; color: var(--gm); font-size: 0.8rem; }

    .quick-band {
      background: var(--gtc);
      border-top: 1px solid var(--gl);
      padding: 48px 0;
    }
    .quick-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--gl);
      border-radius: 16px;
      overflow: hidden;
    }
    .quick-card {
      background: #fff;
      padding: 32px 28px;
      display: flex; flex-direction: column; gap: 12px;
      transition: var(--t);
    }
    .quick-card:hover { background: var(--vc); }
    .quick-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: var(--vc); color: var(--vp);
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
    }
    .quick-title { font-size: .95rem; font-weight: 800; color: var(--gf); }
    .quick-desc  { font-size: .82rem; color: var(--gm); line-height: 1.6; }
    .quick-link  {
      font-size: .78rem; font-weight: 700; color: var(--vp);
      text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
      transition: var(--t);
    }
    .quick-link:hover { gap: 10px; }

    /* Footer */
    .footer-modern {
      background: var(--gf);
      color: var(--gl);
      padding: 4rem 0 2rem;
      border-top: 1px solid rgba(255,255,255,0.08);
    }
    .footer-logo {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: #fff;
      text-decoration: none;
    }
    .footer-logo span { color: var(--vp); }
    .footer-description {
      font-size: 0.85rem;
      line-height: 1.6;
      color: rgba(255,255,255,0.65);
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
    .footer-links li { margin-bottom: 0.7rem; }
    .footer-links a {
      color: rgba(255,255,255,0.6);
      text-decoration: none;
      font-size: 0.85rem;
      transition: var(--t);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .footer-links a i { font-size: 0.7rem; color: var(--vp); }
    .footer-links a:hover { color: #fff; transform: translateX(5px); }
    .footer-contact-info p {
      margin-bottom: 0.7rem;
      font-size: 0.85rem;
      color: rgba(255,255,255,0.6);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .footer-contact-info p i { width: 25px; color: var(--vp); }
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
    }
    .social-icons a:hover { background: var(--vp); color: #fff; transform: translateY(-3px); }
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
    }
    .newsletter-form input::placeholder { color: rgba(255,255,255,0.4); }
    .newsletter-form button {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background: var(--vp);
      color: #fff;
      cursor: pointer;
      transition: var(--t);
    }
    .newsletter-form button:hover { background: var(--vf); transform: translateY(-2px); }
    .footer-bottom {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      padding-top: 1.4rem;
      margin-top: 2rem;
      border-top: 1px solid rgba(255,255,255,0.08);
      font-size: 0.75rem;
      color: rgba(255,255,255,0.5);
    }
    .footer-bottom-links { display: flex; gap: 0.8rem; }
    .footer-bottom-links a { color: rgba(255,255,255,0.5); text-decoration: none; }
    .footer-bottom-links a:hover { color: var(--vp); }

    @media (max-width: 860px) {
      .contact-right { padding: 40px 24px; }
      .form-grid { grid-template-columns: 1fr; }
      .form-group.full { grid-column: span 1; }
      .quick-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
      .intent-tab { padding: 8px 14px; font-size: .75rem; }
      .contact-right { padding: 32px 20px; }
    }
  </style>
</head>
<body>

<!-- ════════ NAVBAR ════════ -->
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
        <a href="{{ route('siteVitrine.tarifs') }}" class="nav-link-custom">Tarifs</a>
        <a href="{{ route('siteVitrine.contact') }}" class="nav-link-custom active">Contact</a>
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
        <a href="{{ route('siteVitrine.tarifs') }}" class="nav-link-custom py-2">Tarifs</a>
        <a href="{{ route('siteVitrine.contact') }}" class="nav-link-custom py-2 active">Contact</a>
        <hr>
        <a href="{{ route('login') }}" class="btn-prim text-center justify-content-center">Connexion</a>
      </div>
    </div>
  </div>
</nav>

<!-- ════════ BREADCRUMB ════════ -->
<div class="breadcrumb-strip">
  <div class="container">
    <a href="{{ route('siteVitrine.acceuil') }}" class="bc-item text-decoration-none">Accueil</a>
    <span class="bc-sep"><i class="fas fa-chevron-right" style="font-size:9px;"></i></span>
    <span class="bc-item active">Contact</span>
  </div>
</div>

<!-- ════════ LAYOUT PRINCIPAL ════════ -->
<div class="contact-layout">

  <!-- ── PANNEAU DROIT — FORMULAIRE ── -->
  <main class="contact-right">

    <div class="intent-tabs">
      <button type="button" class="intent-tab active" data-intent="general">
        <i class="fas fa-comment"></i> Renseignement
      </button>
      <button type="button" class="intent-tab" data-intent="devis">
        <i class="fas fa-file-invoice"></i> Obtenir un devis
      </button>
      <button type="button" class="intent-tab" data-intent="support">
        <i class="fas fa-headset"></i> Support technique
      </button>
    </div>

    <div class="form-header">
      <div class="form-stag" id="formStag">Message général</div>
      <h2 class="form-title" id="formTitle">Envoyez-nous<br>un message</h2>
      <p class="form-desc" id="formDesc">Décrivez votre besoin, nous vous répondons sous 24h ouvrées.</p>
    </div>

    <form id="contactForm" novalidate>
      @csrf
      <input type="hidden" name="intent" id="intentValue" value="general">

      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Prénom <span>*</span></label>
          <input type="text" name="first_name" class="form-input" placeholder="Komi" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nom <span>*</span></label>
          <input type="text" name="last_name" class="form-input" placeholder="Agbeko" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email professionnel <span>*</span></label>
          <input type="email" name="email" class="form-input" placeholder="komi@entreprise.com" required>
        </div>
        <div class="form-group">
          <label class="form-label">Téléphone</label>
          <input type="tel" name="phone" class="form-input" placeholder="+228 90 00 00 00">
        </div>
        <div class="form-group">
          <label class="form-label">Entreprise <span>*</span></label>
          <input type="text" name="company" class="form-input" placeholder="Nom de votre organisation" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nombre d'employés</label>
          <div class="form-select-wrap">
            <select name="employees" class="form-select">
              <option value="">Sélectionner</option>
              <option>1 – 3 employés</option>
              <option>4 – 10 employés</option>
              <option>11 – 30 employés</option>
              <option>31 – 80 employés</option>
              <option>100+ employés</option>
            </select>
          </div>
        </div>
        <div class="form-group full">
          <label class="form-label">Sujet <span>*</span></label>
          <div class="form-select-wrap">
            <select name="subject" class="form-select" required>
              <option value="">Choisir un sujet</option>
              <option>Renseignements généraux</option>
              <option>Demande de démonstration</option>
              <option>Demande de devis</option>
              <option>Support technique</option>
              <option>Partenariat commercial</option>
              <option>Autre</option>
            </select>
          </div>
        </div>
        <div class="form-group full">
          <label class="form-label">Votre message <span>*</span></label>
          <textarea name="message" class="form-textarea" id="msgArea" placeholder="Décrivez votre projet, vos besoins ou votre question..." required></textarea>
        </div>
        <div class="form-group full">
          <label class="form-label">Pièce jointe <span style="color:var(--gm);font-weight:500;">(optionnel)</span></label>
          <div class="file-zone" id="fileZone">
            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.png,.jpg">
            <div class="file-zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
            <div class="file-zone-txt">Glissez un fichier ou <strong>cliquez pour parcourir</strong><br><span style="font-size:.7rem;opacity:.6;">PDF, DOC, PNG, JPG — max 5 Mo</span></div>
          </div>
        </div>
      </div>

      <div class="rgpd-row">
        <input type="checkbox" name="rgpd" class="rgpd-check" id="rgpdCheck" required>
        <label class="rgpd-label" for="rgpdCheck">
          J'accepte que mes données soient traitées conformément à la <a href="#">politique de confidentialité</a> d'EasyPoint.
        </label>
      </div>

      <button type="submit" class="btn-submit" id="submitBtn">
        <i class="fas fa-paper-plane"></i>
        Envoyer le message
      </button>
    </form>

    <div class="loading-spinner" id="loadingSpinner">
      <i class="fas fa-spinner"></i>
      <p>Envoi en cours...</p>
    </div>

    <div class="form-success" id="formSuccess">
      <div class="success-icon"><i class="fas fa-check"></i></div>
      <h3 class="success-title">Message envoyé !</h3>
      <p class="success-sub">Merci pour votre message. Notre équipe vous répondra dans les 24h ouvrées.</p>
      <button class="btn-prim" id="backBtn"><i class="fas fa-undo"></i> Envoyer un autre message</button>
    </div>

  </main>
</div>

<!-- ════════ QUICK ACCESS ════════ -->
<section class="quick-band">
  <div class="container">
    <div class="quick-grid">
      <div class="quick-card">
        <div class="quick-icon"><i class="fas fa-book-open"></i></div>
        <div class="quick-title">Centre d'aide</div>
        <div class="quick-desc">Parcourez nos guides, tutoriels et FAQ pour trouver une réponse immédiate.</div>
        <a href="#" class="quick-link">Accéder <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="quick-card">
        <div class="quick-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="quick-title">Réserver un appel</div>
        <div class="quick-desc">Choisissez un créneau et échangez en direct avec un expert EasyPoint.</div>
        <a href="#" class="quick-link">Réserver <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="quick-card">
        <div class="quick-icon"><i class="fas fa-chart-line"></i></div>
        <div class="quick-title">Devis personnalisé</div>
        <div class="quick-desc">Recevez une offre adaptée à vos besoins spécifiques.</div>
        <a href="#" class="quick-link">Demander <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ════════ FOOTER ════════ -->
<footer class="footer-modern">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a href="{{ route('siteVitrine.acceuil') }}" class="footer-logo">Easy<span>Point</span></a>
        <p class="footer-description mt-3">
          {{ $footer->description ?? 'La solution de suivi de présence intelligente pour les structures exigeantes.' }}
        </p>
        <div class="social-icons d-flex gap-3 mt-4">
          <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.linkedin.com/"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://x.com/@"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <h5 class="footer-title">Produit</h5>
        <ul class="footer-links">
            <li><a href="{{route('siteVitrine.acceuil')}}"><i class="fas fa-chevron-right"></i>Acceuil</a></li>
            <li><a href="{{route('siteVitrine.apropos')}}"><i class="fas fa-chevron-right"></i>A Propos</a></li>
            <li><a href="{{ route('siteVitrine.fonctionnalites') }}"><i class="fas fa-chevron-right"></i>Fonctionnalités</a></li>
            <li><a href="{{ route('siteVitrine.tarifs') }}"><i class="fas fa-chevron-right"></i>Tarifs & Offres</a></li>
        </ul>
      </div>
      {{-- <div class="col-lg-2 col-md-6">
        <h5 class="footer-title">Ressources</h5>
        <ul class="footer-links">
            <li><a href="#"><i class="fas fa-chevron-right"></i>Centre d'aide</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i>Documentation API</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i>Blog & Actualités</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i>Webinaires</a></li>
        </ul>
      </div> --}}
      <div class="col-lg-4 col-md-6">
        <h5 class="footer-title">Contact</h5>
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
      </div>
    </div>
    <div class="footer-bottom">
        <div>
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

<script>
  // Mobile menu
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
    });
  }

  // Navbar scroll
  window.addEventListener('scroll', () => {
    document.getElementById('topnav').classList.toggle('scrolled', window.scrollY > 50);
  });

  // Intent data
  const intentData = {
    general: {
      stag: 'Message général',
      title: 'Envoyez-nous<br>un message',
      desc: 'Décrivez votre besoin, nous vous répondons sous 24h ouvrées.',
      placeholder: 'Décrivez votre projet, vos besoins ou votre question...'
    },
    devis: {
      stag: 'Devis personnalisé',
      title: 'Obtenez votre<br>offre sur mesure',
      desc: 'Indiquez vos besoins — nous vous préparons une offre adaptée.',
      placeholder: 'Décrivez votre organisation et vos besoins spécifiques...'
    },
    support: {
      stag: 'Support technique',
      title: 'Besoin d\'aide<br>technique ?',
      desc: 'Notre équipe technique vous répond en priorité.',
      placeholder: 'Décrivez le problème que vous rencontrez...'
    }
  };

  // Intent tabs
  document.querySelectorAll('.intent-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.intent-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const intent = tab.dataset.intent;
      document.getElementById('intentValue').value = intent;
      const data = intentData[intent];
      if (data) {
        document.getElementById('formStag').textContent = data.stag;
        document.getElementById('formTitle').innerHTML = data.title;
        document.getElementById('formDesc').textContent = data.desc;
        document.getElementById('msgArea').placeholder = data.placeholder;
      }
    });
  });

  // File zone
  const fileZone = document.getElementById('fileZone');
  const fileInput = fileZone.querySelector('input[type="file"]');

  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (file) {
      fileZone.querySelector('.file-zone-txt').innerHTML =
        `<strong style="color:var(--vp)"><i class="fas fa-check-circle"></i> ${file.name}</strong><br><span style="font-size:.7rem;">${(file.size/1024).toFixed(0)} Ko</span>`;
    }
  });

  fileZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileZone.style.borderColor = 'var(--vp)';
  });

  fileZone.addEventListener('dragleave', () => {
    fileZone.style.borderColor = '';
  });

  fileZone.addEventListener('drop', (e) => {
    e.preventDefault();
    fileZone.style.borderColor = '';
    const file = e.dataTransfer.files[0];
    if (file) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      fileInput.files = dataTransfer.files;
      fileZone.querySelector('.file-zone-txt').innerHTML =
        `<strong style="color:var(--vp)"><i class="fas fa-check-circle"></i> ${file.name}</strong><br><span style="font-size:.7rem;">${(file.size/1024).toFixed(0)} Ko</span>`;
    }
  });

  // Form submission
  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const loadingSpinner = document.getElementById('loadingSpinner');
  const formSuccess = document.getElementById('formSuccess');
  const contactRight = document.querySelector('.contact-right');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    loadingSpinner.style.display = 'block';

    try {
      const response = await fetch('{{ route("contact.submit") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        form.style.display = 'none';
        loadingSpinner.style.display = 'none';
        formSuccess.style.display = 'flex';

        // Reset form
        form.reset();
        fileZone.querySelector('.file-zone-txt').innerHTML =
          'Glissez un fichier ou <strong>cliquez pour parcourir</strong><br><span style="font-size:.7rem;opacity:.6;">PDF, DOC, PNG, JPG — max 5 Mo</span>';
      } else if (data.errors) {
        showErrors(data.errors);
      }
    } catch (error) {
      console.error('Erreur:', error);
      showToast('Une erreur est survenue. Veuillez réessayer.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      loadingSpinner.style.display = 'none';
    }
  });

  function showErrors(errors) {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => {
      el.classList.remove('error');
    });

    for (const [field, messages] of Object.entries(errors)) {
      const input = document.querySelector(`[name="${field}"]`);
      if (input) {
        input.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = messages.join(', ');
        input.parentNode.appendChild(errorDiv);

        setTimeout(() => {
          errorDiv.remove();
          input.classList.remove('error');
        }, 5000);
      }
    }
  }

  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
      <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
      <div class="toast-content">${message}</div>
      <div class="toast-close"><i class="fas fa-times"></i></div>
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);

    toast.querySelector('.toast-close').addEventListener('click', () => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    });

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }

  // Back button
  document.getElementById('backBtn').addEventListener('click', () => {
    formSuccess.style.display = 'none';
    form.style.display = 'block';
    form.reset();
    document.getElementById('rgpdCheck').checked = false;
    fileZone.querySelector('.file-zone-txt').innerHTML =
      'Glissez un fichier ou <strong>cliquez pour parcourir</strong><br><span style="font-size:.7rem;opacity:.6;">PDF, DOC, PNG, JPG — max 5 Mo</span>';
  });

  // Remove error on focus
  document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
    input.addEventListener('focus', () => {
      input.classList.remove('error');
      const errorDiv = input.parentNode.querySelector('.error-message');
      if (errorDiv) errorDiv.remove();
    });
  });
</script>
</body>
</html>
