<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIPUS – Sistem Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet" />
    <style>
        :root {
            --navy:       #0D1F2D;
            --accent:     #4A9DB5;
            --accent-dim: rgba(74,157,181,0.55);
            --white:      #FFFFFF;
            --white-70:   rgba(255,255,255,0.70);
            --white-50:   rgba(255,255,255,0.50);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }

        /* ── HERO ── */
        .hero {
            position: relative;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── BACKGROUND ── */
        .hero__bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .hero__bg-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 30%;
            display: block;
            filter: saturate(0.7) brightness(0.85);
        }
        .hero__overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(105deg,
                    rgba(10,22,36,0.92) 0%,
                    rgba(10,22,36,0.75) 38%,
                    rgba(10,22,36,0.30) 65%,
                    rgba(10,22,36,0.10) 100%
                ),
                linear-gradient(to top,
                    rgba(8,18,28,0.70) 0%,
                    transparent 50%
                );
        }
        .hero__top-fade {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 120px;
            background: linear-gradient(to bottom, rgba(8,18,28,0.65) 0%, transparent 100%);
        }

        /* ── NAVBAR ── */
        .navbar {
            position: relative;
            z-index: 10;
            padding: 22px 36px;
            display: flex;
            align-items: center;
        }
        .navbar__brand { display: flex; align-items: center; gap: 12px; }
        .navbar__brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: var(--white);
            letter-spacing: .04em;
            line-height: 1;
        }
        .navbar__brand-divider {
            width: 1.5px;
            height: 32px;
            background: rgba(255,255,255,.35);
            border-radius: 2px;
        }
        .navbar__brand-meta { display: flex; flex-direction: column; gap: 1px; }
        .navbar__brand-sub {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: var(--white);
            letter-spacing: .01em;
            line-height: 1.2;
        }
        .navbar__brand-group {
            font-size: 10.5px;
            color: var(--white-50);
            letter-spacing: .02em;
        }

        /* ── HERO CONTENT ── */
        .hero__content {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 120px 100px 80px;
        }
        .hero__text-block {
            max-width: 620px;
            animation: heroFadeUp .9s cubic-bezier(.22,1,.36,1) both;
        }
        .hero__heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: clamp(38px, 5.5vw, 64px);
            font-weight: 900;
            color: var(--white);
            line-height: 1.12;
            letter-spacing: -.01em;
            margin-bottom: 14px;
            text-shadow: 0 2px 24px rgba(0,0,0,.35);
        }
        .hero__subheading {
            font-size: clamp(14px, 1.4vw, 17px);
            color: var(--white-70);
            line-height: 1.65;
            max-width: 480px;
            margin-bottom: 48px;
            text-shadow: 0 1px 8px rgba(0,0,0,.4);
            animation: heroFadeUp .9s .15s cubic-bezier(.22,1,.36,1) both;
        }

        /* ── BUTTON ── */
        .btn-access {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--accent);
            border: 2px solid var(--accent-dim);
            border-radius: 12px;
            padding: 15px 38px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .02em;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(74,157,181,0.10);
            transition: background .25s, border-color .25s, color .25s, transform .2s, box-shadow .25s;
            animation: heroFadeUp .9s .25s cubic-bezier(.22,1,.36,1) both;
            cursor: pointer;
        }
        .btn-access:hover {
            background: rgba(74,157,181,0.22);
            border-color: var(--accent);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(74,157,181,0.28);
        }
        .btn-access:active { transform: translateY(-1px); }

        /* ── WOSH LOGO ── */
        .hero__wosh {
            height: 28px;
            width: auto;
            margin-bottom: 22px;
            display: block;
            opacity: 0.85;
            animation: heroFadeUp .9s .1s cubic-bezier(.22,1,.36,1) both;
        }

        /* ── FLOATING PARTICLES ── */
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            z-index: 2;
            animation: floatUp linear infinite;
            pointer-events: none;
        }
        .particle:nth-child(1)  { width: 6px;  height: 6px;  left: 15%;  background: rgba(74,157,181,0.6);  animation-duration: 8s;  animation-delay: 0s;   bottom: -20px; }
        .particle:nth-child(2)  { width: 4px;  height: 4px;  left: 30%;  background: rgba(31,207,142,0.5);  animation-duration: 10s; animation-delay: 1.5s; bottom: -20px; }
        .particle:nth-child(3)  { width: 8px;  height: 8px;  left: 50%;  background: rgba(74,157,181,0.4);  animation-duration: 9s;  animation-delay: 3s;   bottom: -20px; }
        .particle:nth-child(4)  { width: 5px;  height: 5px;  left: 70%;  background: rgba(31,207,142,0.6);  animation-duration: 11s; animation-delay: 0.5s; bottom: -20px; }
        .particle:nth-child(5)  { width: 3px;  height: 3px;  left: 85%;  background: rgba(74,157,181,0.5);  animation-duration: 7s;  animation-delay: 2s;   bottom: -20px; }
        .particle:nth-child(6)  { width: 6px;  height: 6px;  left: 22%;  background: rgba(31,207,142,0.4);  animation-duration: 12s; animation-delay: 4s;   bottom: -20px; }
        .particle:nth-child(7)  { width: 4px;  height: 4px;  left: 60%;  background: rgba(74,157,181,0.5);  animation-duration: 9s;  animation-delay: 1s;   bottom: -20px; }
        .particle:nth-child(8)  { width: 7px;  height: 7px;  left: 78%;  background: rgba(31,207,142,0.3);  animation-duration: 13s; animation-delay: 2.5s; bottom: -20px; }
        .particle:nth-child(9)  { width: 5px;  height: 5px;  left: 42%;  background: rgba(74,157,181,0.4);  animation-duration: 11s; animation-delay: 3.5s; bottom: -20px; }
        .particle:nth-child(10) { width: 3px;  height: 3px;  left: 92%;  background: rgba(31,207,142,0.5);  animation-duration: 8s;  animation-delay: 5s;   bottom: -20px; }

        @keyframes floatUp {
            0%   { opacity: 0;   transform: translateY(0)      scale(0.5); }
            10%  { opacity: 0.8; }
            90%  { opacity: 0.6; }
            100% { opacity: 0;   transform: translateY(-110vh) scale(1.2); }
        }

        /* ── ANIMASI UMUM ── */
        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero__content { padding: 0 40px 80px; }
        }
        @media (max-width: 600px) {
            .navbar { padding: 18px 20px; }
            .hero__content { padding: 0 24px 60px; align-items: flex-end; }
            .hero__heading { font-size: 34px; }
            .hero__subheading { font-size: 14px; }
            .navbar__brand-name { font-size: 22px; }
            .btn-access { padding: 13px 28px; font-size: 14px; }
        }
    </style>
