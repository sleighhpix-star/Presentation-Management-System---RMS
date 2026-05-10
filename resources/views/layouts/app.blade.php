<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Research PMS')</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
    

    :root {
        --ink:        #0a0f1e;
        --ink-mid:    #131929;
        --ink-soft:   #1e2a42;
        --teal:       #0d9488;
        --teal-light: #14b8a6;
        --teal-glow:  rgba(13,148,136,.18);
        --amber:      #f59e0b;
        --amber-soft: #fef3c7;
        --rose:       #f43f5e;
        --violet:     #8b5cf6;
        --surface:    #f0f4f8;
        --card:       #ffffff;
        --border:     #e2e8f0;
        --muted:      #64748b;
        --text:       #0f172a;
        --sb-w:       64px;
        --sb-open:    252px;
        --topbar-h:   60px;
        --radius:     14px;
        --t:          .26s cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        background: var(--surface);
        font-family: 'Plus Jakarta Sans', 'DM Sans', sans-serif;
        color: var(--text);
        overflow-x: hidden;
    }

    /* ══════════════════════════════════════════
       SIDEBAR
    ══════════════════════════════════════════ */
    #sidebar {
        position: fixed; top: 0; left: 0; height: 100vh;
        width: var(--sb-w);
        background: var(--ink);
        display: flex; flex-direction: column;
        z-index: 1050;
        overflow: hidden;
        transition: width var(--t);
        border-right: 1px solid rgba(255,255,255,.04);
    }
    #sidebar::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--teal), var(--amber), var(--rose));
        z-index: 10;
    }
    #sidebar.expanded { width: var(--sb-open); }

    /* Toggle button */
    .sb-toggle {
        display: flex; align-items: center;
        width: 100%; height: var(--topbar-h);
        background: none; border: none; cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,.05);
        padding: 0; flex-shrink: 0;
        transition: background var(--t);
        overflow: hidden;
    }
    .sb-toggle:hover { background: rgba(255,255,255,.04); }

    .sb-toggle .t-icon {
        width: var(--sb-w); min-width: var(--sb-w);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .sb-toggle .t-gem {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, var(--teal), var(--teal-light));
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 18px var(--teal-glow);
        transition: box-shadow var(--t);
    }
    .sb-toggle:hover .t-gem { box-shadow: 0 0 28px rgba(13,148,136,.35); }
    .sb-toggle .t-gem i { color: #fff; font-size: 16px; }

    .sb-toggle .t-text {
        opacity: 0; max-width: 0; overflow: hidden; white-space: nowrap;
        transition: opacity .15s, max-width var(--t);
        text-align: left;
    }
    #sidebar.expanded .sb-toggle .t-text { opacity: 1; max-width: 200px; }
    .sb-toggle .t-name { color: #fff; font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; display: block; letter-spacing: .3px; }
    .sb-toggle .t-sub  { color: rgba(255,255,255,.35); font-size: 10px; display: block; margin-top: 1px; }

    /* Nav */
    .sb-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 8px 0; }
    .sb-nav::-webkit-scrollbar { width: 3px; }
    .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 3px; }

    .sb-section {
        height: 0; opacity: 0; overflow: hidden;
        font-family: 'Outfit', sans-serif; font-size: 9px; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase;
        color: rgba(255,255,255,.2); white-space: nowrap; padding: 0 16px;
        transition: height .22s, opacity .22s, padding .22s;
    }
    #sidebar.expanded .sb-section { height: 34px; opacity: 1; padding: 16px 16px 4px; }

    .sb-link {
        display: flex; align-items: center;
        height: 46px; width: 100%;
        color: rgba(255,255,255,.5);
        text-decoration: none;
        border-left: 2px solid transparent;
        overflow: hidden; white-space: nowrap;
        transition: background var(--t), border-color var(--t), color var(--t);
        position: relative;
    }
    .sb-link .lk-icon {
        width: var(--sb-w); min-width: var(--sb-w);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
        transition: color var(--t);
    }
    .sb-link .lk-label {
        font-size: 13px; font-weight: 500;
        opacity: 0; max-width: 0; overflow: hidden;
        transition: opacity .15s, max-width var(--t);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    #sidebar.expanded .sb-link .lk-label { opacity: 1; max-width: 180px; }

    .sb-link:hover { color: #fff; background: rgba(255,255,255,.05); border-left-color: rgba(13,148,136,.4); }
    .sb-link.active {
        color: #fff;
        background: linear-gradient(90deg, rgba(13,148,136,.18) 0%, transparent 100%);
        border-left-color: var(--teal);
    }
    .sb-link.active .lk-icon { color: var(--teal-light); }
    .sb-link.active .lk-label { font-weight: 600; }

    /* Tooltip */
    .sb-link .lk-tip {
        position: absolute; left: calc(var(--sb-w) + 10px); top: 50%;
        transform: translateY(-50%);
        background: var(--ink-soft); color: #fff;
        font-size: 11.5px; font-weight: 600; white-space: nowrap;
        padding: 5px 11px; border-radius: 8px;
        pointer-events: none; opacity: 0;
        transition: opacity .15s; z-index: 9999;
        box-shadow: 0 4px 14px rgba(0,0,0,.4);
        border: 1px solid rgba(255,255,255,.08);
    }
    .sb-link .lk-tip::before {
        content: ''; position: absolute; left: -5px; top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent; border-right-color: var(--ink-soft); border-left: 0;
    }
    #sidebar:not(.expanded) .sb-link:hover .lk-tip { opacity: 1; }

    /* Footer */
    .sb-footer { border-top: 1px solid rgba(255,255,255,.05); flex-shrink: 0; }
    .sb-user-row {
        display: flex; align-items: center; gap: 9px;
        height: 0; opacity: 0; overflow: hidden; padding: 0 16px; white-space: nowrap;
        transition: height .22s, opacity .22s, padding .22s;
    }
    #sidebar.expanded .sb-user-row { height: 40px; opacity: 1; padding: 10px 16px 2px; }
    .sb-user-row i    { color: rgba(255,255,255,.3); font-size: 14px; flex-shrink: 0; }
    .sb-user-row span { color: rgba(255,255,255,.4); font-size: 11.5px; overflow: hidden; text-overflow: ellipsis; }

    .sb-logout {
        display: flex; align-items: center; height: 48px; width: 100%;
        background: none; border: none; cursor: pointer;
        color: rgba(255,255,255,.4); font-family: 'Plus Jakarta Sans', sans-serif;
        overflow: hidden; white-space: nowrap;
        transition: background var(--t), color var(--t);
        padding: 0; border-left: 2px solid transparent; position: relative;
    }
    .sb-logout:hover { background: rgba(244,63,94,.08); color: #fda4af; border-left-color: var(--rose); }
    .sb-logout .lk-icon  { width: var(--sb-w); min-width: var(--sb-w); display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .sb-logout .lk-label { font-size:13px; font-weight:500; opacity:0; max-width:0; overflow:hidden; transition: opacity .15s, max-width var(--t); }
    #sidebar.expanded .sb-logout .lk-label { opacity:1; max-width:180px; }
    .sb-logout .lk-tip {
        position: absolute; left: calc(var(--sb-w) + 10px); top: 50%; transform: translateY(-50%);
        background: var(--ink-soft); color: #fff; font-size: 11.5px; font-weight: 600;
        white-space: nowrap; padding: 5px 11px; border-radius: 8px;
        pointer-events: none; opacity: 0; transition: opacity .15s; z-index: 9999;
        box-shadow: 0 4px 14px rgba(0,0,0,.4); border: 1px solid rgba(255,255,255,.08);
    }
    .sb-logout .lk-tip::before {
        content:''; position:absolute; left:-5px; top:50%; transform:translateY(-50%);
        border:5px solid transparent; border-right-color:var(--ink-soft); border-left:0;
    }
    #sidebar:not(.expanded) .sb-logout:hover .lk-tip { opacity: 1; }

    /* ══════════════════════════════════════════
       MAIN
    ══════════════════════════════════════════ */
    #main {
        margin-left: var(--sb-w);
        min-height: 100vh; display: flex; flex-direction: column;
        transition: margin-left var(--t);
        min-width: 0;
    }
    #sidebar.expanded ~ #main { margin-left: var(--sb-open); }

    /* Topbar */
    .topbar {
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        height: var(--topbar-h);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px;
        border-bottom: 1px solid var(--border);
        position: sticky; top: 0; z-index: 900;
        box-shadow: 0 1px 0 rgba(0,0,0,.04), 0 4px 16px rgba(0,0,0,.04);
        flex-shrink: 0;
    }
    .topbar-title {
        font-family: 'Outfit', sans-serif;
        font-size: 16px; font-weight: 700; color: var(--ink);
        letter-spacing: -.2px;
    }
    .topbar-badge {
        display: flex; align-items: center; gap: 7px;
        background: var(--ink); color: #fff;
        font-size: 11.5px; font-weight: 600;
        padding: 6px 14px; border-radius: 20px;
        font-family: 'Outfit', sans-serif; letter-spacing: .3px;
    }
    .topbar-badge .dot-pulse {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--teal-light);
        box-shadow: 0 0 0 0 rgba(20,184,166,.4);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%   { box-shadow: 0 0 0 0 rgba(20,184,166,.4); }
        70%  { box-shadow: 0 0 0 7px rgba(20,184,166,0); }
        100% { box-shadow: 0 0 0 0 rgba(20,184,166,0); }
    }

    .content { padding: 24px 28px; flex: 1; min-width: 0; }

    /* Prevent Bootstrap Icons in pagination from blowing up */
    .pagination { flex-wrap: wrap; }
    .pagination .page-link svg,
    .pagination .page-link span[aria-hidden] { font-size: 13px !important; width: 16px !important; height: 16px !important; }
    nav[aria-label="pagination"] { overflow: hidden; }

    /* ══════════════════════════════════════════
       CARDS
    ══════════════════════════════════════════ */
    .card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.04);
        transition: box-shadow var(--t);
    }
    .card-header {
        background: var(--card); border-bottom: 1px solid var(--border);
        border-radius: var(--radius) var(--radius) 0 0 !important;
        font-weight: 700; font-size: 13.5px; color: var(--ink);
        font-family: 'Outfit', sans-serif; letter-spacing: -.1px;
    }

    /* ══════════════════════════════════════════
       STAT CARDS
    ══════════════════════════════════════════ */
    .stat-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px;
        display: flex; align-items: center; gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: box-shadow var(--t), transform var(--t);
        text-decoration: none; color: inherit;
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
        transform: scaleX(0); transform-origin: left;
        transition: transform var(--t);
    }
    .stat-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.1); transform: translateY(-2px); color: inherit; }
    .stat-card:hover::after { transform: scaleX(1); }
    .stat-card.c-teal::after   { background: var(--teal); }
    .stat-card.c-green::after  { background: #22c55e; }
    .stat-card.c-blue::after   { background: #3b82f6; }
    .stat-card.c-amber::after  { background: var(--amber); }
    .stat-card.c-violet::after { background: var(--violet); }

    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-val { font-size: 28px; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--ink); line-height: 1; }
    .stat-lbl { font-size: 11.5px; color: var(--muted); margin-top: 3px; font-weight: 500; }

    /* ══════════════════════════════════════════
       TABLE
    ══════════════════════════════════════════ */
    .data-table { border-collapse: separate; border-spacing: 0; }
    .data-table thead th {
        background: var(--ink); color: rgba(255,255,255,.75);
        font-size: 10.5px; text-transform: uppercase; letter-spacing: 1px;
        font-weight: 700; border: none; padding: 12px 14px; white-space: nowrap;
        font-family: 'Outfit', sans-serif;
    }
    .data-table thead th:first-child { border-radius: 0; }
    .data-table tbody td { padding: 11px 14px; vertical-align: middle; font-size: 13px; border-bottom: 1px solid var(--border); background: #fff; }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background var(--t); }
    .data-table tbody tr:hover td { background: #f8faff; }

    /* ══════════════════════════════════════════
       BADGES & CHIPS
    ══════════════════════════════════════════ */
    .pill { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; font-family:'Syne',sans-serif; letter-spacing:.2px; }
    .b-completed { background:#dcfce7; color:#15803d; }
    .b-ongoing   { background:#dbeafe; color:#1d4ed8; }
    .b-proposed  { background:#fef9c3; color:#a16207; }
    .b-intl      { background:#ede9fe; color:#6d28d9; }
    .b-natl      { background:#ffedd5; color:#c2410c; }
    .b-regl      { background:#cffafe; color:#0e7490; }
    .b-local     { background:#f0fdf4; color:#166534; }
    .b-inst      { background:#e0f2fe; color:#0369a1; }
    .b-multi     { background:#fce7f3; color:#be185d; }
    .sdg-chip    { background:#eef2ff; color:#4338ca; font-size:10.5px; padding:2px 8px; border-radius:20px; display:inline-block; margin:1px; white-space:nowrap; font-weight:600; font-family:'Syne',sans-serif; }
    .dot         { width:6px; height:6px; border-radius:50%; display:inline-block; }

    /* ══════════════════════════════════════════
       PHOTO
    ══════════════════════════════════════════ */
    .photo-thumb { width:40px; height:40px; border-radius:8px; object-fit:cover; border:2px solid var(--border); cursor:pointer; transition:transform .15s, border-color .15s; }
    .photo-thumb:hover { transform:scale(1.12); border-color: var(--teal); }
    .no-photo { width:40px; height:40px; border-radius:8px; background:var(--surface); border:2px dashed var(--border); display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:14px; }

    /* ══════════════════════════════════════════
       UPLOAD
    ══════════════════════════════════════════ */
    .upload-zone { border:2px dashed var(--border); border-radius:var(--radius); padding:28px; text-align:center; cursor:pointer; background:var(--surface); transition:all .2s; position:relative; }
    .upload-zone:hover,.upload-zone.dragover { border-color:var(--teal); background:#f0fdfa; }
    .upload-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .upload-preview { width:100%; max-height:220px; object-fit:contain; border-radius:8px; margin-top:12px; border:1px solid var(--border); display:none; }

    /* ══════════════════════════════════════════
       FILTER BAR
    ══════════════════════════════════════════ */
    .filter-bar { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:16px 20px; margin-bottom:18px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }

    /* ══════════════════════════════════════════
       FORMS
    ══════════════════════════════════════════ */
    .form-label { font-size:12px; font-weight:700; color:var(--text); margin-bottom:5px; font-family:'Syne',sans-serif; letter-spacing:.2px; text-transform:uppercase; }
    .section-hdr {
        font-family: 'Outfit', sans-serif; font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 1.5px; color: var(--ink);
        padding-bottom: 10px;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(90deg, var(--teal), var(--amber)) 1;
        margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }
    .form-control, .form-select { font-size:13.5px; border-color:var(--border); border-radius:9px; font-family:'Plus Jakarta Sans', sans-serif; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(13,148,136,.1); }

    /* ══════════════════════════════════════════
       BUTTONS
    ══════════════════════════════════════════ */
    .btn-navy {
        background: var(--ink); color: #fff; border: none;
        border-radius: 9px; font-weight: 700; font-size: 13px;
        font-family: 'Outfit', sans-serif; letter-spacing: .2px;
        transition: background var(--t), box-shadow var(--t);
    }
    .btn-navy:hover { background: var(--ink-soft); color: #fff; box-shadow: 0 4px 14px rgba(10,15,30,.3); }
    .btn-gold {
        background: linear-gradient(135deg, var(--amber), #d97706); color: #fff; border: none;
        border-radius: 9px; font-weight: 700; font-size: 13px;
        font-family: 'Outfit', sans-serif;
        transition: all var(--t);
    }
    .btn-gold:hover { color: #fff; box-shadow: 0 4px 14px rgba(245,158,11,.4); transform: translateY(-1px); }
    .btn-icon { border-radius: 8px; padding: 5px 9px; border: none; font-size: 13px; cursor: pointer; transition: all .15s; }

    /* ══════════════════════════════════════════
       TOAST NOTIFICATIONS
    ══════════════════════════════════════════ */
    #toast-container {
        position: fixed; bottom: 28px; right: 28px;
        z-index: 99999; display: flex; flex-direction: column; gap: 12px;
        pointer-events: none;
    }
    .toast-popup {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px 14px 0;
        border-radius: 16px;
        font-size: 13.5px; font-weight: 500;
        min-width: 300px; max-width: 420px;
        box-shadow: 0 12px 40px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.07);
        pointer-events: all; cursor: pointer;
        animation: toastIn .4s cubic-bezier(.34,1.56,.64,1) both;
        position: relative; overflow: hidden;
        background: #fff;
        border: 1px solid rgba(0,0,0,.07);
    }
    .toast-accent {
        width: 4px; align-self: stretch; border-radius: 4px 0 0 4px; flex-shrink: 0;
        margin-left: 0;
    }
    .toast-icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 17px;
    }
    .toast-body { flex: 1; min-width: 0; }
    .toast-title { font-weight: 700; font-size: 13px; line-height: 1.3; margin-bottom: 2px; }
    .toast-msg { font-size: 12px; color: #6b7280; line-height: 1.4; font-weight: 400; }
    .toast-popup::after {
        content: '';
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 2.5px;
        animation: toastBar 4s linear forwards;
        border-radius: 0 0 16px 16px;
    }
    .toast-popup.toast-success .toast-accent { background: #10b981; }
    .toast-popup.toast-success .toast-icon-wrap { background: #ecfdf5; color: #10b981; }
    .toast-popup.toast-success .toast-title { color: #065f46; }
    .toast-popup.toast-success::after { background: #10b981; }

    .toast-popup.toast-error .toast-accent { background: #ef4444; }
    .toast-popup.toast-error .toast-icon-wrap { background: #fef2f2; color: #ef4444; }
    .toast-popup.toast-error .toast-title { color: #991b1b; }
    .toast-popup.toast-error::after { background: #ef4444; }

    .toast-popup.toast-info .toast-accent { background: #f59e0b; }
    .toast-popup.toast-info .toast-icon-wrap { background: #fffbeb; color: #f59e0b; }
    .toast-popup.toast-info .toast-title { color: #92400e; }
    .toast-popup.toast-info::after { background: #f59e0b; }

    .toast-popup.toast-out {
        animation: toastOut .3s ease forwards;
    }
    .toast-close {
        margin-left: 4px; opacity: .35; font-size: 18px;
        line-height: 1; flex-shrink: 0; color: #374151;
        transition: opacity .15s; padding-right: 4px;
    }
    .toast-popup:hover .toast-close { opacity: .7; }
    @keyframes toastIn {
        from { opacity:0; transform:translateY(20px) scale(.95); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    @keyframes toastOut {
        from { opacity:1; transform:translateY(0) scale(1); max-height:80px; }
        to   { opacity:0; transform:translateY(10px) scale(.95); max-height:0; margin-bottom:-12px; }
    }
    @keyframes toastBar {
        from { width: 100%; }
        to   { width: 0%; }
    }

    /* ══════════════════════════════════════════
       PAGINATION
    ══════════════════════════════════════════ */
    .pagination .page-link { color:var(--ink); border-radius:8px !important; margin:0 2px; font-size:13px; font-family:'Syne',sans-serif; font-weight:600; border-color:var(--border); }
    .pagination .page-item.active .page-link { background:var(--ink); border-color:var(--ink); }
    .pagination .page-link:hover { background:var(--teal); color:#fff; border-color:var(--teal); }

    /* ══════════════════════════════════════════
       LIGHTBOX
    ══════════════════════════════════════════ */
    #lightbox { display:none; position:fixed; inset:0; background:rgba(10,15,30,.9); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(8px); }
    #lightbox.open { display:flex; animation: fadeIn .2s ease; }
    @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    #lightbox img { max-width:90vw; max-height:90vh; border-radius:12px; box-shadow:0 30px 80px rgba(0,0,0,.6); }
    #lightbox-close { position:absolute; top:20px; right:28px; color:rgba(255,255,255,.6); font-size:30px; cursor:pointer; line-height:1; transition:color .15s; }
    #lightbox-close:hover { color:#fff; }

    /* ══════════════════════════════════════════
       PAGE LOAD ANIMATION
    ══════════════════════════════════════════ */
    .content > * {
        animation: contentFade .35s ease both;
    }
    .content > *:nth-child(1) { animation-delay: .04s; }
    .content > *:nth-child(2) { animation-delay: .08s; }
    .content > *:nth-child(3) { animation-delay: .12s; }
    .content > *:nth-child(4) { animation-delay: .16s; }
    .content > *:nth-child(5) { animation-delay: .20s; }
    @keyframes contentFade {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    </style>
    @stack('styles')
</head>
<body>

@auth
<nav id="sidebar">
    <button class="sb-toggle" id="sidebarToggle" title="Toggle sidebar">
        <span class="t-icon"><span class="t-gem"><i class="bi bi-journal-bookmark-fill"></i></span></span>
        <span class="t-text">
            <span class="t-name">Research PMS</span>
            <span class="t-sub">Presentation Management</span>
        </span>
    </button>

    <div class="sb-nav">
        <div class="sb-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="lk-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span class="lk-label">Dashboard</span>
            <span class="lk-tip">Dashboard</span>
        </a>

        <div class="sb-section">Records</div>
        <a href="{{ route('research.index') }}" class="sb-link {{ request()->routeIs('research.index') ? 'active' : '' }}">
            <span class="lk-icon"><i class="bi bi-table"></i></span>
            <span class="lk-label">All Records</span>
            <span class="lk-tip">All Records</span>
        </a>
        <a href="{{ route('research.create') }}" class="sb-link {{ request()->routeIs('research.create') ? 'active' : '' }}">
            <span class="lk-icon"><i class="bi bi-plus-circle-fill"></i></span>
            <span class="lk-label">Add New Record</span>
            <span class="lk-tip">Add New Record</span>
        </a>


    </div>

    <div class="sb-footer">
        <div class="sb-user-row">
            <i class="bi bi-person-circle"></i>
            <span>{{ Auth::user()->name }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout">
                <span class="lk-icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="lk-label">Sign Out</span>
                <span class="lk-tip">Sign Out</span>
            </button>
        </form>
    </div>
</nav>

<div id="main">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title','Dashboard')</span>
        <div class="topbar-badge">
            <span class="dot-pulse"></span>
            Administrator
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>
</div>

{{-- ── Floating Toast Container ─────────────────────── --}}
<div id="toast-container">
    @if(session('success'))
        <div class="toast-popup toast-success" onclick="dismissToast(this)">
            <div class="toast-accent"></div>
            <div class="toast-icon-wrap"><i class="bi bi-check-circle-fill"></i></div>
            <div class="toast-body">
                <div class="toast-title">Success</div>
                <div class="toast-msg">{{ session('success') }}</div>
            </div>
            <span class="toast-close">&times;</span>
        </div>
    @endif
    @if(session('info'))
        <div class="toast-popup toast-info" onclick="dismissToast(this)">
            <div class="toast-accent"></div>
            <div class="toast-icon-wrap"><i class="bi bi-info-circle-fill"></i></div>
            <div class="toast-body">
                <div class="toast-title">Notice</div>
                <div class="toast-msg">{{ session('info') }}</div>
            </div>
            <span class="toast-close">&times;</span>
        </div>
    @endif
    @if(session('error'))
        <div class="toast-popup toast-error" onclick="dismissToast(this)">
            <div class="toast-accent"></div>
            <div class="toast-icon-wrap"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="toast-body">
                <div class="toast-title">Error</div>
                <div class="toast-msg">{{ session('error') }}</div>
            </div>
            <span class="toast-close">&times;</span>
        </div>
    @endif
</div>
</div>

<div id="lightbox" onclick="closeLightbox()">
    <span id="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img" src="" alt="">
</div>
@endauth

@guest
    @yield('content')
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
if (localStorage.getItem('rpms_sb') === '1') sidebar && sidebar.classList.add('expanded');
document.getElementById('sidebarToggle') && document.getElementById('sidebarToggle').addEventListener('click', () => {
    sidebar.classList.toggle('expanded');
    localStorage.setItem('rpms_sb', sidebar.classList.contains('expanded') ? '1' : '0');
});
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.getElementById('lightbox-img').src = '';
}

function dismissToast(el) {
    el.classList.add('toast-out');
    setTimeout(() => el.remove(), 300);
}
// Auto-dismiss all toasts after 4 seconds
document.querySelectorAll('.toast-popup').forEach(function(t) {
    setTimeout(() => dismissToast(t), 4000);
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@stack('scripts')
</body>
</html>