@extends('layouts.layout')

@section('main')
<style>
    .filter-overlay {
        position: absolute;
        top: 55px;
        right: 0;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.75rem;
        border-radius: 12px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6);
        z-index: 50;
        width: 380px;
        display: none;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: 0.05em;
    }

    .range-inputs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    /* Tab switcher for Table vs Visual Analytics */
    .view-toggle-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 2rem;
    }

    .view-toggle {
        display: flex;
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.25rem;
        width: 320px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }

    .view-toggle-btn {
        flex: 1;
        background: transparent;
        color: var(--text-muted);
        border: none;
        padding: 0.5rem 1rem;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: var(--font-family);
    }

    .view-toggle-btn.active {
        background-color: var(--color-primary);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .view-toggle-btn:hover:not(.active) {
        color: var(--text-primary);
        background-color: rgba(255, 255, 255, 0.02);
    }

    /* Override main layout content padding for maximum screen usage */
    .content {
        padding: 1.25rem 3rem !important;
    }

    /* Stat Grid Override for 6 KPI Cards (more compact) */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .stat-card-compact {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.625rem 0.75rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s, border-color 0.2s;
    }

    .stat-card-compact:hover {
        transform: translateY(-2px);
        border-color: var(--border-hover);
    }

    .stat-value-compact {
        font-size: 1.5rem;
        font-weight: 750;
        margin-bottom: 0.15rem;
        line-height: 1.2;
    }

    .stat-label-compact {
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media (max-width: 1200px) {
        .stat-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tab switcher for Table vs Visual Analytics */
    .view-toggle-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1.25rem;
    }

    /* Analytics Section Grid Layout - 4 Column Layout */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .col-span-1 {
        grid-column: span 1;
    }

    .col-span-2 {
        grid-column: span 2;
    }

    @media (max-width: 1200px) {
        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .col-span-1, .col-span-2 {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
        .col-span-1, .col-span-2 {
            grid-column: span 1;
        }
    }

    .chart-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        min-height: 200px;
    }

    .chart-card-title {
        font-size: 0.825rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 0;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-container {
        position: relative;
        flex-grow: 1;
        height: 145px;
        width: 100%;
    }

    .chart-empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 500;
        background-color: rgba(255, 255, 255, 0.01);
        border: 1px dashed var(--border-color);
        border-radius: 8px;
    }

    .fade-section {
        transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
    }
    .fade-section.hidden {
        display: none !important;
        opacity: 0;
        transform: translateY(10px);
    }

    /* Chart Download Dropdown Style */
    .chart-actions-dropdown {
        position: relative;
    }
    .chart-download-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0.35rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .chart-download-btn:hover {
        color: var(--color-primary);
        background-color: rgba(255, 255, 255, 0.05);
    }
    .chart-download-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        z-index: 1000;
        min-width: 120px;
        padding: 0.35rem 0;
        margin-top: 4px;
    }
    .chart-download-menu.show {
        display: block;
    }
    .section-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.5rem;
    }
    .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .download-link {
        display: block;
        padding: 0.5rem 1rem;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 0.775rem;
        font-weight: 500;
        transition: background-color 0.15s, color 0.15s;
        text-align: left;
    }
    .download-link:hover {
        background-color: var(--border-color);
        color: var(--color-primary);
    }
</style>

<!-- KPI Summary Stat Cards Grid -->
<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
    <!-- Card 1: Total Clients -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalClients) }}</div>
            <div class="stat-card-premium-label">Total Clients</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="16"></line><line x1="15" y1="22" x2="15" y2="16"></line><line x1="9" y1="16" x2="15" y2="16"></line><path d="M9 6h.01"></path><path d="M15 6h.01"></path><path d="M9 10h.01"></path><path d="M15 10h.01"></path></svg>
        </div>
    </div>
    <!-- Card 2: Premium Plan -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($premiumClients) }}</div>
            <div class="stat-card-premium-label">Premium Plan</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7z"></path><path d="M3 20h18"></path></svg>
        </div>
    </div>
    <!-- Card 3: Live Accounts -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($liveClients) }}</div>
            <div class="stat-card-premium-label">Live Accounts</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
        </div>
    </div>
    <!-- Card 4: Credits Consumed -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalConsumed, 2) }}</div>
            <div class="stat-card-premium-label">Credits Consumed</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="15" x2="12" y2="21"></line></svg>
        </div>
    </div>
    <!-- Card 5: Remaining Credits -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalRemaining, 2) }}</div>
            <div class="stat-card-premium-label">Remaining Credits</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="12" cy="12" r="3"></circle><path d="M12 2v3"></path><path d="M12 19v3"></path><path d="M2 12h3"></path><path d="M19 12h3"></path></svg>
        </div>
    </div>
    <!-- Card 6: Total Posts -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalPosts) }}</div>
            <div class="stat-card-premium-label">Total Posts</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        </div>
    </div>
</div>

<!-- View Toggle Switcher -->
<div class="view-toggle-container">
    <div class="view-toggle">
        <button id="toggle-table" class="view-toggle-btn active">Table View</button>
        <button id="toggle-analytics" class="view-toggle-btn">Visual Analytics</button>
    </div>