</head>
<body>
<section class="hero">

    {{-- ── Floating Particles ── --}}
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>
    <span class="particle"></span>

    {{-- ── Background ── --}}
    <div class="hero__bg">
        <img class="hero__bg-img"
             src="{{ asset('ui_auth/Background.jpg') }}"
             alt="Library interior" />
        <div class="hero__overlay"></div>
        <div class="hero__top-fade"></div>
    </div>

    {{-- ── Navbar ── --}}
    <header class="navbar">
        <div class="navbar__brand">
            <img src="{{ asset('ui_auth/logo.svg') }}"
                 alt="SIPUS"
                 style="height: 36px; width: auto;">
            <div class="navbar__brand-divider"></div>
            <div class="navbar__brand-meta">
                <p class="navbar__brand-sub">Sistem Perpustakaan</p>
                <p class="navbar__brand-group">Kelompok 2</p>
            </div>
        </div>
    </header>

    {{-- ── Hero Content ── --}}
    <div class="hero__content">
        <div class="hero__text-block">

            <h1 class="hero__heading">
                Digital Library<br />
                Borrowing Platform
            </h1>

            {{-- Wosh logo sebagai pengganti underline --}}
            <img src="{{ asset('ui_auth/wosh-logo.svg') }}"
                 alt=""
                 class="hero__wosh">

            <p class="hero__subheading">
                A modern solution for efficient library services—simplifying<br />
                book loans, returns, and access to knowledge.
            </p>

            {{-- Button ke halaman login --}}
            <a href="{{ route('login') }}" class="btn-access">
                Access Library
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>

        </div>
    </div>

</section>
</body>
</html>