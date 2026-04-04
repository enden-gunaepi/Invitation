<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false, sidebarExpanded: localStorage.getItem('clientSidebarExpanded') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val));
$watch('sidebarExpanded', val => localStorage.setItem('clientSidebarExpanded', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Client</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --sidebar-w: 86px;
            --topbar-h: 56px;
            --radius: 12px;
            --radius-sm: 8px;
            --bg: #f5f5f7;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f0f0f2;
            --border: #e5e5ea;
            --text: #1d1d1f;
            --text-secondary: #86868b;
            --text-tertiary: #aeaeb2;
            --accent: #34c759;
            --accent-hover: #2db84e;
            --accent-bg: rgba(52, 199, 89, 0.1);
            --sidebar-bg: rgba(245, 245, 247, 0.85);
            --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.06);
            --hover-bg: rgba(0, 0, 0, 0.04);
            --danger: #ff3b30;
            --success: #34c759;
            --warning: #ff9500;
            --info: #0071e3;
        }

        .dark {
            --bg: #1c1c1e;
            --bg-secondary: #2c2c2e;
            --bg-tertiary: #3a3a3c;
            --border: #38383a;
            --text: #f5f5f7;
            --text-secondary: #98989d;
            --text-tertiary: #636366;
            --accent: #30d158;
            --accent-hover: #34db5c;
            --accent-bg: rgba(48, 209, 88, 0.12);
            --sidebar-bg: rgba(28, 28, 30, 0.92);
            --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px 2px rgba(0, 0, 0, 0.15);
            --hover-bg: rgba(255, 255, 255, 0.06);
            --danger: #ff453a;
            --success: #30d158;
            --warning: #ff9f0a;
            --info: #0a84ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            overflow-x: hidden;
        }

        /* ===== APP THEME (White + Blue Accent) ===== */
        .client-shell {
            --sidebar-mini-w: 92px;
            --sidebar-expanded-w: 248px;
            --sidebar-w: var(--sidebar-mini-w);
            --shell-edge-gap: 14px;
            --shell-sidebar-left: 16px;
            --shell-content-gap: 34px;
        }

        .client-shell .sidebar {
            top: var(--shell-edge-gap);
            left: var(--shell-sidebar-left);
            bottom: var(--shell-edge-gap);
            transform: none;
            width: var(--sidebar-w);
            height: calc(100vh - (var(--shell-edge-gap) * 2));
            padding: 0;
            border: 0;
            background: transparent;
            display: block;
            overflow: visible;
            z-index: 80;
            transition: width .28s cubic-bezier(.4, 0, .2, 1), transform .28s ease;
        }

        .client-shell.sidebar-expanded {
            --sidebar-w: var(--sidebar-expanded-w);
        }

        .client-shell .sidebar-header {
            display: none;
        }

        .client-shell .sidebar-stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
        }

        .client-shell .sidebar-panel {
            width: 100%;
            padding: 12px 8px;
            border: 1px solid var(--border);
            border-radius: 30px;
            background: var(--sidebar-bg);
            box-shadow: var(--card-shadow);
            overflow: visible;
        }

        .client-shell .sidebar-nav {
            padding-top: 6px;
            padding-bottom: 10px;
        }

        .client-shell.sidebar-expanded .sidebar-panel {
            border-radius: 22px;
            padding: 12px;
        }

        .client-shell .sidebar-profile {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 4px 4px 14px;
            margin-bottom: 8px;
        }

        .client-shell .sidebar-profile-meta {
            display: none;
            min-width: 0;
        }

        .client-shell .sidebar-profile-name {
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
        }

        .client-shell .sidebar-profile-role {
            color: var(--text-tertiary);
            font-size: 10px;
            margin-top: 2px;
        }

        .client-shell.sidebar-expanded .sidebar-profile {
            justify-content: flex-start;
            padding: 6px 6px 14px;
        }

        .client-shell.sidebar-expanded .sidebar-profile-meta {
            display: block;
        }

        .client-shell .nav-section {
            margin-bottom: 12px;
        }

        .client-shell .sidebar-footer {
            display: block;
            padding: 10px 8px;
        }

        .sidebar-toggle-wrap {
            width: 0;
            display: flex;
            justify-content: flex-start;
            margin: 0;
            position: absolute;
            top: 18px;
            left: calc(100% + 8px);
            z-index: 12;
        }

        .sidebar-toggle-btn {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            color: rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.94);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .22s ease;
            cursor: pointer;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.12);
        }

        .sidebar-toggle-btn:hover {
            background: #fff;
            color: var(--accent);
            transform: translateY(-1px);
        }

        .client-shell.sidebar-expanded .sidebar-toggle-wrap {
            justify-content: flex-start;
        }

        .client-shell .sidebar-brand,
        .client-shell .user-name {
            color: var(--text);
        }

        .client-shell .nav-label {
            color: var(--text-tertiary);
        }

        .client-shell .sidebar .nav-item {
            color: var(--text-secondary);
        }

        .client-shell .sidebar .nav-item:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        .client-shell .sidebar .nav-item.active {
            background: var(--accent-bg);
            color: var(--accent);
        }

        .client-shell .sidebar .nav-item.active::before {
            content: "";
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            border-radius: 999px;
            background: linear-gradient(180deg, #f87171, #b91c1c);
            box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.12);
        }

        .client-shell.sidebar-expanded .sidebar .nav-item.active::before {
            right: -12px;
        }

        .client-shell .nav-item::after {
            z-index: 9999;
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.4);
        }

        .client-shell.sidebar-expanded .nav-item::after {
            display: none;
        }

        .client-shell .topbar {
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid #e5e5df;
            backdrop-filter: blur(14px);
            left: calc(var(--shell-sidebar-left) + var(--sidebar-w) + var(--shell-content-gap));
            top: var(--shell-edge-gap);
            right: var(--shell-edge-gap);
            border-radius: 24px;
            box-shadow: 0 16px 36px rgba(15, 15, 15, 0.06);
        }

        .client-shell .page-content {
            max-width: none;
            width: 100%;
            padding: 20px 24px 24px 0;
        }

        .client-shell .card {
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border-color: #e7e5e4;
        }

        .client-shell .btn-primary {
            background: linear-gradient(135deg, #7f1d1d, #dc2626);
        }

        .client-shell .btn-primary:hover {
            background: linear-gradient(135deg, #6b1414, #b91c1c);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            z-index: 50;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-header {
            padding: 14px 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }

        .sidebar-logo {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #7f1d1d, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .sidebar-brand {
            display: none;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px 8px;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-label {
            display: none;
        }

        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: 14px;
            color: var(--text-secondary);
            font-size: 0;
            text-decoration: none;
            transition: all 0.15s ease;
            margin: 0 auto 8px;
        }

        .nav-item:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--accent-bg);
            color: var(--accent);
            font-weight: 600;
        }

        .nav-item i {
            width: 18px;
            text-align: center;
            font-size: 15px;
        }

        .client-shell.sidebar-expanded .nav-item {
            width: 100%;
            height: 40px;
            justify-content: flex-start;
            padding: 0 12px;
            font-size: 12px;
            gap: 10px;
            margin: 0 0 8px;
            border-radius: 12px;
        }

        .client-shell.sidebar-expanded .nav-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin: 8px 6px 10px;
        }

        .nav-item::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            background: #0f172a;
            color: #f8fafc;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .01em;
            padding: 6px 10px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all .16s ease;
            z-index: 90;
        }

        .nav-item:hover::after,
        .nav-item:focus-visible::after {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        .sidebar-footer {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .user-trigger {
            width: 100%;
            background: transparent;
            border: 0;
            padding: 0;
            text-align: left;
            cursor: pointer;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(255, 255, 255, .95), rgba(231, 229, 228, .92));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #111;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .2);
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            display: none;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-secondary);
            display: none;
        }

        .user-menu {
            margin-top: 10px;
            border: 1px solid rgba(0, 0, 0, .08);
            background: #ffffff;
            border-radius: 18px;
            padding: 10px;
            position: absolute;
            left: 0;
            bottom: calc(100% + 10px);
            min-width: 170px;
            z-index: 100;
            box-shadow: 0 20px 40px rgba(15, 15, 15, .12);
        }

        .user-menu a,
        .user-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            text-align: left;
            font-size: 12px;
            padding: 10px 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            cursor: pointer;
        }

        .user-menu a:hover,
        .user-menu button:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        .user-menu .danger {
            color: var(--danger);
        }

        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-w);
            height: var(--topbar-h);
            z-index: 40;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .topbar-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-user-wrap {
            position: relative;
        }

        .topbar-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            color: var(--text);
            border-radius: 999px;
            padding: 4px 10px 4px 4px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .topbar-user-btn:hover {
            background: var(--hover-bg);
            border-color: var(--text-tertiary);
        }

        .topbar-user-btn .user-avatar {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }

        .topbar-user-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .topbar-user-menu {
            margin-top: 10px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            border-radius: 10px;
            padding: 6px;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            min-width: 180px;
            z-index: 120;
            box-shadow: 0 14px 26px rgba(2, 6, 23, 0.18);
        }

        .topbar-user-menu a,
        .topbar-user-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            text-align: left;
            font-size: 12px;
            padding: 7px 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            cursor: pointer;
        }

        .topbar-user-menu a:hover,
        .topbar-user-menu button:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        .topbar-user-menu .danger {
            color: var(--danger);
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 14px;
        }

        .topbar-btn:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        .mobile-toggle {
            display: none;
        }

        .theme-toggle {
            position: relative;
            width: 44px;
            height: 24px;
            border-radius: 12px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle .toggle-dot {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--bg-secondary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .dark .theme-toggle {
            background: var(--accent);
            border-color: var(--accent);
        }

        .dark .theme-toggle .toggle-dot {
            transform: translateX(20px);
        }

        .main-content {
            margin-left: calc(var(--shell-sidebar-left) + var(--sidebar-w) + var(--shell-content-gap));
            padding-top: calc(var(--topbar-h) + (var(--shell-edge-gap) * 2));
            min-height: 100vh;
        }

        .page-content {
            padding: 24px 24px 24px 0;
            max-width: 1200px;
        }

        .page-shell {
            display: grid;
            gap: 18px;
        }

        /* Components — same as admin */
        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
        }

        .stat-card {
            padding: 20px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 6px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-secondary);
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .form-input::placeholder {
            color: var(--text-tertiary);
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2386868b' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.762L10.825 4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--hover-bg);
        }

        .btn-danger {
            background: rgba(255, 59, 48, 0.1);
            color: var(--danger);
        }

        .btn-danger:hover {
            background: rgba(255, 59, 48, 0.18);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-outline:hover {
            border-color: var(--text-tertiary);
            color: var(--text);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(52, 199, 89, 0.12);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(255, 149, 0, 0.12);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(255, 59, 48, 0.12);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(0, 113, 227, 0.08);
            color: var(--info);
        }

        .badge-default {
            background: var(--hover-bg);
            color: var(--text-secondary);
        }

        .badge-active {
            background: rgba(52, 199, 89, 0.12);
            color: var(--success);
        }

        .badge-pending {
            background: rgba(255, 149, 0, 0.12);
            color: var(--warning);
        }

        .badge-draft {
            background: var(--hover-bg);
            color: var(--text-secondary);
        }

        .toast {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 200;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            padding: 14px 20px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 4s forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 45;
        }

        /* Mobile Bottom Dock */
        .mobile-dock {
            position: fixed;
            left: 50%;
            bottom: 14px;
            transform: translateX(-50%);
            width: min(360px, calc(100% - 24px));
            height: 62px;
            border-radius: 22px;
            background: linear-gradient(180deg, #111827, #0b1220);
            border: 1px solid rgba(148, 163, 184, .2);
            box-shadow: 0 16px 35px rgba(2, 6, 23, .45);
            z-index: 80;
            display: none;
            align-items: center;
            padding: 0 10px;
        }

        .mobile-dock-track {
            width: 100%;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0 2px;
        }

        .mobile-dock-track::-webkit-scrollbar {
            display: none;
        }

        .mobile-dock-slot {
            flex: 0 0 calc((100% - 20px) / 3);
            display: flex;
            justify-content: center;
            scroll-snap-align: start;
        }

        .mobile-dock-link {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: rgba(255, 255, 255, .82);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1rem;
            transition: all .2s ease;
        }

        .mobile-dock-link:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .mobile-dock-link.active {
            background: rgba(185, 28, 28, .14);
            color: #dc2626;
            box-shadow: inset 0 0 0 1px rgba(185, 28, 28, .26);
        }

        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }

            .sidebar-stack {
                gap: 0;
            }

            .sidebar-panel {
                border-radius: 0 !important;
            }

            .sidebar-profile,
            .sidebar-footer {
                display: none !important;
            }

            .sidebar-toggle-wrap {
                display: none;
            }

            .sidebar-overlay {
                display: none !important;
            }

            .topbar {
                position: relative;
                left: 0;
                right: 0;
                top: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .main-content {
                margin-left: 0;
                padding-top: 0;
            }

            .mobile-toggle {
                display: none !important;
            }

            .mobile-dock {
                display: flex;
            }

            .main-content {
                padding-bottom: 88px;
            }
        }

        @media (max-width: 640px) {
            .page-content {
                padding: 16px;
            }

            .topbar {
                padding: 0 16px;
            }
        }
    </style>
</head>

<body class="client-shell" :class="{ 'sidebar-expanded': sidebarExpanded }">
    <script>
        if (localStorage.getItem('adminSidebarExpanded') === 'true') {
            document.body.classList.add('sidebar-expanded');
        }
    </script>
    @if (session('success'))
        <div class="toast"><i class="fas fa-check-circle" style="color: var(--success);"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="toast"><i class="fas fa-exclamation-circle" style="color: var(--danger);"></i>
            {{ session('error') }}</div>
    @endif

    <div class="sidebar-overlay" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    <aside class="sidebar" :class="{ 'open': sidebarOpen, 'expanded': sidebarExpanded }">
        <div class="sidebar-stack">
            <div class="sidebar-toggle-wrap">
                <button type="button" class="sidebar-toggle-btn" @click="sidebarExpanded = !sidebarExpanded"
                    :title="sidebarExpanded ? 'Kecilkan Sidebar' : 'Perbesar Sidebar'">
                    <i class="fas" :class="sidebarExpanded ? 'fa-angles-left' : 'fa-angles-right'"></i>
                </button>
            </div>
            <div class="sidebar-panel">
                <div class="sidebar-profile">
                    @if(auth()->user()->company_logo)
                        <div class="w-8 h-8 rounded-full overflow-hidden shadow-sm shrink-0 border border-gray-300 mr-2" style="width: 34px; height: 34px; min-width: 34px;">
                            <img src="{{ Storage::url(auth()->user()->company_logo) }}" alt="Logo" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="user-avatar">{{ substr(auth()->user()->company_name ?? auth()->user()->name, 0, 1) }}</div>
                    @endif
                    <div class="sidebar-profile-meta">
                        <div class="sidebar-profile-name">{{ auth()->user()->company_name ?? auth()->user()->name }}</div>
                        <div class="sidebar-profile-role">Client</div>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    <div class="nav-section">
                        <div class="nav-label">Menu</div>
                        <a href="{{ route('client.dashboard') }}" data-tip="Dashboard"
                            class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-house"></i> Dashboard
                        </a>
                    </div>
                    <div class="nav-section">
                        <div class="nav-label">Undangan</div>
                        <a href="{{ route('client.invitations.index') }}" data-tip="Undangan Saya"
                            class="nav-item {{ request()->routeIs('client.invitations.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i> Undangan Saya
                        </a>
                        <a href="{{ route('client.invitations.create') }}" data-tip="Buat Undangan"
                            class="nav-item {{ request()->routeIs('client.invitations.create') ? 'active' : '' }}">
                            <i class="fas fa-plus"></i> Buat Undangan
                        </a>
                        <a href="{{ route('client.templates.index') }}" data-tip="Katalog Template"
                            class="nav-item {{ request()->routeIs('client.templates.*') ? 'active' : '' }}">
                            <i class="fas fa-palette"></i> Katalog Template
                        </a>
                        <a href="{{ route('client.packages.select') }}" data-tip="Pilih Paket"
                            class="nav-item {{ request()->routeIs('client.packages.*') ? 'active' : '' }}">
                            <i class="fas fa-box-open"></i> Pilih Paket
                        </a>
                        <a href="{{ route('client.affiliate.index') }}" data-tip="Affiliate"
                            class="nav-item {{ request()->routeIs('client.affiliate.*') ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-dollar"></i> Affiliate
                        </a>
                    </div>
                    <div class="nav-section">
                        <div class="nav-label">Wedding Planner</div>
                        <a href="{{ route('client.planner.dashboard') }}" data-tip="Planner"
                            class="nav-item {{ request()->routeIs('client.planner.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i> Planner
                        </a>
                        <a href="{{ route('client.planner.checklist.index') }}" data-tip="Checklist"
                            class="nav-item {{ request()->routeIs('client.planner.checklist.*') ? 'active' : '' }}">
                            <i class="fas fa-list-check"></i> Checklist
                        </a>
                        <a href="{{ route('client.planner.budget.index') }}" data-tip="Budget"
                            class="nav-item {{ request()->routeIs('client.planner.budget.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i> Budget
                        </a>
                        <a href="{{ route('client.planner.vendors.index') }}" data-tip="Vendor"
                            class="nav-item {{ request()->routeIs('client.planner.vendors.*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i> Vendor
                        </a>
                        <a href="{{ route('client.planner.advisor.index') }}" data-tip="AI Advisor"
                            class="nav-item {{ request()->routeIs('client.planner.advisor.*') ? 'active' : '' }}">
                            <i class="fas fa-robot"></i> AI Advisor
                        </a>
                    </div>
                </nav>
            </div>
            <div class="sidebar-panel sidebar-footer">
                <div class="user-info">
                    <a href="{{ route('profile.edit') }}" data-tip="Profile Settings"
                        class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user-gear"></i> Profile Settings
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-btn mobile-toggle" @click="sidebarOpen = !sidebarOpen"><i
                    class="fas fa-bars"></i></button>
            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                @hasSection('page-subtitle')
                    <div class="topbar-subtitle">@yield('page-subtitle')</div>
                @endif
            </div>
        </div>
        <div class="topbar-right" x-data="{ userMenuOpen: false }">
            <div class="theme-toggle" @click="darkMode = !darkMode" title="Toggle Dark/Light">
                <div class="toggle-dot">
                    <span x-show="!darkMode">&#9728;</span>
                    <span x-show="darkMode">&#9790;</span>
                </div>
            </div>
            <div class="topbar-user-wrap">
                <button class="topbar-user-btn" @click="userMenuOpen = !userMenuOpen">
                    @if(auth()->user()->avatar)
                        <div class="w-6 h-6 rounded-full overflow-hidden shadow-sm border border-gray-200" style="width: 30px; height: 30px;">
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    @endif
                    <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                </button>
                <div class="topbar-user-menu" x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                    x-transition x-cloak>
                    <a href="{{ route('profile.edit') }}"><i class="fas fa-user-gear"></i> Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="danger"><i class="fas fa-arrow-right-from-bracket"></i>
                            Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="page-content page-shell">@yield('content')</div>
    </main>

    <nav class="mobile-dock" aria-label="Mobile Navigation">
        <div class="mobile-dock-track" data-mobile-dock-track>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.dashboard') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}"
                    title="Dashboard">
                    <i class="fas fa-house"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.invitations.index') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.invitations.index') ? 'active' : '' }}"
                    title="Undangan Saya">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.invitations.create') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.invitations.create') ? 'active' : '' }}"
                    title="Buat Undangan">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.affiliate.index') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.affiliate.*') ? 'active' : '' }}"
                    title="Affiliate">
                    <i class="fas fa-hand-holding-dollar"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.templates.index') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.templates.*') ? 'active' : '' }}"
                    title="Template">
                    <i class="fas fa-palette"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('client.packages.select') }}"
                    class="mobile-dock-link {{ request()->routeIs('client.packages.*') ? 'active' : '' }}"
                    title="Pilih Paket">
                    <i class="fas fa-box-open"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('profile.edit') }}"
                    class="mobile-dock-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    title="Pengaturan">
                    <i class="fas fa-gear"></i>
                </a>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.querySelector('[data-mobile-dock-track]');
            if (!track) return;

            const key = 'client_mobile_dock_scroll';
            const saved = localStorage.getItem(key);
            if (saved !== null) {
                track.scrollLeft = parseInt(saved, 10) || 0;
            } else {
                const active = track.querySelector('.mobile-dock-link.active');
                if (active) {
                    active.scrollIntoView({
                        behavior: 'auto',
                        inline: 'center',
                        block: 'nearest'
                    });
                }
            }

            track.addEventListener('scroll', function() {
                localStorage.setItem(key, String(track.scrollLeft));
            }, {
                passive: true
            });
        });
    </script>
    @stack('scripts')
    {{-- Floating Donation Ad Card (Shows every 30 mins if closed) --}}
    <div x-data="{ 
            showAd: false, 
            init() {
                const hideUntil = localStorage.getItem('hideSaweriaAdUntil');
                if (!hideUntil || Date.now() > parseInt(hideUntil)) {
                    this.showAd = true;
                }
            },
            closeAd() {
                this.showAd = false;
                localStorage.setItem('hideSaweriaAdUntil', Date.now() + (30 * 60 * 1000));
            }
        }"
        x-show="showAd"
        x-cloak
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-6 left-6 z-[9999] w-[calc(var(--sidebar-w)-48px)] rounded-2xl overflow-hidden shadow-2xl transition-all duration-300" 
        style="border: 1px solid rgba(251,191,36,0.3); min-width: 200px; max-width: 260px;"
        :style="sidebarExpanded ? 'left: 24px; max-width: 260px;' : 'left: 24px; max-width: 60px; min-width: 60px;'">
        
        <button x-show="sidebarExpanded" @click="closeAd()" class="absolute top-2 right-2 w-5 h-5 flex items-center justify-center rounded-full bg-black/20 text-gray-400 hover:text-white hover:bg-black/40 transition-colors z-10" title="Tutup iklan (muncul lagi 30 mnt)">
            <i class="fas fa-times text-[10px]"></i>
        </button>
        
        {{-- Expanded View --}}
        <div x-show="sidebarExpanded" class="p-4 pt-5 text-center bg-gradient-to-b from-[#1e293b] to-[#0f172a]">
            <div class="w-12 h-12 mx-auto bg-amber-400 text-slate-900 rounded-full flex items-center justify-center text-xl shadow-[0_0_15px_rgba(251,191,36,0.25)] mb-3 relative animate-bounce" style="animation-duration: 2s;">
                <i class="fas fa-coffee"></i>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
            </div>
            <div class="text-amber-400 text-xs font-bold mb-1 uppercase tracking-wide">Dukung Kami</div>
            <div class="text-gray-400 text-[10px] leading-relaxed mb-3 px-1">Bantu traktir kopi programmer agar update makin ngebut! ☕</div>
            <a href="https://saweria.co/gunaepi" target="_blank" class="block w-full py-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-900 text-xs font-bold rounded-lg transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fas fa-heart text-[#dc2626] mr-1"></i> Donasi Sekarang
            </a>
        </div>

        {{-- Mini View (When Sidebar is hidden/collapsed) --}}
        <div x-show="!sidebarExpanded" class="p-2 py-3 flex justify-center bg-gradient-to-b from-[#1e293b] to-[#0f172a]">
            <a href="https://saweria.co/gunaepi" target="_blank" title="Traktir Kopi Programmer" class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-500 text-slate-900 flex items-center justify-center rounded-xl shadow-[0_0_15px_rgba(251,191,36,0.3)] hover:scale-105 transition-transform relative">
                <i class="fas fa-coffee"></i>
                <button @click.prevent="closeAd()" class="absolute -top-2 -right-2 w-4 h-4 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors z-[10000]" title="Tutup iklan">
                    <i class="fas fa-times text-[8px]"></i>
                </button>
            </a>
        </div>
    </div>
</body>

</html>