</div>

<!-- View Section 1: Table View -->
<div id="table-view-section" class="fade-section">
    <div class="card" style="position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button class="btn btn-outline" style="padding: 0.625rem 0.75rem;">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M1 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4zM1 9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V9z"/></svg>
                </button>
                <a href="{{ route('export.consumption') }}" class="btn btn-outline" style="gap: 0.375rem;">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    Download CSV
                </a>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; position: relative;">
                <form id="mainSearchForm" method="GET" action="{{ route('client-consumption') }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="plan" value="{{ request('plan') }}">
                    <input type="hidden" name="last_shared_from" value="{{ request('last_shared_from') }}">
                    <input type="hidden" name="last_shared_to" value="{{ request('last_shared_to') }}">
                    <input type="hidden" name="post_created_from" value="{{ request('post_created_from') }}">
                    <input type="hidden" name="post_created_to" value="{{ request('post_created_to') }}">
                    
                    <label style="color: var(--text-primary); font-size: 0.875rem; font-weight: 500;">Search:</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width: 220px;" onkeypress="if(event.key === 'Enter') this.form.submit();">
                </form>

                <button class="btn btn-outline" id="filterToggleBtn" style="gap: 0.375rem;">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-.124.318l-3.978 4.773a1 1 0 0 0-.242.623V13a1 1 0 0 1-.553.894l-2 1A1 1 0 0 1 6 13.9v-4.3a1 1 0 0 0-.242-.623L1.782 4.318a.5.5 0 0 1-.124-.318v-2.5z"/></svg>
                    Filters
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.125rem;"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>
                </button>

                <!-- Filter Search Drawer Modal -->
                <div class="filter-overlay" id="filterOverlay">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                        <span style="font-weight: 700; font-size: 0.9rem; letter-spacing: 0.02em;">FILTER SEARCH</span>
                        <a href="{{ route('client-consumption') }}" style="color: #a855f7; text-decoration: none; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">RESET ALL</a>
                    </div>

                    <form id="drawerFilterForm" method="GET" action="{{ route('client-consumption') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="form-group">
                            <label>Account Status</label>
                            <select name="status" class="form-control" style="width: 100%; box-sizing: border-box;">
                                <option value="">All Statuses</option>
                                <option value="Live" {{ request('status') === 'Live' ? 'selected' : '' }}>Live</option>
                                <option value="Not Live" {{ request('status') === 'Not Live' ? 'selected' : '' }}>Not Live</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Plan Type</label>
                            <select name="plan" class="form-control" style="width: 100%; box-sizing: border-box;">
                                <option value="">All Plans</option>
                                <option value="Free" {{ request('plan') === 'Free' ? 'selected' : '' }}>Free</option>
                                <option value="Premium" {{ request('plan') === 'Premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Last Shared Date Range</label>
                            <div class="range-inputs">
                                <input type="date" name="last_shared_from" value="{{ request('last_shared_from') }}" class="form-control" style="flex: 1; min-width: 0;">
                                <span style="color: var(--text-muted); font-size: 0.825rem;">to</span>
                                <input type="date" name="last_shared_to" value="{{ request('last_shared_to') }}" class="form-control" style="flex: 1; min-width: 0;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Post Creation Date Range</label>
                            <div class="range-inputs">
                                <input type="date" name="post_created_from" value="{{ request('post_created_from') }}" class="form-control" style="flex: 1; min-width: 0;">
                                <span style="color: var(--text-muted); font-size: 0.825rem;">to</span>
                                <input type="date" name="post_created_to" value="{{ request('post_created_to') }}" class="form-control" style="flex: 1; min-width: 0;">
                            </div>
                        </div>

                        <button type="submit" class="btn" style="width: 100%; justify-content: center; margin-top: 1rem;">Apply Parameters</button>
                    </form>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Account Created</th>
                        <th>Last Created Post</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Consumed</th>
                        <th style="text-align: right;">Remaining</th>
                        <th>Last Shared</th>
                        <th style="text-align: right;">Clicks</th>
                        <th style="text-align: right;">Posts</th>
                        <th style="text-align: right;">Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-muted);">{{ $client->id }}</td>
                            <td style="font-weight: 500; color: var(--text-primary);">{{ $client->email }}</td>
                            <td>{{ $client->company ?? 'N/A' }}</td>
                            <td>{{ $client->account_created->toDateString() }}</td>
                            <td>{{ $client->last_created_post ? $client->last_created_post->toDateString() : 'N/A' }}</td>
                            <td>
                                <span style="color: {{ $client->plan === 'Premium' ? '#f59e0b' : '#9ca3af' }}; font-weight: 600;">
                                    {{ $client->plan }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge" style="background-color: {{ $client->status === 'Live' ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }}; color: {{ $client->status === 'Live' ? 'var(--color-success)' : '#ef4444' }};">
                                    {{ $client->status }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #3b82f6;">{{ number_format($client->consumed) }}</td>
                            <td style="text-align: right; font-weight: 600; color: #6ee7b7;">{{ number_format($client->remaining) }}</td>
                            <td>{{ $client->last_shared ? $client->last_shared->toDateString() : 'N/A' }}</td>
                            <td style="text-align: right;">{{ number_format($client->clicks) }}</td>
                            <td style="text-align: right; font-weight: 600; color: #ec4899;">{{ number_format($client->posts) }}</td>
                            <td style="text-align: right;">{{ number_format($client->comments) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; color: var(--text-muted); padding: 3rem;">No client records found. Try uploading an Excel data sheet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $clients->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- View Section 2: Visual Analytics View -->
<div id="analytics-view-section" class="fade-section hidden">
    <div class="section-title-bar">
        <div class="section-title">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zm-3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V6zM5 10a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4z"/></svg>
            Visual Analytics Overview
        </div>
        <div class="chart-actions-dropdown" style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Download Section:</span>
            <button class="chart-download-btn section-download-btn" title="Download Entire Section" data-section="visual-analytics">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
            </button>
            <div class="chart-download-menu">
                <a href="#" class="download-link section-download-link" data-format="png" data-section="visual-analytics">PNG Image</a>
                <a href="#" class="download-link section-download-link" data-format="jpeg" data-section="visual-analytics">JPEG Image</a>
            </div>
        </div>
    </div>
    <div class="analytics-grid">
        <!-- 1. Plan Type Distribution -->
        <div class="chart-card col-span-1">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    🍩 Plan Type Distribution
                </h3>
                <div class="chart-actions-dropdown">
                    <button class="chart-download-btn" title="Download Chart">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    </button>
                    <div class="chart-download-menu">
                        <a href="#" class="download-link" data-format="png">PNG Image</a>
                        <a href="#" class="download-link" data-format="jpeg">JPEG Image</a>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="chartPlanDistribution"></canvas>
                <div id="chartPlanDistribution-empty" class="chart-empty-state" style="display:none;">
                    No Plan Data Available
                </div>
            </div>
        </div>

        <!-- 2. Status Distribution -->
        <div class="chart-card col-span-1">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    🍩 Status Distribution
                </h3>
                <div class="chart-actions-dropdown">
                    <button class="chart-download-btn" title="Download Chart">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    </button>
                    <div class="chart-download-menu">
                        <a href="#" class="download-link" data-format="png">PNG Image</a>
                        <a href="#" class="download-link" data-format="jpeg">JPEG Image</a>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="chartStatusDistribution"></canvas>
                <div id="chartStatusDistribution-empty" class="chart-empty-state" style="display:none;">
                    No Status Data Available
                </div>
            </div>
        </div>

        <!-- 3. Top Consumers -->
        <div class="chart-card col-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    💎 Top Consumers
                </h3>
                <div class="chart-actions-dropdown">
                    <button class="chart-download-btn" title="Download Chart">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    </button>
                    <div class="chart-download-menu">
                        <a href="#" class="download-link" data-format="png">PNG Image</a>
                        <a href="#" class="download-link" data-format="jpeg">JPEG Image</a>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="chartTopConsumers"></canvas>
                <div id="chartTopConsumers-empty" class="chart-empty-state" style="display:none;">
                    No Consumption Data Available
                </div>
            </div>
        </div>

        <!-- 4. Registration Trend -->
        <div class="chart-card col-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    📈 Signups over Time
                </h3>
                <div class="chart-actions-dropdown">
                    <button class="chart-download-btn" title="Download Chart">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    </button>
                    <div class="chart-download-menu">
                        <a href="#" class="download-link" data-format="png">PNG Image</a>
                        <a href="#" class="download-link" data-format="jpeg">JPEG Image</a>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="chartRegistrationTrend"></canvas>
                <div id="chartRegistrationTrend-empty" class="chart-empty-state" style="display:none;">
                    No Registration Trend Available
                </div>
            </div>
        </div>

        <!-- 5. Top Engaged Clients -->
        <div class="chart-card col-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    📊 Top Clients by Total Engagement
                </h3>
                <div class="chart-actions-dropdown">
                    <button class="chart-download-btn" title="Download Chart">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    </button>
                    <div class="chart-download-menu">
                        <a href="#" class="download-link" data-format="png">PNG Image</a>
                        <a href="#" class="download-link" data-format="jpeg">JPEG Image</a>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="chartTopEngagedClients"></canvas>
                <div id="chartTopEngagedClients-empty" class="chart-empty-state" style="display:none;">
                    No Engagement Data Available
                </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const savedView = localStorage.getItem('client_consumption_view') || 'table';
        if (savedView === 'analytics') {
            document.getElementById('table-view-section').classList.add('hidden');
            document.getElementById('analytics-view-section').classList.remove('hidden');
            document.getElementById('toggle-table').classList.remove('active');
            document.getElementById('toggle-analytics').classList.add('active');
        }
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle Modal Drawer Script
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterOverlay = document.getElementById('filterOverlay');

    filterToggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        filterOverlay.style.display = filterOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!filterOverlay.contains(e.target) && e.target !== filterToggleBtn) {
            filterOverlay.style.display = 'none';
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        // Tab switching and view toggling
        const toggleTable = document.getElementById('toggle-table');
        const toggleAnalytics = document.getElementById('toggle-analytics');
        const tableView = document.getElementById('table-view-section');
        const analyticsView = document.getElementById('analytics-view-section');

        const switchView = (showTable) => {
            localStorage.setItem('client_consumption_view', showTable ? 'table' : 'analytics');
            if (showTable) {
                toggleTable.classList.add('active');
                toggleAnalytics.classList.remove('active');
                
                analyticsView.classList.add('hidden');
                tableView.classList.remove('hidden');
                
                setTimeout(() => {
                    tableView.style.opacity = '1';
                    tableView.style.transform = 'translateY(0)';
                }, 50);
            } else {
                toggleAnalytics.classList.add('active');
                toggleTable.classList.remove('active');
                
                tableView.classList.add('hidden');
                analyticsView.classList.remove('hidden');
                
                setTimeout(() => {
                    analyticsView.style.opacity = '1';
                    analyticsView.style.transform = 'translateY(0)';
                }, 50);
            }
        };

        toggleTable.addEventListener('click', () => switchView(true));
        toggleAnalytics.addEventListener('click', () => switchView(false));

        const savedView = localStorage.getItem('client_consumption_view') || 'table';
        switchView(savedView === 'table');

        // Load pre-fetched data object
        const data = @json($analytics);

        const gridColor = '#1c182d';
        const labelColor = '#8c8ea7';
        const chartFont = {
            family: "'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
            size: 11
        };

        const globalOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#110f1b',
                    borderColor: '#1c182d',
                    borderWidth: 1,
                    titleColor: '#f8fafc',
                    bodyColor: '#8c8ea7',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 10,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: chartFont }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: chartFont }
                }
            }
        };

        const checkEmpty = (array, canvasId, emptyStateId) => {
            if (!array || array.length === 0) {
                document.getElementById(canvasId).style.display = 'none';
                document.getElementById(emptyStateId).style.display = 'flex';
                return true;
            }
            return false;
        };

        // Chart 1: Top Consumers (Horizontal Bar)
        if (!checkEmpty(data.topConsumers, 'chartTopConsumers', 'chartTopConsumers-empty')) {
            const horizontalOptions = JSON.parse(JSON.stringify(globalOptions));
            horizontalOptions.indexAxis = 'y';
            horizontalOptions.onHover = (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            };
            const chartConsumers = new Chart(document.getElementById('chartTopConsumers'), {
                type: 'bar',
                data: {
                    labels: data.topConsumers.map(c => c.name),
                    datasets: [{
                        data: data.topConsumers.map(c => c.consumed),
                        backgroundColor: 'rgba(168, 85, 247, 0.75)', // primary purple
                        borderColor: '#a855f7',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barThickness: 16
                    }]
                },
                options: horizontalOptions
            });

            // Click handler to filter search by client
            document.getElementById('chartTopConsumers').onclick = (evt) => {
                const points = chartConsumers.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length > 0) {
                    const index = points[0].index;
                    const clientName = chartConsumers.data.labels[index];
                    const searchEl = document.querySelector('#mainSearchForm input[name="search"]');
                    if (searchEl) {
                       searchEl.value = clientName;
                       searchEl.form.submit();
                    }
                }
            };
        }

        // Chart 2: Plan Distribution (Doughnut)
        if (!checkEmpty(data.planDistribution, 'chartPlanDistribution', 'chartPlanDistribution-empty')) {
            const chartPlanDistributionCanvas = document.getElementById('chartPlanDistribution');
            chartPlanDistributionCanvas.style.cursor = 'pointer';
            const chartPlan = new Chart(chartPlanDistributionCanvas, {
                type: 'doughnut',
                data: {
                    labels: data.planDistribution.map(p => p.label),
                    datasets: [{
                        data: data.planDistribution.map(p => p.count),
                        backgroundColor: [
                            'rgba(168, 85, 247, 0.8)',   // Purple
                            'rgba(99, 102, 241, 0.4)'    // Indigo
                        ],
                        borderColor: '#110f1b',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { color: labelColor, font: chartFont, boxWidth: 10, padding: 15 }
                        },
                        tooltip: globalOptions.plugins.tooltip
                    }
                }
            });

            chartPlanDistributionCanvas.onclick = (evt) => {
                const points = chartPlan.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length > 0) {
                    const index = points[0].index;
                    const planName = chartPlan.data.labels[index];
                    const planInput = document.querySelector('#mainSearchForm input[name="plan"]');
                    if (planInput) {
                        planInput.value = planName;
                        planInput.form.submit();
                    }
                }
            };
        }

        // Chart 3: Status Distribution (Doughnut)
        if (!checkEmpty(data.statusDistribution, 'chartStatusDistribution', 'chartStatusDistribution-empty')) {
            const chartStatusDistributionCanvas = document.getElementById('chartStatusDistribution');
            chartStatusDistributionCanvas.style.cursor = 'pointer';
            const chartStatus = new Chart(chartStatusDistributionCanvas, {
                type: 'doughnut',
                data: {
                    labels: data.statusDistribution.map(s => s.label),
                    datasets: [{
                        data: data.statusDistribution.map(s => s.count),
                        backgroundColor: [
                            'rgba(168, 85, 247, 0.8)',   // Purple
                            'rgba(99, 102, 241, 0.4)'    // Indigo
                        ],
                        borderColor: '#110f1b',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { color: labelColor, font: chartFont, boxWidth: 10, padding: 15 }
                        },
                        tooltip: globalOptions.plugins.tooltip
                    }
                }
            });

            chartStatusDistributionCanvas.onclick = (evt) => {
                const points = chartStatus.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length > 0) {
                    const index = points[0].index;
                    const statusName = chartStatus.data.labels[index];
                    const statusInput = document.querySelector('#mainSearchForm input[name="status"]');
                    if (statusInput) {
                        statusInput.value = statusName;
                        statusInput.form.submit();
                    }
                }
            };
        }

        // Chart 4: Registration Trend (Line)
        if (!checkEmpty(data.registrationTrend, 'chartRegistrationTrend', 'chartRegistrationTrend-empty')) {
            const chartRegistrationTrendCanvas = document.getElementById('chartRegistrationTrend');
            chartRegistrationTrendCanvas.style.cursor = 'pointer';
            const chartTrend = new Chart(chartRegistrationTrendCanvas, {
                type: 'line',
                data: {
                    labels: data.registrationTrend.map(r => r.date),
                    datasets: [{
                        data: data.registrationTrend.map(r => r.count),
                        fill: true,
                        backgroundColor: 'rgba(168, 85, 247, 0.04)',
                        borderColor: '#a855f7', // primary purple
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#a855f7'
                    }]
                },
                options: {
                    ...globalOptions,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    }
                }
            });

            chartRegistrationTrendCanvas.onclick = (evt) => {
                const points = chartTrend.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length > 0) {
                    const index = points[0].index;
                    const dateVal = chartTrend.data.labels[index];
                    const fromInput = document.querySelector('#mainSearchForm input[name="post_created_from"]');
                    const toInput = document.querySelector('#mainSearchForm input[name="post_created_to"]');
                    if (fromInput && toInput) {
                        fromInput.value = dateVal;
                        toInput.value = dateVal;
                        fromInput.form.submit();
                    }
                }
            };
        }

        // Chart 5: Top Engaged Clients (Bar Chart)
        if (!checkEmpty(data.topEngagedClients, 'chartTopEngagedClients', 'chartTopEngagedClients-empty')) {
            const verticalOptions = JSON.parse(JSON.stringify(globalOptions));
            verticalOptions.onHover = (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            };
            const chartEngaged = new Chart(document.getElementById('chartTopEngagedClients'), {
                type: 'bar',
                data: {
                    labels: data.topEngagedClients.map(c => c.name),
                    datasets: [{
                        data: data.topEngagedClients.map(c => c.engagement),
                        backgroundColor: 'rgba(139, 92, 246, 0.75)', // violet/purple
                        borderColor: '#8b5cf6',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barThickness: 24
                    }]
                },
                options: verticalOptions
            });

            // Click handler to search by client
            document.getElementById('chartTopEngagedClients').onclick = (evt) => {
                const points = chartEngaged.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length > 0) {
                    const index = points[0].index;
                    const clientName = chartEngaged.data.labels[index];
                    const searchEl = document.querySelector('#mainSearchForm input[name="search"]');
                    if (searchEl) {
                        searchEl.value = clientName;
                        searchEl.form.submit();
                    }
                }
            };
        }

        // Chart Download Dropdown & Trigger Logic
        document.querySelectorAll('.chart-download-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = btn.nextElementSibling;
                document.querySelectorAll('.chart-download-menu').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });
                menu.classList.toggle('show');
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.chart-download-menu').forEach(m => {
                m.classList.remove('show');
            });
        });

        document.querySelectorAll('.download-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const format = link.getAttribute('data-format');
                const card = link.closest('.chart-card');
                const canvas = card.querySelector('canvas');
                const titleEl = card.querySelector('.chart-card-title');
                
                let title = 'chart';
                if (titleEl) {
                    title = titleEl.textContent.trim()
                        .replace(/[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/g, '') // remove emojis
                        .trim()
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '_');
                }
                
                if (!canvas) {
                    const funnelContainer = card.querySelector('.funnel-container');
                    if (funnelContainer) {
                        downloadFunnel(funnelContainer, title, format);
                    }
                    return;
                }
                
                downloadCanvas(canvas, title, format);
            });
        });

        function downloadCanvas(canvas, filename, format) {
            let dataUrl;
            if (format === 'jpeg') {
                const offscreen = document.createElement('canvas');
                offscreen.width = canvas.width;
                offscreen.height = canvas.height;
                const ctx = offscreen.getContext('2d');
                ctx.fillStyle = '#0d0e15';
                ctx.fillRect(0, 0, offscreen.width, offscreen.height);
                ctx.drawImage(canvas, 0, 0);
                dataUrl = offscreen.toDataURL('image/jpeg', 0.95);
            } else {
                dataUrl = canvas.toDataURL('image/png');
            }
            
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = `${filename}.${format}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function downloadFunnel(container, filename, format) {
            const steps = container.querySelectorAll('.funnel-step');
            if (steps.length === 0) return;
            
            const canvas = document.createElement('canvas');
            canvas.width = 800;
            canvas.height = 400;
            const ctx = canvas.getContext('2d');
            
            if (format === 'jpeg') {
                ctx.fillStyle = '#0d0e15';
            } else {
                ctx.fillStyle = 'rgba(13, 14, 21, 0)';
            }
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            const paddingX = 40;
            const paddingY = 30;
            const availableHeight = canvas.height - paddingY * 2;
            const gap = 20;
            const stepHeight = (availableHeight - gap * (steps.length - 1)) / steps.length;
            
            steps.forEach((step, idx) => {
                const y = paddingY + idx * (stepHeight + gap);
                const w = canvas.width - paddingX * 2;
                
                let bgColor = 'rgba(59, 130, 246, 0.06)';
                let borderColor = 'rgba(59, 130, 246, 0.15)';
                let textColor = '#93c5fd';
                let badgeBg = 'transparent';
                let badgeTextColor = 'transparent';
                
                if (step.classList.contains('funnel-step-1')) {
                    bgColor = 'rgba(59, 130, 246, 0.08)';
                    borderColor = 'rgba(59, 130, 246, 0.25)';
                    textColor = '#93c5fd';
                } else if (step.classList.contains('funnel-step-2')) {
                    bgColor = 'rgba(16, 185, 129, 0.08)';
                    borderColor = 'rgba(16, 185, 129, 0.25)';
                    textColor = '#6ee7b7';
                    badgeBg = 'rgba(16, 185, 129, 0.18)';
                    badgeTextColor = '#10b981';
                } else if (step.classList.contains('funnel-step-3')) {
                    bgColor = 'rgba(236, 72, 153, 0.08)';
                    borderColor = 'rgba(236, 72, 153, 0.25)';
                    textColor = '#fbcfe8';
                    badgeBg = 'rgba(236, 72, 153, 0.18)';
                    badgeTextColor = '#f472b6';
                }
                
                drawRoundedRect(ctx, paddingX, y, w, stepHeight, 10, bgColor, borderColor, 1.5);
                
                const labelEl = step.querySelector('.funnel-step-label');
                const valEl = step.querySelector('.funnel-step-val');
                const badgeEl = step.querySelector('.funnel-step-badge');
                
                const label = labelEl ? labelEl.textContent.trim() : '';
                const val = valEl ? valEl.textContent.trim() : '';
                const badge = badgeEl ? badgeEl.textContent.trim() : '';
                
                ctx.textBaseline = 'middle';
                
                ctx.font = 'bold 18px "Plus Jakarta Sans", "Inter", sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText(label, paddingX + 24, y + stepHeight / 2);
                
                if (badge && badgeEl) {
                    const labelWidth = ctx.measureText(label).width;
                    const badgeX = paddingX + 24 + labelWidth + 15;
                    ctx.font = 'bold 13px "Plus Jakarta Sans", "Inter", sans-serif';
                    const badgeWidth = ctx.measureText(badge).width;
                    const badgeHeight = 24;
                    const badgeY = y + (stepHeight - badgeHeight) / 2;
                    
                    drawRoundedRect(ctx, badgeX, badgeY, badgeWidth + 16, badgeHeight, 12, badgeBg, 'transparent');
                    
                    ctx.fillStyle = badgeTextColor;
                    ctx.fillText(badge, badgeX + 8, badgeY + badgeHeight / 2);
                }
                
                ctx.font = 'bold 20px "Plus Jakarta Sans", "Inter", sans-serif';
                ctx.fillStyle = textColor;
                ctx.textAlign = 'right';
                ctx.fillText(val, paddingX + w - 24, y + stepHeight / 2);
                ctx.textAlign = 'left';
            });
            
            function drawRoundedRect(c, x, y, width, height, radius, fill, stroke, strokeWidth = 1) {
                c.beginPath();
                c.moveTo(x + radius, y);
                c.lineTo(x + width - radius, y);
                c.quadraticCurveTo(x + width, y, x + width, y + radius);
                c.lineTo(x + width, y + height - radius);
                c.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                c.lineTo(x + radius, y + height - radius);
                c.quadraticCurveTo(x, y + height, x, y + height - radius);
                c.lineTo(x, y + radius);
                c.quadraticCurveTo(x, y, x + radius, y);
                c.closePath();
                if (fill && fill !== 'transparent') {
                    c.fillStyle = fill;
                    c.fill();
                }
                if (stroke && stroke !== 'transparent') {
                    c.strokeStyle = stroke;
                    c.lineWidth = strokeWidth;
                    c.stroke();
                }
            }
            
            const dataUrl = canvas.toDataURL(format === 'jpeg' ? 'image/jpeg' : 'image/png', format === 'jpeg' ? 0.95 : undefined);
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = `${filename}.${format}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Section Download Event Listener
        document.querySelectorAll('.section-download-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const section = link.getAttribute('data-section');
                const format = link.getAttribute('data-format');
                downloadSection(section, format);
            });
        });

        function downloadSection(sectionName, format) {
            let cards = [];
            let columnCount = 2;
            let titleText = "";
            
            if (sectionName === 'visual-analytics') {
                const section = document.getElementById('analytics-view-section');
                cards = Array.from(section.querySelectorAll('.chart-card'));
                columnCount = 4;
                titleText = "Visual Analytics Overview";
            } else if (sectionName === 'client-summaries-analytics') {
                const section = document.querySelector('#analytics-view-section');
                const grid = section.querySelectorAll('.analytics-grid')[0];
                cards = Array.from(grid.querySelectorAll('.chart-card'));
                columnCount = 2;
                titleText = "Client Summaries Analytics";
            } else if (sectionName === 'removed-campaigns-analytics') {
                const section = document.querySelector('#analytics-view-section');
                const grid = section.querySelectorAll('.analytics-grid')[1];
                cards = Array.from(grid.querySelectorAll('.chart-card'));
                columnCount = 2;
                titleText = "Removed Campaigns Analytics";
            }
            
            if (cards.length === 0) return;
            
            const cardWidth = 500;
            const cardHeight = 320;
            const gap = 20;
            const padding = 30;
            const headerHeight = 70;
            
            let layout = [];
            let rowCount = 0;
            
            if (sectionName === 'visual-analytics') {
                layout = [
                    { card: cards[0], col: 0, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[1], col: 1, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[2], col: 2, row: 0, colspan: 2, rowspan: 1 },
                    { card: cards[3], col: 0, row: 1, colspan: 2, rowspan: 1 },
                    { card: cards[4], col: 2, row: 1, colspan: 2, rowspan: 1 }
                ];
                rowCount = 2;
            } else if (sectionName === 'client-summaries-analytics') {
                layout = [
                    { card: cards[0], col: 0, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[1], col: 1, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[2], col: 0, row: 1, colspan: 1, rowspan: 1 },
                    { card: cards[3], col: 1, row: 1, colspan: 1, rowspan: 1 },
                    { card: cards[4], col: 0, row: 2, colspan: 1, rowspan: 1 },
                    { card: cards[5], col: 1, row: 2, colspan: 1, rowspan: 1 }
                ];
                rowCount = 3;
            } else if (sectionName === 'removed-campaigns-analytics') {
                layout = [
                    { card: cards[0], col: 0, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[1], col: 1, row: 0, colspan: 1, rowspan: 1 },
                    { card: cards[2], col: 0, row: 1, colspan: 1, rowspan: 1 },
                    { card: cards[4], col: 1, row: 1, colspan: 1, rowspan: 1 },
                    { card: cards[3], col: 0, row: 2, colspan: 2, rowspan: 1 }
                ];
                rowCount = 3;
            }
            
            const totalWidth = columnCount * cardWidth + (columnCount - 1) * gap + padding * 2;
            const totalHeight = headerHeight + rowCount * cardHeight + (rowCount - 1) * gap + padding * 2;
            
            const canvas = document.createElement('canvas');
            canvas.width = totalWidth;
            canvas.height = totalHeight;
            const ctx = canvas.getContext('2d');
            
            ctx.fillStyle = '#08090f';
            ctx.fillRect(0, 0, totalWidth, totalHeight);
            
            ctx.textBaseline = 'middle';
            ctx.font = 'bold 24px "Plus Jakarta Sans", "Inter", sans-serif';
            ctx.fillStyle = '#f8fafc';
            ctx.fillText(titleText, padding, padding + headerHeight / 2 - 10);
            
            ctx.strokeStyle = '#181b28';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(padding, padding + headerHeight - 15);
            ctx.lineTo(totalWidth - padding, padding + headerHeight - 15);
            ctx.stroke();
            
            layout.forEach(pos => {
                const colWidth = (pos.colspan * cardWidth) + (pos.colspan - 1) * gap;
                const colHeight = (pos.rowspan * cardHeight) + (pos.rowspan - 1) * gap;
                const x = padding + pos.col * (cardWidth + gap);
                const y = padding + headerHeight + pos.row * (cardHeight + gap);
                
                drawCardToContext(ctx, pos.card, x, y, colWidth, colHeight);
            });
            
            function drawRoundedRect(c, x, y, width, height, radius, fill, stroke, strokeWidth = 1) {
                c.beginPath();
                c.moveTo(x + radius, y);
                c.lineTo(x + width - radius, y);
                c.quadraticCurveTo(x + width, y, x + width, y + radius);
                c.lineTo(x + width, y + height - radius);
                c.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                c.lineTo(x + radius, y + height - radius);
                c.quadraticCurveTo(x, y + height, x, y + height - radius);
                c.lineTo(x, y + radius);
                c.quadraticCurveTo(x, y, x + radius, y);
                c.closePath();
                if (fill && fill !== 'transparent') {
                    c.fillStyle = fill;
                    c.fill();
                }
                if (stroke && stroke !== 'transparent') {
                    c.strokeStyle = stroke;
                    c.lineWidth = strokeWidth;
                    c.stroke();
                }
            }
            
            function drawCardToContext(c, card, cx, cy, cw, ch) {
                drawRoundedRect(c, cx, cy, cw, ch, 14, '#0d0e15', '#181b28', 1.5);
                
                const titleEl = card.querySelector('.chart-card-title');
                let title = '';
                let subtitle = '';
                
                if (titleEl) {
                    const span = titleEl.querySelector('span');
                    if (span) {
                        subtitle = span.textContent.trim();
                        let temp = titleEl.cloneNode(true);
                        const s = temp.querySelector('span');
                        if (s) s.remove();
                        title = temp.textContent.trim();
                    } else {
                        title = titleEl.textContent.trim();
                    }
                }
                
                c.textBaseline = 'top';
                c.font = 'bold 16px "Plus Jakarta Sans", "Inter", sans-serif';
                c.fillStyle = '#f8fafc';
                c.fillText(title, cx + 24, cy + 20);
                
                if (subtitle) {
                    c.font = 'normal 12px "Plus Jakarta Sans", "Inter", sans-serif';
                    c.fillStyle = '#8c8ea7';
                    c.textAlign = 'right';
                    c.fillText(subtitle, cx + cw - 24, cy + 22);
                    c.textAlign = 'left';
                }
                
                const chartCanvas = card.querySelector('canvas');
                if (chartCanvas) {
                    const destY = cy + 55;
                    const destH = ch - 75;
                    const destW = cw - 48;
                    const destX = cx + 24;
                    c.drawImage(chartCanvas, destX, destY, destW, destH);
                } else {
                    const funnel = card.querySelector('.funnel-container');
                    if (funnel) {
                        const steps = funnel.querySelectorAll('.funnel-step');
                        const paddingX = 24;
                        const paddingY = 60;
                        const availableHeight = ch - paddingY - 24;
                        const gap = 15;
                        const stepHeight = (availableHeight - gap * (steps.length - 1)) / steps.length;
                        
                        steps.forEach((step, idx) => {
                            const y = cy + paddingY + idx * (stepHeight + gap);
                            const w = cw - paddingX * 2;
                            const x = cx + paddingX;
                            
                            let bgColor = 'rgba(59, 130, 246, 0.06)';
                            let borderColor = 'rgba(59, 130, 246, 0.15)';
                            let textColor = '#93c5fd';
                            let badgeBg = 'transparent';
                            let badgeTextColor = 'transparent';
                            
                            if (step.classList.contains('funnel-step-1')) {
                                bgColor = 'rgba(59, 130, 246, 0.08)';
                                borderColor = 'rgba(59, 130, 246, 0.25)';
                                textColor = '#93c5fd';
                            } else if (step.classList.contains('funnel-step-2')) {
                                bgColor = 'rgba(16, 185, 129, 0.08)';
                                borderColor = 'rgba(16, 185, 129, 0.25)';
                                textColor = '#6ee7b7';
                                badgeBg = 'rgba(16, 185, 129, 0.18)';
                                badgeTextColor = '#10b981';
                            } else if (step.classList.contains('funnel-step-3')) {
                                bgColor = 'rgba(236, 72, 153, 0.08)';
                                borderColor = 'rgba(236, 72, 153, 0.25)';
                                textColor = '#fbcfe8';
                                badgeBg = 'rgba(236, 72, 153, 0.18)';
                                badgeTextColor = '#f472b6';
                            }
                            
                            drawRoundedRect(c, x, y, w, stepHeight, 8, bgColor, borderColor, 1);
                            
                            const labelEl = step.querySelector('.funnel-step-label');
                            const valEl = step.querySelector('.funnel-step-val');
                            const badgeEl = step.querySelector('.funnel-step-badge');
                            
                            const label = labelEl ? labelEl.textContent.trim() : '';
                            const val = valEl ? valEl.textContent.trim() : '';
                            const badge = badgeEl ? badgeEl.textContent.trim() : '';
                            
                            c.textBaseline = 'middle';
                            
                            c.font = 'bold 13px "Plus Jakarta Sans", "Inter", sans-serif';
                            c.fillStyle = textColor;
                            c.fillText(label, x + 16, y + stepHeight / 2);
                            
                            if (badge && badgeEl) {
                                const labelWidth = c.measureText(label).width;
                                const badgeX = x + 16 + labelWidth + 10;
                                c.font = 'bold 10px "Plus Jakarta Sans", "Inter", sans-serif';
                                const badgeWidth = c.measureText(badge).width;
                                const badgeHeight = 18;
                                const badgeY = y + (stepHeight - badgeHeight) / 2;
                                
                                drawRoundedRect(c, badgeX, badgeY, badgeWidth + 10, badgeHeight, 9, badgeBg, 'transparent');
                                
                                c.fillStyle = badgeTextColor;
                                c.fillText(badge, badgeX + 5, badgeY + badgeHeight / 2);
                            }
                            
                            c.font = 'bold 15px "Plus Jakarta Sans", "Inter", sans-serif';
                            c.fillStyle = textColor;
                            c.textAlign = 'right';
                            c.fillText(val, x + w - 16, y + stepHeight / 2);
                            c.textAlign = 'left';
                        });
                    }
                }
            }
            
            const filenameText = titleText.toLowerCase().replace(/[^a-z0-9]+/g, '_');
            const dataUrl = canvas.toDataURL(format === 'jpeg' ? 'image/jpeg' : 'image/png', format === 'jpeg' ? 0.95 : undefined);
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = `${filenameText}.${format}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });
</script>
@endsection
