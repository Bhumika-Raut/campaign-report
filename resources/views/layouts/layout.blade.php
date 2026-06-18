<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #08090f;
            --bg-surface: #0b0c13;
            --bg-card: #0d0e15;
            --border-color: #181b28;
            --border-hover: #26293c;
            --text-primary: #f8fafc;
            --text-muted: #8c8ea7;
            --color-primary: #a855f7;
            --color-primary-hover: #9333ea;
            --color-success: #10b981;
            --font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        header {
            border-bottom: 1px solid var(--border-color);
            padding: 0 3rem;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-surface);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .tabs {
            display: flex;
            gap: 2.5rem;
            height: 100%;
            align-items: center;
        }

        .tab-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 1rem;
            height: 100%;
            display: flex;
            align-items: center;
            position: relative;
            transition: color 0.2s;
        }

        .tab-link:hover {
            color: var(--text-primary);
        }

        .tab-link.active {
            color: var(--color-primary);
            font-weight: 600;
        }

        .tab-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--color-primary);
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.6);
        }

        .content {
            padding: 3rem;
            max-width: 1500px;
            width: 100%;
            box-sizing: border-box;
            margin: 0 auto;
            flex-grow: 1;
        }

        .btn {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.25);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(168, 85, 247, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            box-shadow: none;
        }

        .btn-outline:hover {
            background: var(--bg-card);
            border-color: var(--border-hover);
            transform: none;
            box-shadow: none;
        }

        .form-control {
            background-color: #11121a;
            border: 1px solid var(--border-color);
            color: white;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            outline: none;
            font-size: 0.875rem;
            font-family: var(--font-family);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Premium KPI Cards Layout */
        .stat-card-premium {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.45);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.2s;
            position: relative;
            overflow: hidden;
            text-align: left;
        }

        .stat-card-premium:hover {
            transform: translateY(-3px);
            border-color: var(--border-hover);
        }

        .stat-card-premium-left {
            display: flex;
            flex-direction: column;
            z-index: 2;
        }

        .stat-card-premium-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
            letter-spacing: -0.03em;
        }

        .stat-card-premium-label {
            color: var(--text-muted);
            font-size: 0.825rem;
            font-weight: 500;
            margin-top: 0.375rem;
        }

        .stat-card-premium-right {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(168, 85, 247, 0.08); /* purple watermark icon */
            z-index: 1;
            transition: color 0.2s;
        }
        
        .stat-card-premium:hover .stat-card-premium-right {
            color: rgba(168, 85, 247, 0.18);
        }

        .stat-card-premium-right svg {
            width: 2.75rem;
            height: 2.75rem;
        }

        /* Inline Filter Bar Layout */
        .filter-bar-inline {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .filter-group-inline {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .filter-group-inline label {
            color: var(--text-muted);
            font-size: 0.675rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Capsule selector buttons style */
        .capsule-group {
            display: flex;
            gap: 0.4rem;
            background: #09080e;
            padding: 0.25rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .capsule-btn {
            background: transparent;
            color: var(--text-muted);
            border: none;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
            font-family: var(--font-family);
        }

        .capsule-btn.active {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(168, 85, 247, 0.3);
        }

        .capsule-btn:hover:not(.active) {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.03);
        }

        /* Card and Table styling */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s, border-color 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-hover);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 750;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.775rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: #d1d5db;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
            color: var(--text-primary);
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
            gap: 0.25rem;
        }

        .pagination svg {
            width: 16px !important;
            height: 16px !important;
            display: inline-block !important;
            vertical-align: middle;
        }

        .pagination nav {
            display: flex;
            gap: 0.5rem;
        }

        .pagination span, .pagination a {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.875rem;
            background-color: var(--bg-surface);
            transition: all 0.2s;
        }

        .pagination a:hover {
            border-color: var(--color-primary);
            color: var(--text-primary);
        }

        .pagination .active span {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            font-weight: 600;
        }

        /* Import File Dialog Banner */
        .import-banner {
            background-color: var(--bg-surface);
            border: 1px dashed var(--border-color);
            padding: 1.25rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="tabs">
            <a href="{{ route('client-consumption') }}" class="tab-link {{ request()->routeIs('client-consumption') ? 'active' : '' }}">Client Consumption</a>
            <a href="{{ route('campaign-report') }}" class="tab-link {{ request()->routeIs('campaign-report') ? 'active' : '' }}">Campaign Report</a>
        </div>
        <div>
            <a href="{{ route('sync.show') }}" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.825rem; font-weight: 600; border-radius: 6px; gap: 0.375rem; background-color: var(--bg-card); display: inline-flex; align-items: center; {{ request()->routeIs('sync.show') ? 'border-color: var(--color-primary); color: var(--color-primary);' : '' }}">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.25rem;"><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-7.1 2h-3.93a.25.25 0 0 0-.192.41l1.966 2.36a.25.25 0 0 0 .384 0l1.966-2.36a.25.25 0 0 0-.192-.41z"/><path d="M9.05 1.22A7 7 0 0 0 1.52 7.5a.5.5 0 0 0 .498.441h1.365a.5.5 0 0 0 .49-.408 4 4 0 0 1 7.64-1.578.5.5 0 0 0 .372.269l3 1a.5.5 0 0 0 .56-.678A7 7 0 0 0 9.05 1.22zm-6.89 8.2a.5.5 0 0 0-.372-.269l-3-1a.5.5 0 0 0-.56.678 7 7 0 0 0 13.06 4.63.5.5 0 0 0-.498-.44h-1.365a.5.5 0 0 0-.49.408 4 4 0 0 1-7.64 1.578z"/></svg>
                Sync Data
            </a>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid var(--color-success); color: var(--color-success); padding: 1.25rem 2rem; border-radius: 10px; margin-bottom: 2rem; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                {{ session('success') }}
            </div>
        @endif



        @yield('main')
    </div>
</body>
</html>
