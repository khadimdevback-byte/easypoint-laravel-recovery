<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover" />
  <title>EasyPoint — Gestion de présence intelligente</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #ffffff;
      color: #1a2a2e;
      overflow-x: hidden;
      scroll-behavior: smooth;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      letter-spacing: -0.02em;
    }

    :root {
      --vert-principal: #2e7d64;
      --vert-clair: #e8f5f1;
      --vert-fonce: #1a5a48;
      --vert-accent: #40a088;
      --gris-fonce: #2d3a3e;
      --gris-moyen: #5a6b6f;
      --gris-clair: #d4dfe2;
      --gres-tres-clair: #eef3f5;
      --blanc: #ffffff;
      --blanc-casse: #fafdfc;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -2px rgb(0 0 0 / 0.05);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.02);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ======= NAVBAR ======= */
    .navbar-glass {
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--gris-clair);
      transition: var(--transition);
      position: sticky;
      top: 0;
      z-index: 1100;
    }
    .navbar-glass.scrolled { box-shadow: var(--shadow-md); }
    .logo {
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--gris-fonce);
      text-decoration: none;
    }
    .logo span { color: var(--vert-principal); }
    .nav-link-custom {
      font-weight: 500;
      color: var(--gris-moyen);
      text-decoration: none;
      transition: var(--transition);
      font-size: 0.95rem;
      position: relative;
    }
    .nav-link-custom::after {
      content: '';
      position: absolute;
      bottom: -4px; left: 0;
      width: 0; height: 2px;
      background: var(--vert-principal);
      border-radius: 2px;
      transition: width 0.3s ease;
    }
    .nav-link-custom:hover::after, .nav-link-custom.active::after { width: 100%; }
    .nav-link-custom:hover, .nav-link-custom.active { color: var(--vert-principal); }

    /* ======= BOUTONS ======= */
    .btn-primary-custom {
      background: var(--vert-principal);
      color: white;
      border: none;
      border-radius: 9999px;
      padding: 0.7rem 1.8rem;
      font-weight: 600;
      font-size: 0.9rem;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      cursor: pointer;
    }
    .btn-primary-custom:hover {
      background: var(--vert-fonce);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(46,125,100,0.35);
      color: white;
    }
    .btn-outline-custom {
      background: transparent;
      border: 1.5px solid var(--gris-clair);
      border-radius: 9999px;
      padding: 0.7rem 1.8rem;
      font-weight: 600;
      font-size: 0.9rem;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      color: var(--gris-fonce);
      cursor: pointer;
    }
    .btn-outline-custom:hover {
      border-color: var(--vert-principal);
      color: var(--vert-principal);
      transform: translateY(-2px);
    }

    /* ======= HERO SECTION ======= */
    .hero-section {
      position: relative;
      min-height: 100vh;
      background: linear-gradient(135deg, #1a3830 0%, #2d3a3e 40%, #1a3830 100%);
      display: flex;
      align-items: center;
      overflow: hidden;
      padding: 6rem 0 4rem;
    }

    /* Grille de fond animée */
    .hero-grid-bg {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(46,125,100,0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(46,125,100,0.08) 1px, transparent 1px);
      background-size: 60px 60px;
      animation: gridDrift 20s linear infinite;
    }
    @keyframes gridDrift {
      0%   { transform: translate(0, 0); }
      100% { transform: translate(60px, 60px); }
    }

    /* Orbes lumineux */
    .hero-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      animation: orbPulse 6s ease-in-out infinite;
    }
    .hero-orb-1 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(46,125,100,0.28) 0%, transparent 70%);
      top: -100px; right: -100px;
      animation-delay: 0s;
    }
    .hero-orb-2 {
      width: 350px; height: 350px;
      background: radial-gradient(circle, rgba(64,160,136,0.2) 0%, transparent 70%);
      bottom: -80px; left: 10%;
      animation-delay: 3s;
    }
    .hero-orb-3 {
      width: 250px; height: 250px;
      background: radial-gradient(circle, rgba(46,125,100,0.15) 0%, transparent 70%);
      top: 50%; left: 45%;
      animation-delay: 1.5s;
    }
    @keyframes orbPulse {
      0%, 100% { transform: scale(1) translate(0,0); opacity: 0.8; }
      50%       { transform: scale(1.15) translate(10px, -10px); opacity: 1; }
    }

    /* Particules flottantes */
    .hero-particles {
      position: absolute;
      inset: 0;
      overflow: hidden;
      pointer-events: none;
    }
    .particle {
      position: absolute;
      width: 3px; height: 3px;
      background: rgba(46,125,100,0.6);
      border-radius: 50%;
      animation: particleFloat linear infinite;
    }
    @keyframes particleFloat {
      0%   { transform: translateY(100vh) scale(0); opacity: 0; }
      10%  { opacity: 1; }
      90%  { opacity: 1; }
      100% { transform: translateY(-20px) scale(1); opacity: 0; }
    }

    /* Contenu hero */
    .hero-content { position: relative; z-index: 10; }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(46,125,100,0.2);
      border: 1px solid rgba(46,125,100,0.4);
      color: #6ecfb5;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      padding: 0.4rem 1rem;
      border-radius: 9999px;
      margin-bottom: 1.5rem;
      animation: fadeSlideDown 0.7s ease both;
    }
    .hero-badge-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: #40a088;
      animation: blink 2s ease-in-out infinite;
    }
    @keyframes blink {
      0%, 100% { opacity: 1; }
      50%       { opacity: 0.3; }
    }

    .hero-title {
      font-size: clamp(2.4rem, 5vw, 4.2rem);
      font-weight: 800;
      color: white;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      animation: fadeSlideDown 0.7s ease 0.15s both;
    }
    .hero-title .accent {
      background: linear-gradient(135deg, #40a088, #6ecfb5);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      position: relative;
    }
    .hero-title .accent::after {
      content: '';
      position: absolute;
      bottom: 2px; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, #40a088, transparent);
      border-radius: 2px;
    }

    .hero-desc {
      font-size: 1.1rem;
      color: rgba(255,255,255,0.68);
      line-height: 1.7;
      max-width: 540px;
      margin-bottom: 2.5rem;
      animation: fadeSlideDown 0.7s ease 0.3s both;
    }

    .hero-cta-group {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
      margin-bottom: 3rem;
      animation: fadeSlideDown 0.7s ease 0.45s both;
    }

    .hero-trust {
      display: flex;
      align-items: center;
      gap: 1rem;
      animation: fadeSlideDown 0.7s ease 0.6s both;
    }
    .trust-avatars {
      display: flex;
      margin-left: 0;
    }
    .trust-avatar {
      width: 34px; height: 34px;
      border-radius: 50%;
      border: 2px solid rgba(255,255,255,0.2);
      background: var(--vert-principal);
      display: flex; align-items: center; justify-content: center;
      font-size: 0.65rem; font-weight: 700; color: white;
      margin-left: -8px;
    }
    .trust-avatar:first-child { margin-left: 0; }
    .trust-text {
      font-size: 0.78rem;
      color: rgba(255,255,255,0.55);
      line-height: 1.4;
    }
    .trust-text strong { color: rgba(255,255,255,0.85); }

    /* Dashboard mockup */
    .hero-visual {
      position: relative;
      z-index: 10;
      animation: fadeSlideUp 0.9s ease 0.3s both;
    }
    @keyframes fadeSlideUp {
      from { opacity: 0; transform: translateY(40px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeSlideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .dashboard-mockup {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 20px;
      backdrop-filter: blur(20px);
      overflow: hidden;
      box-shadow: 0 40px 80px rgba(0,0,0,0.4), 0 0 0 1px rgba(46,125,100,0.2);
      animation: floatY 6s ease-in-out infinite;
    }
    @keyframes floatY {
      0%, 100% { transform: translateY(0px); }
      50%       { transform: translateY(-12px); }
    }

    .mock-topbar {
      background: rgba(255,255,255,0.06);
      border-bottom: 1px solid rgba(255,255,255,0.07);
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .mock-dot {
      width: 10px; height: 10px;
      border-radius: 50%;
    }
    .mock-dot-r { background: #E24B4A; }
    .mock-dot-y { background: #EF9F27; }
    .mock-dot-g { background: #40a088; }
    .mock-title-bar {
      flex: 1;
      height: 6px;
      background: rgba(255,255,255,0.1);
      border-radius: 3px;
      margin-left: 8px;
    }

    .mock-body { display: flex; }
    .mock-sidebar {
      width: 56px;
      background: rgba(255,255,255,0.03);
      border-right: 1px solid rgba(255,255,255,0.06);
      padding: 12px 8px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }
    .mock-icon {
      width: 32px; height: 32px;
      border-radius: 8px;
      background: rgba(255,255,255,0.06);
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; color: rgba(255,255,255,0.4);
      transition: var(--transition);
    }
    .mock-icon.active {
      background: rgba(46,125,100,0.3);
      color: #6ecfb5;
    }

    .mock-main { flex: 1; padding: 16px; }

    .mock-kpi-row { display: flex; gap: 8px; margin-bottom: 14px; }
    .mock-kpi {
      flex: 1;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 10px;
      padding: 10px;
    }
    .mock-kpi-label {
      font-size: 8px;
      color: rgba(255,255,255,0.4);
      margin-bottom: 4px;
      font-family: 'Inter', sans-serif;
    }
    .mock-kpi-value {
      font-size: 17px;
      font-weight: 700;
      font-family: 'Syne', sans-serif;
    }
    .mock-kpi-value.green { color: #6ecfb5; }
    .mock-kpi-value.white { color: white; }
    .mock-kpi-value.amber { color: #EF9F27; }

    .mock-chart-wrap {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 12px;
    }
    .mock-chart-header {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 10px;
    }
    .mock-chart-title {
      font-size: 9px; font-weight: 600;
      color: rgba(255,255,255,0.6);
      font-family: 'Inter', sans-serif;
    }
    .mock-chart-pill {
      font-size: 7px; padding: 2px 7px;
      background: rgba(46,125,100,0.25);
      color: #6ecfb5;
      border-radius: 9999px;
    }
    .mock-bars {
      display: flex;
      align-items: flex-end;
      gap: 5px;
      height: 60px;
    }
    .mock-bar {
      flex: 1;
      border-radius: 3px 3px 0 0;
      background: rgba(46,125,100,0.3);
      transition: height 0.5s ease;
    }
    .mock-bar.highlight { background: #40a088; }

    .mock-list { display: flex; flex-direction: column; gap: 6px; }
    .mock-list-item {
      display: flex; align-items: center; gap: 8px;
      padding: 6px 8px;
      background: rgba(255,255,255,0.04);
      border-radius: 7px;
      border: 1px solid rgba(255,255,255,0.06);
    }
    .mock-avatar {
      width: 22px; height: 22px;
      border-radius: 50%;
      background: rgba(46,125,100,0.4);
      font-size: 8px; color: #6ecfb5;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-family: 'Inter', sans-serif;
    }
    .mock-info { flex: 1; }
    .mock-name { height: 5px; background: rgba(255,255,255,0.2); border-radius: 2px; width: 70%; margin-bottom: 3px; }
    .mock-sub  { height: 4px; background: rgba(255,255,255,0.08); border-radius: 2px; width: 45%; }
    .mock-status {
      font-size: 7px; padding: 2px 6px;
      border-radius: 9999px;
    }
    .mock-status.in  { background: rgba(46,125,100,0.25); color: #6ecfb5; }
    .mock-status.out { background: rgba(226,75,74,0.2); color: #f0959b; }

    /* Cartes flottantes */
    .hero-float-card {
      position: absolute;
      background: white;
      border-radius: 14px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      padding: 12px 16px;
      display: flex; align-items: center; gap: 10px;
      animation: floatCard 7s ease-in-out infinite;
      z-index: 20;
    }
    .hero-float-card-1 {
      bottom: 30px; left: -30px;
      animation-delay: 0s;
    }
    .hero-float-card-2 {
      top: 20px; right: -20px;
      animation-delay: 3.5s;
    }
    @keyframes floatCard {
      0%, 100% { transform: translateY(0) rotate(-1deg); }
      50%       { transform: translateY(-8px) rotate(1deg); }
    }
    .float-card-icon {
      width: 36px; height: 36px;
      border-radius: 10px;
      background: var(--vert-clair);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: var(--vert-principal);
      flex-shrink: 0;
    }
    .float-card-text .label {
      font-size: 10px; color: var(--gris-moyen);
      font-family: 'Inter', sans-serif;
    }
    .float-card-text .value {
      font-size: 15px; font-weight: 700;
      color: var(--gris-fonce);
      font-family: 'Syne', sans-serif;
    }
    .float-card-text .sub {
      font-size: 9px;
      color: #40a088; font-weight: 600;
      font-family: 'Inter', sans-serif;
    }

    /* ======= SECTIONS ======= */
    .section-tag {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      color: var(--vert-principal);
      margin-bottom: 0.75rem;
    }
    .section-title {
      font-size: clamp(1.6rem, 2.5vw, 2.2rem);
      font-weight: 800;
      color: var(--gris-fonce);
      margin-bottom: 1rem;
    }
    .section-underline {
      width: 60px; height: 3px;
      background: var(--vert-principal);
      margin: 1rem auto 0;
      border-radius: 2px;
    }

    /* ======= STATS BAND ======= */
    .stats-band {
      background: var(--gris-fonce);
      padding: 3rem 0;
    }
    .stat-item {
      text-align: center;
      position: relative;
    }
    .stat-item:not(:last-child)::after {
      content: '';
      position: absolute;
      right: 0; top: 20%; bottom: 20%;
      width: 1px;
      background: rgba(255,255,255,0.12);
    }
    .stat-number {
      font-family: 'Syne', sans-serif;
      font-size: 2.8rem;
      font-weight: 800;
      color: white;
      line-height: 1;
      margin-bottom: 0.4rem;
    }
    .stat-number span { color: #40a088; }
    .stat-label {
      font-size: 0.82rem;
      color: rgba(255,255,255,0.5);
      font-weight: 500;
    }

    /* ======= SECTION "ILS NOUS FONT CONFIANCE" ======= */
    .trust-section {
      background: linear-gradient(135deg, var(--blanc-casse) 0%, var(--gres-tres-clair) 100%);
      padding: 4rem 0;
    }
    .trust-card {
      background: var(--blanc);
      border-radius: 1.5rem;
      padding: 2rem;
      text-align: center;
      transition: var(--transition);
      border: 1px solid var(--gris-clair);
      height: 100%;
    }
    .trust-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-xl);
      border-color: var(--vert-principal);
    }
    .trust-logo-wrapper {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--vert-clair);
      border-radius: 20px;
      transition: var(--transition);
    }
    .trust-card:hover .trust-logo-wrapper {
      background: var(--vert-principal);
    }
    .trust-logo-wrapper i {
      font-size: 2.5rem;
      color: var(--vert-principal);
      transition: var(--transition);
    }
    .trust-card:hover .trust-logo-wrapper i {
      color: white;
    }
    .trust-card h4 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: var(--gris-fonce);
    }
    .trust-card .quote {
      font-size: 0.9rem;
      color: var(--gris-moyen);
      font-style: italic;
      margin: 1rem 0;
      line-height: 1.6;
    }
    .trust-card .author {
      font-weight: 700;
      color: var(--vert-principal);
      font-size: 0.85rem;
      margin-top: 1rem;
    }
    .trust-card .position {
      font-size: 0.75rem;
      color: var(--gris-moyen);
    }
    .trust-stats {
      display: flex;
      justify-content: space-between;
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid var(--gris-clair);
    }
    .trust-stat {
      text-align: center;
      flex: 1;
    }
    .trust-stat .number {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--vert-principal);
      font-family: 'Syne', sans-serif;
    }
    .trust-stat .label {
      font-size: 0.7rem;
      color: var(--gris-moyen);
    }
    .trust-carousel {
      position: relative;
      overflow: hidden;
    }
    .trust-track {
      display: flex;
      transition: transform 0.5s ease;
    }
    .trust-slide {
      flex: 0 0 33.333%;
      padding: 0 15px;
    }
    @media (max-width: 768px) {
      .trust-slide {
        flex: 0 0 100%;
      }
    }
    .trust-nav {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 2rem;
    }
    .trust-nav-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--blanc);
      border: 1px solid var(--gris-clair);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }
    .trust-nav-btn:hover {
      background: var(--vert-principal);
      color: white;
      border-color: var(--vert-principal);
    }
    .trust-dots {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
    }
    .trust-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--gris-clair);
      cursor: pointer;
      transition: var(--transition);
    }
    .trust-dot.active {
      background: var(--vert-principal);
      width: 24px;
      border-radius: 4px;
    }

    /* ======= SERVICE CARDS ======= */
    .service-card {
      background: var(--blanc);
      padding: 1.8rem;
      border: 1px solid var(--gris-clair);
      border-radius: 1rem;
      transition: var(--transition);
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .service-card::before {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--vert-principal), var(--vert-accent));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
    }
    .service-card:hover::before { transform: scaleX(1); }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
      border-color: var(--vert-clair);
    }
    .service-icon {
      width: 55px; height: 55px;
      background: var(--vert-clair);
      border-radius: 0.75rem;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 1.2rem;
      font-size: 1.6rem;
      color: var(--vert-principal);
      transition: var(--transition);
    }
    .service-card:hover .service-icon {
      background: var(--vert-principal);
      color: white;
      transform: scale(1.1);
    }

    /* ======= POURQUOI NOUS ======= */
    .why-us {
      background: linear-gradient(135deg, var(--gres-tres-clair) 0%, var(--blanc-casse) 100%);
    }
    .atout-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }
    .atout-check {
      width: 24px; height: 24px;
      background: var(--vert-principal);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      transition: var(--transition);
    }
    .atout-item:hover .atout-check { transform: scale(1.15); }
    .atout-check svg { width: 14px; height: 14px; color: white; }

    /* ======= SOLUTIONS ======= */
    .solution-card {
      background: var(--blanc);
      border-radius: 1.2rem;
      overflow: hidden;
      border: 1px solid var(--gris-clair);
      transition: var(--transition);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .solution-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-xl);
    }
    .solution-image {
      height: 220px;
      overflow: hidden;
      position: relative;
      background: var(--gris-clair);
    }
    .solution-image img {
      width: 100%; height: 100%;
      object-fit: cover;
      transition: transform 0.5s;
    }
    .solution-card:hover .solution-image img { transform: scale(1.05); }
    .solution-badge {
      position: absolute;
      top: 1rem; left: 1rem;
      background: rgba(46,125,100,0.95);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* ======= DEMO WINDOW ======= */
    .demo-window {
      background: var(--blanc);
      border-radius: 1.5rem;
      border: 1px solid var(--gris-clair);
      box-shadow: var(--shadow-xl);
      overflow: hidden;
    }
    .demo-topbar {
      background: var(--gris-fonce);
      padding: 0.75rem 1.2rem;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .demo-dot { width: 12px; height: 12px; border-radius: 50%; }
    .demo-sidebar {
      background: var(--gres-tres-clair);
      min-height: 350px;
      padding: 1.5rem 1rem;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 10px;
      color: var(--gris-moyen);
      text-decoration: none;
      padding: 0.6rem 0.8rem;
      border-radius: 0.5rem;
      font-size: 0.85rem;
      transition: var(--transition);
    }
    .sidebar-link.active, .sidebar-link:hover {
      background: rgba(46,125,100,0.1);
      color: var(--vert-principal);
    }
    .chart-bar-container {
      display: flex; align-items: flex-end; gap: 8px;
      height: 150px;
      border-bottom: 2px solid var(--gris-clair);
      padding-bottom: 5px;
    }
    .chart-bar-item {
      flex: 1;
      background: var(--vert-principal);
      border-radius: 4px 4px 0 0;
      transition: height 0.6s ease;
    }
    .chart-bar-item:hover { background: var(--vert-accent); }

    /* ======= TARIFS ======= */
    .pricing-card {
      background: var(--blanc);
      border: 1px solid var(--gris-clair);
      border-radius: 1.2rem;
      padding: 2rem;
      transition: var(--transition);
      position: relative;
    }
    .pricing-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-xl); }
    .pricing-card.featured {
      border: 2px solid var(--vert-principal);
      box-shadow: var(--shadow-xl);
    }
    .pricing-badge {
      position: absolute;
      top: 1rem; right: 1rem;
      background: var(--vert-clair);
      color: var(--vert-principal);
      font-size: 0.7rem; font-weight: 700;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
    }

    /* ======= CTA SECTION ======= */
    .cta-section {
      background: linear-gradient(135deg, var(--gris-fonce) 0%, #1e3a33 100%);
      padding: 4rem 2rem;
      border-radius: 2rem;
      margin: 3rem 0;
      position: relative;
      overflow: hidden;
    }
    .cta-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(46,125,100,0.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(46,125,100,0.06) 1px, transparent 1px);
      background-size: 40px 40px;
    }
    .cta-section > * { position: relative; z-index: 1; }

    /* ======= FOOTER ======= */
    .footer-modern {
      background: var(--gris-fonce);
      color: var(--gris-clair);
      padding: 4rem 0 2rem;
      margin-top: 3rem;
    }
    .footer-logo {
      font-family: 'Syne', sans-serif;
      font-size: 1.6rem; font-weight: 800;
      color: white; text-decoration: none;
    }
    .footer-logo span { color: var(--vert-principal); }
    .footer-description { color: #9baeb3; line-height: 1.6; margin-top: 1rem; }
    .social-icons a {
      display: inline-flex; align-items: center; justify-content: center;
      width: 36px; height: 36px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
      color: var(--gris-clair);
      transition: var(--transition);
    }
    .social-icons a:hover {
      background: var(--vert-principal);
      color: white;
      transform: translateY(-3px);
    }
    .footer-title {
      color: white; font-size: 1rem; font-weight: 700;
      margin-bottom: 1.2rem; letter-spacing: 0.05em;
    }
    .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 0.7rem; }
    .footer-links a {
      color: #9baeb3; text-decoration: none;
      font-size: 0.85rem; transition: var(--transition);
      display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .footer-links a:hover { color: var(--vert-principal); transform: translateX(5px); }
    .footer-links a i { font-size: 10px; color: var(--vert-principal); }
    .footer-contact-info { margin-top: 1rem; }
    .footer-contact-info p { margin-bottom: 0.6rem; font-size: 0.85rem; color: #9baeb3; }
    .footer-contact-info i { width: 25px; color: var(--vert-principal); }
    .newsletter-form { display: flex; gap: 0.5rem; margin-top: 1rem; }
    .newsletter-form input {
      flex: 1;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 9999px;
      padding: 0.7rem 1rem;
      color: white; font-size: 0.85rem;
    }
    .newsletter-form input::placeholder { color: #7a949b; }
    .newsletter-form input:focus { outline: none; border-color: var(--vert-principal); }
    .newsletter-form button {
      background: var(--vert-principal); border: none;
      border-radius: 9999px; padding: 0.7rem 1.2rem;
      color: white; font-weight: 600;
      transition: var(--transition); cursor: pointer;
    }
    .newsletter-form button:hover { background: var(--vert-fonce); transform: translateY(-2px); }
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 1.5rem; margin-top: 2rem;
    }
    .footer-bottom-links a {
      color: #7a949b; text-decoration: none;
      font-size: 0.75rem; transition: var(--transition);
    }
    .footer-bottom-links a:hover { color: var(--vert-principal); }
    .footer-copyright { color: #7a949b; font-size: 0.75rem; }
    .footer-heart { color: #2e7d64; }

    /* ======= REVEAL ANIMATIONS ======= */
    .reveal {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* ======= RESPONSIVE ======= */
    @media (max-width: 991px) {
      .hero-visual { margin-top: 3rem; }
      .hero-float-card-1, .hero-float-card-2 { display: none; }
    }
    @media (max-width: 768px) {
      .hero-title { font-size: 2rem; }
      .hero-section { padding: 5rem 0 3rem; }
      .btn-primary-custom, .btn-outline-custom { padding: 0.55rem 1.3rem; font-size: 0.85rem; }
      .stat-number { font-size: 2rem; }
      .stat-item:not(:last-child)::after { display: none; }
      .cta-section { padding: 2rem 1rem; margin: 2rem 0; }
    }
  </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar-glass py-3" id="topnav">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <a  href="{{ route('siteVitrine.acceuil') }}" class="block px-8 py-2 m-0 whitespace-nowrap text-slate-700" href="">
            <img src="{{ asset('images/logo.png') }}"
                class="inline transition-all duration-200 ease-nav-brand"
                alt="main_logo"
                style="height: 40px; width: auto;" />
        </a>
      <div class="d-none d-md-flex gap-4">
        <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom active">Accueil</a>
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom">À Propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom">Fonctionnalités</a>
        <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom">Tarifs</a>
        <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom">Contact</a>
      </div>

      <div class="d-flex gap-2 align-items-center">
        <a href="{{route('login')}}" class="btn-primary-custom"><i class="fas fa-lock"></i> Connexion</a>
        <button class="d-md-none btn btn-link text-dark p-0" id="mobileMenuBtn"><i class="fas fa-bars fs-4"></i></button>
      </div>
    </div>

    <div id="mobileMenu" class="d-md-none mt-3" style="display:none;">
      <div class="d-flex flex-column gap-2 py-2">
        <a href="{{route('siteVitrine.acceuil')}}" class="nav-link-custom py-2">Accueil</a>
        <a href="{{route('siteVitrine.apropos')}}" class="nav-link-custom py-2">À Propos</a>
        <a href="{{route('siteVitrine.fonctionnalites')}}" class="nav-link-custom py-2">Fonctionnalités</a>
        <a href="{{route('siteVitrine.tarifs')}}" class="nav-link-custom py-2">Tarifs</a>
        <a href="{{route('siteVitrine.contact')}}" class="nav-link-custom py-2">Contact</a>
        <hr>
        <a href="{{route('login')}}" class="btn-primary-custom text-center justify-content-center">Connexion</a>
      </div>
    </div>
  </div>
</nav>

<!-- ===== HERO SECTION ===== -->
<section class="hero-section" id="accueil">
  <div class="hero-grid-bg"></div>
  <div class="hero-orb hero-orb-1"></div>
  <div class="hero-orb hero-orb-2"></div>
  <div class="hero-orb hero-orb-3"></div>
  <div class="hero-particles" id="heroParticles"></div>

  <div class="container">
    <div class="row align-items-center g-5">

      <!-- Texte -->
      <div class="col-lg-6 hero-content">
        <h1 class="hero-title">
          Gérez vos <span class="accent">présences</span><br>
          avec précision et<br>
          <span class="accent">intelligence</span>
        </h1>

        <p class="hero-desc">
          Solution SaaS complète pour automatiser le suivi de présence de vos équipes.
          QR Code, mobile — zéro fraude, 100% fiable.
        </p>

        <div class="hero-cta-group">
          <a href="{{route('siteVitrine.tarifs')}}" class="btn-outline-custom" style="border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.8); padding: 0.85rem 2rem; font-size: 1rem;">
            Commencer maintenant <i class="fas fa-arrow-right"></i>
          </a>
        </div>

        <div class="hero-trust">
          <div class="trust-avatars">
            <div class="trust-avatar">AK</div>
            <div class="trust-avatar" style="background:#1a5a48;">MB</div>
            <div class="trust-avatar" style="background:#40a088;">SO</div>
            <div class="trust-avatar" style="background:#2d3a3e;">KD</div>
          </div>
          <div class="trust-text">
            <strong>+2 500 entreprises</strong> nous font confiance<br>
            au quotidien à travers l'Afrique
          </div>
        </div>
      </div>

      <!-- Dashboard visuel -->
      <div class="col-lg-6 hero-content">
        <div class="position-relative">
          <!-- Carte flottante 1 -->
          <div class="hero-float-card hero-float-card-1">
            <div class="float-card-icon"><i class="fas fa-user-check"></i></div>
            <div class="float-card-text">
              <div class="label">Présences aujourd'hui</div>
              <div class="value">247 / 260</div>
              <div class="sub">↑ 96% de taux</div>
            </div>
          </div>

          <!-- Carte flottante 2 -->
          <div class="hero-float-card hero-float-card-2">
            <div class="float-card-icon" style="background:#fef3e2;"><i class="fas fa-bolt" style="color:#EF9F27;"></i></div>
            <div class="float-card-text">
              <div class="label">Pointage en cours</div>
              <div class="value">En direct</div>
              <div class="sub" style="color:#EF9F27;">● Live</div>
            </div>
          </div>

          <!-- Dashboard mockup -->
          <div class="dashboard-mockup">
            <div class="mock-topbar">
              <div class="mock-dot mock-dot-r"></div>
              <div class="mock-dot mock-dot-y"></div>
              <div class="mock-dot mock-dot-g"></div>
              <div class="mock-title-bar"></div>
              <span style="font-size:9px;color:rgba(255,255,255,0.3);margin-left:6px;">EasyPoint Pro</span>
            </div>
            <div class="mock-body">
              <div class="mock-sidebar">
                <div class="mock-icon active"><i class="fas fa-th-large" style="font-size:13px;"></i></div>
                <div class="mock-icon"><i class="fas fa-users" style="font-size:13px;"></i></div>
                <div class="mock-icon"><i class="fas fa-clock" style="font-size:13px;"></i></div>
                <div class="mock-icon"><i class="fas fa-chart-bar" style="font-size:13px;"></i></div>
                <div class="mock-icon"><i class="fas fa-cog" style="font-size:13px;"></i></div>
              </div>
              <div class="mock-main">
                <div class="mock-kpi-row">
                  <div class="mock-kpi">
                    <div class="mock-kpi-label">Présents</div>
                    <div class="mock-kpi-value green">247</div>
                  </div>
                  <div class="mock-kpi">
                    <div class="mock-kpi-label">Absents</div>
                    <div class="mock-kpi-value white">13</div>
                  </div>
                  <div class="mock-kpi">
                    <div class="mock-kpi-label">En retard</div>
                    <div class="mock-kpi-value amber">8</div>
                  </div>
                </div>
                <div class="mock-chart-wrap">
                  <div class="mock-chart-header">
                    <div class="mock-chart-title">Flux hebdomadaire</div>
                    <div class="mock-chart-pill">Cette semaine</div>
                  </div>
                  <div class="mock-bars" id="heroBars">
                    <div class="mock-bar" style="height:65%;"></div>
                    <div class="mock-bar" style="height:85%;"></div>
                    <div class="mock-bar" style="height:72%;"></div>
                    <div class="mock-bar highlight" style="height:95%;"></div>
                    <div class="mock-bar" style="height:78%;"></div>
                    <div class="mock-bar" style="height:40%;"></div>
                    <div class="mock-bar" style="height:20%;"></div>
                  </div>
                  <div style="display:flex;justify-content:space-between;margin-top:5px;">
                    <span style="font-size:7px;color:rgba(255,255,255,0.3);">L</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.3);">M</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.3);">M</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.6);font-weight:700;">J</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.3);">V</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.2);">S</span>
                    <span style="font-size:7px;color:rgba(255,255,255,0.2);">D</span>
                  </div>
                </div>
                <div class="mock-list">
                  <div class="mock-list-item">
                    <div class="mock-avatar">AM</div>
                    <div class="mock-info"><div class="mock-name"></div><div class="mock-sub"></div></div>
                    <div class="mock-status in">Présent</div>
                  </div>
                  <div class="mock-list-item">
                    <div class="mock-avatar" style="background:rgba(64,160,136,0.3);">KB</div>
                    <div class="mock-info"><div class="mock-name"></div><div class="mock-sub"></div></div>
                    <div class="mock-status out">Absent</div>
                  </div>
                  <div class="mock-list-item">
                    <div class="mock-avatar" style="background:rgba(64,160,136,0.2);">DT</div>
                    <div class="mock-info"><div class="mock-name"></div><div class="mock-sub"></div></div>
                    <div class="mock-status in">Présent</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== STATS BAND ===== -->
<div class="stats-band">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-md-3 stat-item reveal">
        <div class="stat-number">2<span>500</span><span style="font-size:1.5rem;">+</span></div>
        <div class="stat-label">Entreprises clientes</div>
      </div>
      <div class="col-6 col-md-3 stat-item reveal">
        <div class="stat-number">98<span>%</span></div>
        <div class="stat-label">Taux de satisfaction</div>
      </div>
      <div class="col-6 col-md-3 stat-item reveal">
        <div class="stat-number">2<span>M</span><span style="font-size:1.5rem;">+</span></div>
        <div class="stat-label">Pointages traités</div>
      </div>
      <div class="col-6 col-md-3 stat-item reveal">
        <div class="stat-number">7<span>j</span>/7</div>
        <div class="stat-label">Support disponible</div>
      </div>
    </div>
  </div>
</div>

<!-- ===== SECTION "ILS NOUS FONT CONFIANCE" ===== -->
<section class="trust-section">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <p class="section-tag">Témoignages & Confiance</p>
      <h2 class="section-title">Ils nous font confiance</h2>
      <div class="section-underline"></div>
      <p class="text-muted mt-3">Découvrez ce que nos clients pensent de notre solution</p>
    </div>

    <div class="trust-carousel" id="trustCarousel">
      <div class="trust-track" id="trustTrack">
        <!-- Slide 1 - AgroBank -->
        <div class="trust-slide">
          <div class="trust-card">
            <div class="trust-logo-wrapper">
              <i class="fas fa-university"></i>
            </div>
            <h4>AgroBank</h4>
            <div class="stars mb-2">
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
            </div>
            <div class="quote">
              "EasyPoint a révolutionné notre gestion des présences. Plus besoin de pointeuses obsolètes, tout est dématérialisé et accessible en temps réel."
            </div>
            <div class="author">Jean Kouassi</div>
            <div class="position">DRH Groupe AgroBank</div>
            <div class="trust-stats">
              <div class="trust-stat">
                <div class="number">1 200+</div>
                <div class="label">Utilisateurs</div>
              </div>
              <div class="trust-stat">
                <div class="number">99%</div>
                <div class="label">Précision</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 - Telecom TG -->
        <div class="trust-slide">
          <div class="trust-card">
            <div class="trust-logo-wrapper">
              <i class="fas fa-tower-cell"></i>
            </div>
            <h4>Telecom TG</h4>
            <div class="stars mb-2">
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star-half-alt" style="color: #FFD700;"></i>
            </div>
            <div class="quote">
              "La gestion des équipes terrain était un vrai casse-tête. Avec EasyPoint, nous suivons nos techniciens en mobilité simplement et efficacement."
            </div>
            <div class="author">Awa Diop</div>
            <div class="position">Directrice Technique</div>
            <div class="trust-stats">
              <div class="trust-stat">
                <div class="number">850+</div>
                <div class="label">Employés</div>
              </div>
              <div class="trust-stat">
                <div class="number">-70%</div>
                <div class="label">Temps admin</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 3 - Medisanté -->
        <div class="trust-slide">
          <div class="trust-card">
            <div class="trust-logo-wrapper">
              <i class="fas fa-hospital-user"></i>
            </div>
            <h4>Medisanté</h4>
            <div class="stars mb-2">
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
            </div>
            <div class="quote">
              "Pour notre clinique, la fiabilité des pointages est cruciale. EasyPoint nous offre une sécurité et une traçabilité exemplaires."
            </div>
            <div class="author">Dr. Kodjo Mensah</div>
            <div class="position">Directeur Médical</div>
            <div class="trust-stats">
              <div class="trust-stat">
                <div class="number">450+</div>
                <div class="label">Personnel</div>
              </div>
              <div class="trust-stat">
                <div class="number">100%</div>
                <div class="label">Sécurisé</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 4 - EduCentre -->
        <div class="trust-slide">
          <div class="trust-card">
            <div class="trust-logo-wrapper">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>EduCentre</h4>
            <div class="stars mb-2">
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
            </div>
            <div class="quote">
              "Le suivi des présences des enseignants et étudiants est devenu transparent. L'interface est intuitive et très appréciée par nos équipes."
            </div>
            <div class="author">Claire Adjovi</div>
            <div class="position">Responsable Académique</div>
            <div class="trust-stats">
              <div class="trust-stat">
                <div class="number">2 300+</div>
                <div class="label">Étudiants</div>
              </div>
              <div class="trust-stat">
                <div class="number">+95%</div>
                <div class="label">Assiduité</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 5 - LogiFreight -->
        <div class="trust-slide">
          <div class="trust-card">
            <div class="trust-logo-wrapper">
              <i class="fas fa-truck-fast"></i>
            </div>
            <h4>LogiFreight</h4>
            <div class="stars mb-2">
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
              <i class="fas fa-star" style="color: #FFD700;"></i>
            </div>
            <div class="quote">
              "La gestion des pointages sur nos différents dépôts est désormais centralisée. EasyPoint nous fait gagner un temps précieux au quotidien."
            </div>
            <div class="author">Marc Zinsou</div>
            <div class="position">COO LogiFreight</div>
            <div class="trust-stats">
              <div class="trust-stat">
                <div class="number">25+</div>
                <div class="label">Sites</div>
              </div>
              <div class="trust-stat">
                <div class="number">-85%</div>
                <div class="label">Erreurs</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="trust-nav">
      <div class="trust-nav-btn" id="prevSlide">
        <i class="fas fa-chevron-left"></i>
      </div>
      <div class="trust-nav-btn" id="nextSlide">
        <i class="fas fa-chevron-right"></i>
      </div>
    </div>
    <div class="trust-dots" id="trustDots"></div>
  </div>
</section>

<!-- ===== SERVICES ===== -->
<section class="py-5" id="services" style="background: var(--blanc);">
  <div class="container py-4">
    <div class="text-center mb-5 reveal">
      <p class="section-tag">Ce que nous faisons</p>
      <h2 class="section-title">Nos Services & Accompagnements</h2>
      <div class="section-underline"></div>
    </div>
    <div class="row g-4">
        <div class="col-md-4 reveal">
            <div class="service-card">
            <div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
            <h5 class="fw-bold mb-2">Application Mobile</h5>
            <p class="text-muted small">Application iOS & Android pour pointer à distance et suivre son planning en temps réel.</p>
            <div class="mt-3"><span class="fw-bold" style="color: var(--vert-principal);">Inclus</span></div>
            </div>
        </div>

        <div class="col-md-4 reveal">
            <div class="service-card">
            <div class="service-icon"><i class="fas fa-qrcode"></i></div>
            <h5 class="fw-bold mb-2">Pointage QR Code</h5>
            <p class="text-muted small">Pointage rapide par QR code, idéal pour les événements et visites temporaires.</p>
            <div class="mt-3"><span class="fw-bold" style="color: var(--vert-principal);"></span></div>
            </div>
        </div>

        <div class="col-md-4 reveal">
            <div class="service-card">
            <div class="service-icon"><i class="fas fa-fingerprint"></i></div>
            <h5 class="fw-bold mb-2">Pointage Biométrique</h5>
            <p class="text-muted small">Système de pointage par empreinte digitale ultra-précis et sécurisé.</p>
            <div class="mt-3"><span class="fw-bold" style="color: var(--vert-principal);">Sur Mesure</span></div>
            </div>
        </div>

    </div>
  </div>
</section>

<!-- ===== POURQUOI NOUS ===== -->
<section class="why-us py-5">
  <div class="container py-4">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 reveal">
        <p class="section-tag">Notre valeur ajoutée</p>
        <h2 class="section-title">Pourquoi Choisir EasyPoint ?</h2>
        <div class="atout-item">
          <div class="atout-check"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
          <span class="fw-semibold">Support technique expert disponible 7j/7</span>
        </div>
        <div class="atout-item">
          <div class="atout-check"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
          <span class="fw-semibold">Protection stricte des données et conformité RGPD</span>
        </div>
        <div class="atout-item">
          <div class="atout-check"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
          <span class="fw-semibold">+2500 structures et entreprises satisfaites</span>
        </div>
        <a href="#" class="btn-primary-custom mt-4">En savoir plus <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
      <div class="col-lg-6 reveal">
        <div style="background: var(--blanc); border: 1px solid var(--gris-clair); border-radius: 1.5rem; padding: 2rem; box-shadow: var(--shadow-lg);">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:48px;height:48px;background:var(--vert-clair);border-radius:12px;display:flex;align-items:center;justify-content:center;">
              <i class="fas fa-chart-line" style="color:var(--vert-principal);font-size:1.4rem;"></i>
            </div>
            <div>
              <div class="fw-bold" style="color:var(--gris-fonce);">Tableau de bord analytique</div>
              <div class="text-muted" style="font-size:0.8rem;">Données en temps réel</div>
            </div>
          </div>
          <!-- Mini bar chart -->
          <div style="display:flex;align-items:flex-end;gap:6px;height:90px;margin-bottom:12px;">
            <div style="flex:1;background:var(--vert-clair);border-radius:4px 4px 0 0;height:60%;"></div>
            <div style="flex:1;background:var(--vert-principal);border-radius:4px 4px 0 0;height:85%;"></div>
            <div style="flex:1;background:var(--vert-clair);border-radius:4px 4px 0 0;height:50%;"></div>
            <div style="flex:1;background:var(--vert-accent);border-radius:4px 4px 0 0;height:95%;"></div>
            <div style="flex:1;background:var(--vert-clair);border-radius:4px 4px 0 0;height:70%;"></div>
            <div style="flex:1;background:var(--vert-principal);border-radius:4px 4px 0 0;height:80%;"></div>
            <div style="flex:1;background:var(--vert-clair);border-radius:4px 4px 0 0;height:45%;"></div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <span class="badge p-2" style="background: var(--vert-clair); color: var(--vert-principal); font-size:0.75rem;">98% de satisfaction</span>
            <span class="badge p-2" style="background: var(--vert-clair); color: var(--vert-principal); font-size:0.75rem;">+2M de pointages</span>
            <span class="badge p-2" style="background: var(--vert-clair); color: var(--vert-principal); font-size:0.75rem;">0 fraude détectée</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== SOLUTIONS ===== -->
<section class="py-5" id="solutions" style="background: var(--gres-tres-clair);">
  <div class="container py-4">
    <div class="text-center mb-5 reveal">
      <p class="section-tag">Innovation & Performance</p>
      <h2 class="section-title">Nos Solutions Métiers</h2>
      <div class="section-underline"></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4 reveal">
        <div class="solution-card">
          <div class="solution-image">
            <img src="https://www.bizneo.com/blog/wp-content/uploads/2024/08/Pointage-des-heures-de-travail-1024x575.webp" alt="Pointage biométrique">
            <div class="solution-badge">Premium</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold mb-1">Pointage Physique</h5>
            <p class="fw-semibold small mb-2" style="color: var(--vert-principal);">Sécurité maximale</p>
            <p class="text-muted small">Bornes connectées et terminaux biométriques intelligents pour vos locaux physiques.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 reveal">
        <div class="solution-card">
          <div class="solution-image">
            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070" alt="Pointage mobile">
            <div class="solution-badge">Mobile First</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold mb-1">Mobilité & Terrain</h5>
            <p class="fw-semibold small mb-2" style="color: var(--vert-principal);">Géolocalisé</p>
            <p class="text-muted small">Idéal pour le télétravail, les chantiers et les collaborateurs en déplacement constant.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 reveal">
        <div class="solution-card">
          <div class="solution-image">
            <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=2070" alt="Dashboard analytics">
            <div class="solution-badge">Data Driven</div>
          </div>
          <div class="p-3">
            <h5 class="fw-bold mb-1">Dashboard Analytics</h5>
            <p class="fw-semibold small mb-2" style="color: var(--vert-principal);">Reporting Intelligent</p>
            <p class="text-muted small">Centralisation des données, fiches d'heures automatisées et export RH immédiat.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== DEMO ===== -->
<section class="py-5 bg-white" id="demo">
  <div class="container py-4">
    <div class="text-center mb-5 reveal">
      <p class="section-tag">Aperçu en direct</p>
      <h2 class="section-title">Espace Démo Applicatif</h2>
      <div class="section-underline"></div>
    </div>
    <div class="row g-4 align-items-center">
      <div class="col-lg-4 reveal">
        <h4 class="fw-bold mb-3">Prenez le contrôle de l'interface</h4>
        <p class="text-muted small">Cliquez sur les options du simulateur pour observer l'ajustement dynamique de nos rapports et statistiques hebdomadaires.</p>
        <div class="list-group list-group-flush gap-2 mt-3">
          <button class="btn btn-outline-custom text-start active w-100" onclick="updateDemoChart([85, 78, 92, 88], 'Semaine 1')">
            <i class="fas fa-calendar-week me-2" style="color: var(--vert-principal);"></i> Semaine 1
          </button>
          <button class="btn btn-outline-custom text-start w-100" onclick="updateDemoChart([72, 85, 79, 91], 'Semaine 2')">
            <i class="fas fa-calendar-week me-2" style="color: var(--vert-principal);"></i> Semaine 2
          </button>
          <button class="btn btn-outline-custom text-start w-100" onclick="updateDemoChart([90, 82, 88, 94], 'Semaine 3')">
            <i class="fas fa-calendar-week me-2" style="color: var(--vert-principal);"></i> Semaine 3
          </button>
        </div>
      </div>
      <div class="col-lg-8 reveal">
        <div class="demo-window">
          <div class="demo-topbar">
            <div class="demo-dot bg-danger"></div>
            <div class="demo-dot bg-warning"></div>
            <div class="demo-dot bg-success"></div>
            <span class="text-white-50 small ms-3">EasyPoint Pro — Simulation de flux</span>
          </div>
          <div class="row g-0">
            <div class="col-3 demo-sidebar">
              <a href="javascript:void(0)" class="sidebar-link active"><i class="fas fa-th-large"></i> <span class="d-none d-md-inline">Vue Générale</span></a>
              <a href="javascript:void(0)" class="sidebar-link"><i class="fas fa-users"></i> <span class="d-none d-md-inline">Employés</span></a>
              <a href="javascript:void(0)" class="sidebar-link"><i class="fas fa-clock"></i> <span class="d-none d-md-inline">Heures</span></a>
              <a href="javascript:void(0)" class="sidebar-link"><i class="fas fa-cog"></i> <span class="d-none d-md-inline">Configuration</span></a>
            </div>
            <div class="col-9 p-4">
              <h6 class="fw-bold mb-3" style="color: var(--gris-fonce);" id="demo-title">Flux Global — Semaine 1</h6>
              <div class="chart-bar-container">
                <div class="chart-bar-item" id="bar-1" style="height: 85%;"></div>
                <div class="chart-bar-item" id="bar-2" style="height: 78%;"></div>
                <div class="chart-bar-item" id="bar-3" style="height: 92%;"></div>
                <div class="chart-bar-item" id="bar-4" style="height: 88%;"></div>
              </div>
              <div class="d-flex justify-content-between text-muted pt-2 fw-medium" style="font-size:0.75rem;">
                <span>Lundi</span>
                <span>Mardi</span>
                <span>Mercredi</span>
                <span>Jeudi</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="container" id="contact">
  <div class="cta-section text-center reveal">
    <h2 class="text-white mb-3" style="font-size: clamp(1.5rem, 3vw, 2rem);">Prêt à moderniser votre gestion RH ?</h2>
    <p class="text-white-50 mb-4">Rejoignez plus de 2 500 entreprises qui nous font confiance au quotidien</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{route('siteVitrine.tarifs')}}" class="btn-outline-custom" style="border-color: white; color: white;"><i class="fas fa-gem me-2"></i>Commencer maintenant</a>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer-modern" >
  <div class="container" >
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a href="#" class="footer-logo">Easy<span>Point</span></a>
        <p class="footer-description mt-3">{{ $footer->description ?? 'La solution de suivi de présence intelligente pour les structures exigeantes. Performance, simplicité et rigueur.' }}</p>
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
            <h5 class="footer-title">Contactez-nous</h5>
            <div class="footer-contact-info">
            @if($footer->email)
                <p><i class="fas fa-envelope"></i> {{ $footer->email }}</p>
            @endif
            @if($footer->phone)
                <p><i class="fas fa-phone"></i> {{ $footer->phone }}</p>
            @endif
            @if($footer->address)
                <p><i class="fas fa-map-marker-alt"></i> {{ $footer->address }}</p>
            @endif
            </div>
            <p class="small-text mt-2" style="font-size: 12px;">Recevez nos offres et actualités</p>
        </div>

    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
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
      {{-- <div class="footer-copyright">Propulsé avec <i class="fas fa-heart footer-heart"></i> pour les équipes modernes.</div> --}}
    </div>
  </div>
</footer>

<script>
  /* ---- Particules hero ---- */
  (function() {
    const container = document.getElementById('heroParticles');
    if (!container) return;
    for (let i = 0; i < 25; i++) {
      const p = document.createElement('div');
      p.className = 'particle';
      p.style.cssText = `
        left: ${Math.random() * 100}%;
        width: ${Math.random() * 3 + 2}px;
        height: ${Math.random() * 3 + 2}px;
        animation-duration: ${Math.random() * 15 + 10}s;
        animation-delay: ${Math.random() * -20}s;
        opacity: ${Math.random() * 0.6 + 0.2};
      `;
      container.appendChild(p);
    }
  })();

  /* ---- Carousel "Ils nous font confiance" ---- */
  const track = document.getElementById('trustTrack');
  const slides = document.querySelectorAll('.trust-slide');
  const prevBtn = document.getElementById('prevSlide');
  const nextBtn = document.getElementById('nextSlide');
  const dotsContainer = document.getElementById('trustDots');

  let currentIndex = 0;
  let slidesPerView = window.innerWidth >= 768 ? 3 : 1;
  let totalSlides = slides.length;

  function updateCarousel() {
    const slideWidth = slides[0]?.offsetWidth || 0;
    const gap = 30;
    const offset = -currentIndex * (slideWidth + 30);
    if (track) {
      track.style.transform = `translateX(${offset}px)`;
    }
    updateDots();
  }

  function updateDots() {
    if (!dotsContainer) return;
    const dotCount = Math.ceil(totalSlides / slidesPerView);
    dotsContainer.innerHTML = '';
    for (let i = 0; i < dotCount; i++) {
      const dot = document.createElement('div');
      dot.className = 'trust-dot';
      if (i === Math.floor(currentIndex / slidesPerView)) dot.classList.add('active');
      dot.addEventListener('click', () => {
        currentIndex = i * slidesPerView;
        if (currentIndex > totalSlides - slidesPerView) currentIndex = totalSlides - slidesPerView;
        if (currentIndex < 0) currentIndex = 0;
        updateCarousel();
      });
      dotsContainer.appendChild(dot);
    }
  }

  function nextSlide() {
    if (currentIndex + slidesPerView < totalSlides) {
      currentIndex += slidesPerView;
    } else {
      currentIndex = totalSlides - slidesPerView;
    }
    if (currentIndex < 0) currentIndex = 0;
    updateCarousel();
  }

  function prevSlide() {
    currentIndex -= slidesPerView;
    if (currentIndex < 0) currentIndex = 0;
    updateCarousel();
  }

  if (prevBtn && nextBtn) {
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
  }

  window.addEventListener('resize', () => {
    slidesPerView = window.innerWidth >= 768 ? 3 : 1;
    updateCarousel();
  });

  setTimeout(() => {
    updateCarousel();
  }, 100);

  /* ---- Demo chart ---- */
  window.updateDemoChart = function(heights, weekName) {
    document.getElementById('demo-title').innerText = `Flux Global — ${weekName}`;
    heights.forEach((h, i) => {
      const el = document.getElementById(`bar-${i+1}`);
      if (el) el.style.height = `${h}%`;
    });
    document.querySelectorAll('#demo .btn-outline-custom').forEach(btn => {
      btn.classList.toggle('active', btn.innerText.includes(weekName));
    });
  };

  /* ---- Mobile menu ---- */
  const mobileBtn  = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
    });
  }
  document.querySelectorAll('#mobileMenu a').forEach(link => {
    link.addEventListener('click', () => { mobileMenu.style.display = 'none'; });
  });

  /* ---- Scroll effects ---- */
  const reveals = document.querySelectorAll('.reveal');
  function checkReveal() {
    reveals.forEach(rev => {
      if (rev.getBoundingClientRect().top < window.innerHeight - 80) {
        rev.classList.add('visible');
      }
    });
  }
  window.addEventListener('scroll', () => {
    const navbar = document.getElementById('topnav');
    if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 50);
    checkReveal();
  }, { passive: true });

  setTimeout(checkReveal, 300);

  /* ---- Hero bars animation ---- */
  const heroBars = document.querySelectorAll('#heroBars .mock-bar');
  let barIndex = 0;
  setInterval(() => {
    heroBars.forEach(b => b.classList.remove('highlight'));
    heroBars[barIndex % heroBars.length].classList.add('highlight');
    barIndex++;
  }, 1800);
</script>

</body>
</html>
