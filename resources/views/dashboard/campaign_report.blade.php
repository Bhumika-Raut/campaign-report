@extends('layouts.layout')

@section('main')
<style>
    /* Section Headers */
    .section-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2.5rem;
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

    /* Filter Section & Overlay styling */
    .filter-overlay {
        position: absolute;
        top: 55px;
        right: 0;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.75rem;
        border-radius: 12px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6);
        z-index: 150;
        width: 380px;
        display: none;
        max-height: 80vh;
        overflow-y: auto;
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

    /* View Switcher */
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

    /* KPI Cards Grid */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    /* Charts Grid */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    @media (max-width: 1200px) {
        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (min-width: 769px) {
        .lg-span-2 {
            grid-column: span 2;
        }
    }

    .chart-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        min-height: 280px;
    }

    .chart-card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 0;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-container {
        position: relative;
        flex-grow: 1;
        height: 190px;
        width: 100%;
    }

    .chart-empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        background-color: rgba(255, 255, 255, 0.01);
        border: 1px dashed var(--border-color);
        border-radius: 8px;
    }

    /* Conversion Funnel */
    .funnel-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        justify-content: center;
        height: 100%;
        flex-grow: 1;
    }

    .funnel-step {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid transparent;
        overflow: hidden;
    }

    .funnel-step-bg {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .funnel-step-label, .funnel-step-val, .funnel-step-badge {
        position: relative;
        z-index: 2;
    }

    .funnel-step-1 {
        background-color: rgba(59, 130, 246, 0.06);
        border-color: rgba(59, 130, 246, 0.15);
        color: #93c5fd;
    }
    .funnel-step-1 .funnel-step-bg {
        background-color: rgba(59, 130, 246, 0.06);
    }

    .funnel-step-2 {
        background-color: rgba(16, 185, 129, 0.06);
        border-color: rgba(16, 185, 129, 0.15);
        color: #6ee7b7;
    }
    .funnel-step-2 .funnel-step-bg {
        background-color: rgba(16, 185, 129, 0.06);
    }

    .funnel-step-3 {
        background-color: rgba(236, 72, 153, 0.06);
        border-color: rgba(236, 72, 153, 0.15);
        color: #fbcfe8;
    }
    .funnel-step-3 .funnel-step-bg {
        background-color: rgba(236, 72, 153, 0.06);
    }

    .funnel-step-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.625rem;
        border-radius: 12px;
    }
    .funnel-step-2 .funnel-step-badge {
        background-color: rgba(16, 185, 129, 0.18);
        color: var(--color-success);
    }
    .funnel-step-3 .funnel-step-badge {
        background-color: rgba(236, 72, 153, 0.18);
        color: #f472b6;
    }

    .fade-section {
        transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
    }
    .fade-section.hidden {
        display: none !important;
        opacity: 0;
        transform: translateY(10px);
    }

    /* Filter button style */
    .filter-btn {
        background: linear-gradient(135deg, var(--color-primary) 0%, #1d4ed8 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 0.625rem 1.75rem;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        transition: all 0.2s;
    }
    .filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(59, 130, 246, 0.35);
    }

    /* Loader styling */
    .spinner {
        display: inline-block;
        width: 1.25rem;
        height: 1.25rem;
        border: 2.5px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Searchable Dropdown Styling */
    .searchable-select-container {
        position: relative;
    }

    .searchable-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-top: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }

    .searchable-option {
        padding: 0.625rem 1rem;
        cursor: pointer;
        color: #d1d5db;
        font-size: 0.875rem;
        transition: background-color 0.15s, color 0.15s;
    }

    .searchable-option:hover {
        background-color: var(--color-primary);
        color: white;
    }

    .searchable-option.no-results {
        color: var(--text-muted);
        cursor: default;
        text-align: center;
        padding: 1rem;
    }
    .searchable-option.no-results:hover {
        background-color: transparent;
        color: var(--text-muted);
    }

    .client-campaigns-panel {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.5rem;
    }

    .client-campaigns-panel .client-campaigns-search {
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 0.5rem;
    }

    .client-campaigns-list {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        max-height: 180px;
        overflow-y: auto;
    }

    .client-campaign-item.active {
        background-color: rgba(59, 130, 246, 0.15);
        border-color: var(--color-primary);
        color: white;
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
    #columnsDropdownMenu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: auto;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        min-width: 180px;
        padding: 0.5rem 0;
        margin-top: 4px;
    }
    #columnsDropdownMenu.show {
        display: block;
    }
    .column-toggle-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.625rem 1rem;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.15s, color 0.15s;
        user-select: none;
        background-color: #ffffff;
    }
    .column-toggle-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .column-toggle-item .checkmark {
        color: #0f172a;
        font-weight: bold;
        font-size: 0.95rem;
        visibility: hidden;
    }
    .column-toggle-item.active .checkmark {
        visibility: visible;
    }
</style>

<!-- Control Bar with Search and Filter Toggle -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem; position: relative;">
    <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M12.438 1.667a.5.5 0 0 1 .416.708L10.14 7.95a.5.5 0 0 0-.083.275v5.108a.5.5 0 0 1-.76.426l-1.888-1.133a.5.5 0 0 1-.24-.426V8.225a.5.5 0 0 0-.083-.275L4.32 2.375a.5.5 0 0 1 .416-.708h7.701zM3.5 1h9a1 1 0 0 1 .832 1.415L10.5 7.63v4.696a1 1 0 0 1-.48.858l-1.888 1.133A1 1 0 0 1 7 13.458V7.63L4.168 2.415A1 1 0 0 1 5 1h9z"/></svg>
        Campaign Reports
    </div>

    <div style="display: flex; gap: 1rem; align-items: center; position: relative; z-index: 100;">
        <!-- Search form -->
        <form method="GET" action="{{ route('campaign-report') }}" style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="hidden" name="quick_select" value="{{ request('quick_select') }}">
            <input type="hidden" name="from" value="{{ $from }}">
            <input type="hidden" name="to" value="{{ $to }}">
            <input type="hidden" name="client_name" value="{{ request('client_name') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <input type="hidden" name="campaign_date" value="{{ request('campaign_date') }}">
            <input type="hidden" name="campaign_id" value="{{ request('campaign_id') }}">
            <input type="hidden" name="campaign_name" value="{{ request('campaign_name') }}">
            <input type="hidden" name="paid_or_not" value="{{ request('paid_or_not') }}">
            <input type="hidden" name="custom_condition" value="{{ request('custom_condition') }}">
            
            <label style="color: var(--text-primary); font-size: 0.875rem; font-weight: 500;">Search:</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaign, client..." class="form-control" style="width: 220px;" onkeypress="if(event.key === 'Enter') this.form.submit();">
        </form>

        <button class="btn btn-outline" id="filterToggleBtn" style="gap: 0.375rem;">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-.124.318l-3.978 4.773a1 1 0 0 0-.242.623V13a1 1 0 0 1-.553.894l-2 1A1 1 0 0 1 6 13.9v-4.3a1 1 0 0 0-.242-.623L1.782 4.318a.5.5 0 0 1-.124-.318v-2.5z"/></svg>
            Filters
            <svg width="10" height="10" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.125rem;"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>
        </button>

        <!-- Filter Overlay Drawer (matches Client Consumption drawer style) -->
        <div class="filter-overlay" id="filterOverlay">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <span style="font-weight: 700; font-size: 0.9rem; letter-spacing: 0.02em;">FILTER SEARCH</span>
                <a href="{{ route('campaign-report') }}" style="color: #a855f7; text-decoration: none; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">RESET ALL</a>
            </div>

            <form method="GET" action="{{ route('campaign-report') }}" id="drawerFilterForm">
                <!-- Preserve search value -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="quick_select" value="{{ request('quick_select') }}">
                <input type="hidden" name="from" value="{{ $from }}">
                <input type="hidden" name="to" value="{{ $to }}">

                <!-- Client Select -->
                <div class="form-group">
                    <label>Client Name</label>
                    <div class="searchable-select-container">
                        <input type="text" name="client_name" value="{{ request('client_name') }}" placeholder="All Clients" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Clients</div>
                            @foreach($uniqueClients as $uc)
                                <div class="searchable-option" data-value="{{ $uc }}">{{ $uc }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Dynamic Client Campaigns List -->
                <div class="form-group" id="client-campaigns-group" style="display: none;">
                    <label style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: 0.05em; display: block;">Select Campaign</label>
                    <div class="client-campaigns-panel">
                        <input type="text" id="client-campaigns-search" class="form-control client-campaigns-search" placeholder="Search campaigns for this client..." autocomplete="off">
                        <div id="client-campaigns-list" class="client-campaigns-list">
                            <!-- JS populated items -->
                        </div>
                    </div>
                </div>

                <!-- Email Select -->
                <div class="form-group">
                    <label>Email</label>
                    <div class="searchable-select-container">
                        <input type="text" name="email" value="{{ request('email') }}" placeholder="All Emails" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Emails</div>
                            @foreach($uniqueEmails as $ue)
                                <div class="searchable-option" data-value="{{ $ue }}">{{ $ue }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Specific Campaign Date -->
                <div class="form-group">
                    <label>Specific Date</label>
                    <div class="searchable-select-container">
                        <input type="text" name="campaign_date" value="{{ request('campaign_date') }}" placeholder="All Dates" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Dates</div>
                            @foreach($uniqueDates as $ud)
                                <div class="searchable-option" data-value="{{ $ud->toDateString() }}">{{ $ud->toDateString() }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Campaign ID Select -->
                <div class="form-group">
                    <label>Campaign ID</label>
                    <div class="searchable-select-container">
                        <input type="text" name="campaign_id" value="{{ request('campaign_id') }}" placeholder="All Campaign IDs" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Campaign IDs</div>
                            @foreach($uniqueCampaignIds as $campObj)
                                <div class="searchable-option" data-value="{{ $campObj->id }}" data-client="{{ $campObj->client_name }}">{{ $campObj->id }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Campaign Name Select -->
                <div class="form-group">
                    <label>Campaign Name</label>
                    <div class="searchable-select-container">
                        <input type="text" name="campaign_name" value="{{ request('campaign_name') }}" placeholder="All Campaign Names" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Campaign Names</div>
                            @foreach($uniqueCampaignNames as $campObj)
                                <div class="searchable-option" data-value="{{ $campObj->campaign_title }}" data-client="{{ $campObj->client_name }}">{{ $campObj->campaign_title }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Paid Status Select -->
                <div class="form-group">
                    <label>Paid Status</label>
                    <div class="searchable-select-container">
                        <input type="text" name="paid_or_not" value="{{ request('paid_or_not') }}" placeholder="All Paid Statuses" class="form-control searchable-input" autocomplete="off" style="width: 100%; box-sizing: border-box;">
                        <div class="searchable-dropdown">
                            <div class="searchable-option" data-value="">All Paid Statuses</div>
                            @foreach($uniquePaidStatuses as $ups)
                                <div class="searchable-option" data-value="{{ $ups }}">{{ $ups }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Custom Query/Metric Condition -->
                <div class="form-group">
                    <label>Custom Metric Condition</label>
                    <input type="text" name="custom_condition" value="{{ request('custom_condition') }}" placeholder="e.g. likes > 4" class="form-control" style="width: 100%; box-sizing: border-box;">
                    <div style="font-size: 0.675rem; color: var(--text-muted); margin-top: 0.35rem; line-height: 1.3;">
                        Format: <code>column operator value</code>. Examples:<br>
                        • <code>likes = 4</code> (or <code>total_likes = 4</code>)<br>
                        • <code>shares > 100</code><br>
                        • <code>reach >= 5000</code><br>
                        • <code>roi > 1.5</code><br>
                        • <code>registrations > 10</code>
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; justify-content: center; margin-top: 1.5rem;">Apply Parameters</button>
            </form>
        </div>
    </div>
</div>

<!-- Inline Filter Bar matching the screenshot theme -->
<form method="GET" action="{{ route('campaign-report') }}" id="inlineFilterForm">
    <!-- Preserve all other search parameters -->
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="client_name" value="{{ request('client_name') }}">
    <input type="hidden" name="email" value="{{ request('email') }}">
    <input type="hidden" name="campaign_date" value="{{ request('campaign_date') }}">
    <input type="hidden" name="campaign_id" value="{{ request('campaign_id') }}">
    <input type="hidden" name="campaign_name" value="{{ request('campaign_name') }}">
    <input type="hidden" name="paid_or_not" value="{{ request('paid_or_not') }}">
    <input type="hidden" name="custom_condition" value="{{ request('custom_condition') }}">
    
    <!-- Active Quick Select parameter -->
    <input type="hidden" name="quick_select" id="hiddenQuickSelect" value="{{ request('quick_select', 'This Week') }}">
    
    <div class="filter-bar-inline">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; width: 100%;">
            <div class="filter-group-inline">
                <label>Quick Select</label>
                <div class="capsule-group">
                    <button type="button" class="capsule-btn {{ request('quick_select') === 'This Week' || (!request()->has('quick_select') && !request()->has('from')) ? 'active' : '' }}" data-value="This Week">This Week</button>
                    <button type="button" class="capsule-btn {{ request('quick_select') === 'Last Week' ? 'active' : '' }}" data-value="Last Week">Last Week</button>
                    <button type="button" class="capsule-btn {{ request('quick_select') === 'This Month' ? 'active' : '' }}" data-value="This Month">This Month</button>
                    <button type="button" class="capsule-btn {{ request('quick_select') === 'Last Month' ? 'active' : '' }}" data-value="Last Month">Last Month</button>
                    <button type="button" class="capsule-btn {{ request('quick_select') === 'All Time' ? 'active' : '' }}" data-value="All Time">All Time</button>
                </div>
            </div>
            
            <div class="filter-group-inline" style="flex-grow: 1;">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; justify-content: flex-end;">
                    <div class="filter-group-inline">
                        <label>From</label>
                        <input type="date" name="from" id="inlineFromInput" value="{{ request()->filled('from') ? $from : '' }}" class="form-control" style="padding: 0.4rem 0.8rem; height: 36px; box-sizing: border-box; line-height: 1;">
                    </div>
                    <div class="filter-group-inline">
                        <label>To</label>
                        <input type="date" name="to" id="inlineToInput" value="{{ request()->filled('to') ? $to : '' }}" class="form-control" style="padding: 0.4rem 0.8rem; height: 36px; box-sizing: border-box; line-height: 1;">
                    </div>
                    <div class="filter-group-inline" style="justify-content: flex-end; height: 100%; margin-top: auto; padding-top: 1.2rem;">
                        <button type="submit" class="btn" style="padding: 0.4rem 1.25rem; height: 36px; font-size: 0.75rem; border-radius: 6px; font-weight: 600;">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- KPI Summary Stat Cards Grid -->
<div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2.5rem;">
    <!-- Card 1: Clients -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($clientsCount) }}</div>
            <div class="stat-card-premium-label">Clients</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="16"></line><line x1="15" y1="22" x2="15" y2="16"></line><line x1="9" y1="16" x2="15" y2="16"></line><path d="M9 6h.01"></path><path d="M15 6h.01"></path><path d="M9 10h.01"></path><path d="M15 10h.01"></path></svg>
        </div>
    </div>
    
    <!-- Card 2: Total Campaigns -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($summaryCampaignsCount > 0 ? $summaryCampaignsCount : $campaigns->total()) }}</div>
            <div class="stat-card-premium-label">Total Campaigns</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 11h4l7 5V3L6 8H2v3z"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
        </div>
    </div>
    
    <!-- Card 3: Total Shares -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($summarySharesCount > 0 ? $summarySharesCount : $totalShares) }}</div>
            <div class="stat-card-premium-label">Total Shares</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
        </div>
    </div>
    
    <!-- Card 4: Form Submissions -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($summarySubmissionsCount > 0 ? $summarySubmissionsCount : $totalSubmissions) }}</div>
            <div class="stat-card-premium-label">Form Submissions</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg>
        </div>
    </div>
    
    <!-- Card 5: Credits Consumed -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($creditsConsumed, 2) }}</div>
            <div class="stat-card-premium-label">Credits Consumed</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5"></path><path d="M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6"></path></svg>
        </div>
    </div>
    
    <!-- Card 6: Potential Reach -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalReach) }}</div>
            <div class="stat-card-premium-label">Potential Reach</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </div>
    </div>
    
    <!-- Card 7: Total Likes -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalLikes) }}</div>
            <div class="stat-card-premium-label">Total Likes</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
        </div>
    </div>
    
    <!-- Card 8: Total Clicks -->
    <div class="stat-card-premium">
        <div class="stat-card-premium-left">
            <div class="stat-card-premium-value">{{ number_format($totalClicks) }}</div>
            <div class="stat-card-premium-label">Total Clicks</div>
        </div>
        <div class="stat-card-premium-right">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"></path><path d="M13 13l6 6"></path></svg>
        </div>
    </div>
</div>

<!-- Global Switcher: Table View vs Visual Analytics -->
<div class="view-toggle-container">
    <div class="view-toggle">
        <button id="toggle-table" class="view-toggle-btn active">Table View</button>
        <button id="toggle-analytics" class="view-toggle-btn">Visual Analytics</button>
    </div>
</div>

<!-- ==================== VIEW 1: TABLE VIEW ==================== -->
<div id="table-view-section" class="fade-section">
    <!-- Section A: Removed Campaigns Table -->
    <div class="section-title-bar" style="margin-top: 1.5rem;">
        <div class="section-title">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0V9z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg>
            Campaigns
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; position: relative;">
            <!-- Columns Dropdown -->
            <div style="position: relative; display: inline-block;">
                <button class="btn btn-outline" id="columnsDropdownBtn" style="padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; gap: 0.375rem; height: 34px; display: inline-flex; align-items: center; box-sizing: border-box;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.125rem;">
                        <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13zM1.5 1a.5.5 0 0 0-.5.5V5h4V1H1.5zM5 6H1v4h4V6zm0 5H1v3.5a.5.5 0 0 0 .5.5H5v-4zm1-5h4v4H6V6zm5 0h4v4h-4V6zm4-1V1.5a.5.5 0 0 0-.5-.5H11v4h4zm0 6h-4v4h3.5a.5.5 0 0 0 .5-.5V11zm-5 4h-4v-4h4v4zM6 5h4V1H6v4zm5-4v4h4V1h-4z"/>
                    </svg>
                    Columns
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.125rem;"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>
                </button>
                <div id="columnsDropdownMenu">
                    <div class="column-toggle-item active" data-column="1"><span>Campaign ID</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="2"><span>Client Name</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="3"><span>Company</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="4"><span>Campaign Title</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="5"><span>Paid</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="6"><span>Reach</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="7"><span>Shares</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="8"><span>Clicks</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="9"><span>Comments</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="10"><span>Likes</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="11"><span>Posts</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="12"><span>No. of Forms</span><span class="checkmark">✓</span></div>
                    <div class="column-toggle-item active" data-column="13"><span>ROI</span><span class="checkmark">✓</span></div>
                </div>
            </div>
            
            <!-- Export CSV Button -->
            <a href="{{ route('export.campaigns') }}" class="btn btn-outline" style="padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; gap: 0.25rem; height: 34px; box-sizing: border-box; display: inline-flex; align-items: center;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Removed Campaigns Table Card -->
    <div class="card" style="margin-bottom: 2.5rem;">
        <div style="overflow-x: auto;">
            <table id="campaignsTable">
                <thead>
                    <tr>
                        <th>Campaign ID</th>
                        <th>Client Name</th>
                        <th>Company</th>
                        <th>Campaign Title</th>
                        <th>Paid</th>
                        <th>Reach</th>
                        <th>Shares</th>
                        <th>Clicks</th>
                        <th>Comments</th>
                        <th>Likes</th>
                        <th>Posts</th>
                        <th>No. of Forms</th>
                        <th>ROI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $camp)
                        <tr>
                            <td style="color: var(--text-muted);">{{ $camp->id }}</td>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $camp->client_name }}</td>
                            <td>{{ $camp->company ?? 'N/A' }}</td>
                            <td style="font-weight: 500; color: var(--text-primary);">{{ $camp->campaign_title }}</td>
                            <td>
                                <span style="display: inline-block; padding: 0.2rem 0.4rem; border-radius: 5px; font-size: 0.75rem; font-weight: 600; background-color: {{ strtolower($camp->paid_or_not) === 'paid' ? 'rgba(59, 130, 246, 0.12)' : 'rgba(156, 163, 175, 0.12)' }}; color: {{ strtolower($camp->paid_or_not) === 'paid' ? '#3b82f6' : 'var(--text-muted)' }};">
                                    {{ $camp->paid_or_not ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #3b82f6;">{{ number_format($camp->potential_reach) }}</td>
                            <td style="font-weight: 600; color: #06b6d4;">{{ number_format($camp->total_shares) }}</td>
                            <td style="font-weight: 600; color: #3b82f6;">{{ number_format($camp->total_clicks) }}</td>
                            <td>{{ number_format($camp->total_comments) }}</td>
                            <td>{{ number_format($camp->total_likes) }}</td>
                            <td>{{ number_format($camp->total_posts) }}</td>
                            <td style="font-weight: 600; color: #8b5cf6;">{{ number_format($camp->registrations) }}</td>
                            <td style="white-space: nowrap;">
                                <button class="btn btn-outline calculate-row-roi-btn" 
                                        data-roi="{{ number_format($camp->roi, 2) }}x" 
                                        style="padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; gap: 0.25rem; min-width: 80px; justify-content: center; height: 28px; display: inline-flex; align-items: center; background-color: #17192a; border-color: var(--border-color); color: var(--text-primary);">
                                    Calculate
                                </button>
                                <span class="row-roi-value" style="display: none; font-weight: 600; color: #3b82f6;"></span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; color: var(--text-muted); padding: 3rem;">No campaigns found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $campaigns->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Section B: Client Summary Table -->
    <div class="section-title-bar" style="margin-top: 2.5rem;">
        <div class="section-title">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
            Client Summaries
        </div>
        <a href="{{ route('export.consumption') }}" class="btn btn-outline" style="padding: 0.5rem 0.875rem; font-size: 0.8rem; font-weight: 600; gap: 0.25rem;">
            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
            Export Clients CSV
        </a>
    </div>

    <!-- Client Summaries Table Card -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th style="text-align: right;">Campaigns Count</th>
                        <th style="text-align: right;">Total Shares</th>
                        <th style="text-align: right;">Total Submissions</th>
                        <th style="text-align: right; color:#60a5fa;">Credits Consumed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $client->client_name }}</td>
                            <td>{{ $client->email ?? 'N/A' }}</td>
                            <td style="text-align: right;">{{ $client->campaigns_count ?? 0 }}</td>
                            <td style="text-align: right;">{{ number_format($client->shares_count ?? 0) }}</td>
                            <td style="text-align: right;">{{ number_format($client->form_submissions_count ?? 0) }}</td>
                            <td style="text-align: right; font-weight: 600; color: #60a5fa;">{{ number_format($client->credits_consumed ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No client summaries found matching criteria.</td>
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

<!-- ==================== VIEW 2: VISUAL ANALYTICS VIEW ==================== -->
<div id="analytics-view-section" class="fade-section hidden">
    <!-- SECTION A: CLIENT SUMMARY VISUALS -->
    <div class="section-title-bar" style="margin-top: 0.5rem;">
        <div class="section-title">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
            Client Summaries Analytics
        </div>
        <div class="chart-actions-dropdown" style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Download Section:</span>
            <button class="chart-download-btn section-download-btn" title="Download Entire Section" data-section="client-summaries-analytics">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
            </button>
            <div class="chart-download-menu">
                <a href="#" class="download-link section-download-link" data-format="png" data-section="client-summaries-analytics">PNG Image</a>
                <a href="#" class="download-link section-download-link" data-format="jpeg" data-section="client-summaries-analytics">JPEG Image</a>
            </div>
        </div>
    </div>
    
    <div class="analytics-grid" style="margin-bottom: 4rem;">
        <!-- 1. Campaign Performance -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    📊 Campaign Performance <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Shares per Campaign</span>
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
                <canvas id="chartCampaignPerformance"></canvas>
                <div id="chartCampaignPerformance-empty" class="chart-empty-state" style="display:none;">
                    No Campaign Data Available
                </div>
            </div>
        </div>

        <!-- 2. Top Clients -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    🏆 Top Clients <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Based on Total Shares</span>
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
                <canvas id="chartTopClients"></canvas>
                <div id="chartTopClients-empty" class="chart-empty-state" style="display:none;">
                    No Client Data Available
                </div>
            </div>
        </div>

        <!-- 3. Shares Trend -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    📈 Shares Trend <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Shares over Time</span>
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
                <canvas id="chartSharesTrend"></canvas>
                <div id="chartSharesTrend-empty" class="chart-empty-state" style="display:none;">
                    No Trend Data Available
                </div>
            </div>
        </div>

        <!-- 4. Form Submissions Trend -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    📝 Form Submissions Trend <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Submissions over Time</span>
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
                <canvas id="chartSubmissionsTrend"></canvas>
                <div id="chartSubmissionsTrend-empty" class="chart-empty-state" style="display:none;">
                    No Submission Data Available
                </div>
            </div>
        </div>

        <!-- 5. Conversion Funnel -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    ⚡ Conversion Funnel <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Overview</span>
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
            <div class="funnel-container">
                <div class="funnel-step funnel-step-1">
                    <div class="funnel-step-bg" style="width: 100%;"></div>
                    <span class="funnel-step-label">Campaigns Created</span>
                    <span class="funnel-step-val">{{ number_format($analytics['client']['funnel']['campaigns']) }}</span>
                </div>
                <div class="funnel-step funnel-step-2">
                    @php
                        $sharesPct = $analytics['client']['funnel']['campaigns'] > 0 ? round(($analytics['client']['funnel']['shares'] / $analytics['client']['funnel']['campaigns']), 1) : 0;
                    @endphp
                    <div class="funnel-step-bg" style="width: {{ $analytics['client']['funnel']['campaigns'] > 0 ? '100%' : '0%' }};"></div>
                    <span class="funnel-step-label">Shares Generated</span>
                    <span class="funnel-step-badge">x{{ $sharesPct }} shares/campaign</span>
                    <span class="funnel-step-val">{{ number_format($analytics['client']['funnel']['shares']) }}</span>
                </div>
                <div class="funnel-step funnel-step-3">
                    @php
                        $convRate = $analytics['client']['funnel']['shares'] > 0 ? round(($analytics['client']['funnel']['submissions'] / $analytics['client']['funnel']['shares']) * 100, 1) : 0;
                    @endphp
                    <div class="funnel-step-bg" style="width: {{ $analytics['client']['funnel']['shares'] > 0 ? '100%' : '0%' }};"></div>
                    <span class="funnel-step-label">Form Submissions</span>
                    <span class="funnel-step-badge">{{ $convRate }}% conv. rate</span>
                    <span class="funnel-step-val">{{ number_format($analytics['client']['funnel']['submissions']) }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Credits Consumed -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    💎 Credits Consumed <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Credits used by Client</span>
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
                <canvas id="chartCreditsConsumed"></canvas>
                <div id="chartCreditsConsumed-empty" class="chart-empty-state" style="display:none;">
                    No Credits Consumed
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION B: REMOVED CAMPAIGNS VISUALS -->
    <div class="section-title-bar">
        <div class="section-title">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="color: var(--color-primary);"><path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0V9z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3a1.5 1.5 0 0 0 11 2.5v-1a1.5 1.5 0 0 0 9.5 0h-3z"/></svg>
            Removed Campaigns Analytics
        </div>
        <div class="chart-actions-dropdown" style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Download Section:</span>
            <button class="chart-download-btn section-download-btn" title="Download Entire Section" data-section="removed-campaigns-analytics">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
            </button>
            <div class="chart-download-menu">
                <a href="#" class="download-link section-download-link" data-format="png" data-section="removed-campaigns-analytics">PNG Image</a>
                <a href="#" class="download-link section-download-link" data-format="jpeg" data-section="removed-campaigns-analytics">JPEG Image</a>
            </div>
        </div>
    </div>

    <div class="analytics-grid">
        <!-- 1. Top Engaged Campaigns -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    📊 Top Campaigns by Engagement <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Total interactions</span>
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
                <canvas id="chartTopEngaged"></canvas>
                <div id="chartTopEngaged-empty" class="chart-empty-state" style="display:none;">
                    No Engagement Data Available
                </div>
            </div>
        </div>

        <!-- 2. Engagement Breakdown -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    🍩 Engagement Breakdown <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Channels ratio</span>
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
                <canvas id="chartEngagementBreakdown"></canvas>
                <div id="chartEngagementBreakdown-empty" class="chart-empty-state" style="display:none;">
                    No Breakdown Data Available
                </div>
            </div>
        </div>

        <!-- 3. Reach by Client -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    🏆 Reach by Client <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Potential Reach</span>
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
                <canvas id="chartReachByClient"></canvas>
                <div id="chartReachByClient-empty" class="chart-empty-state" style="display:none;">
                    No Reach Data Available
                </div>
            </div>
        </div>

        <!-- 4. Daily Trends -->
        <div class="chart-card lg-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    📈 Daily Trends <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Shares & Clicks</span>
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
                <canvas id="chartDailyTrends"></canvas>
                <div id="chartDailyTrends-empty" class="chart-empty-state" style="display:none;">
                    No Trend Data Available
                </div>
            </div>
        </div>

        <!-- 5. Paid Status Impact -->
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; width: 100%;">
                <h3 class="chart-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                    ⚡ Paid Status Impact <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-left: 0.5rem; margin-right: auto;">Shares per Status</span>
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
                <canvas id="chartUgcPerformance"></canvas>
                <div id="chartUgcPerformance-empty" class="chart-empty-state" style="display:none;">
                    No Paid Status Data Available
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const savedView = localStorage.getItem('campaign_report_view') || 'table';
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
document.addEventListener("DOMContentLoaded", () => {
    // Toggle Modal Drawer Script
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterOverlay = document.getElementById('filterOverlay');

    if (filterToggleBtn && filterOverlay) {
        filterToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            filterOverlay.style.display = filterOverlay.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', (e) => {
            if (!filterOverlay.contains(e.target) && e.target !== filterToggleBtn) {
                filterOverlay.style.display = 'none';
            }
        });
        
        // Auto-update dates when quick select changes (Inline Filter Bar)
        const capsuleBtns = document.querySelectorAll('.capsule-btn');
        const hiddenQuickSelect = document.getElementById('hiddenQuickSelect');
        const inlineFromInput = document.getElementById('inlineFromInput');
        const inlineToInput = document.getElementById('inlineToInput');
        const inlineFilterForm = document.getElementById('inlineFilterForm');
        
        if (capsuleBtns.length > 0 && inlineFromInput && inlineToInput && inlineFilterForm) {
            const calculateQuickSelectDates = (option) => {
                const now = new Date();
                let fromDate, toDate;
                switch (option) {
                    case 'This Week': {
                        const monday = new Date(now);
                        const day = monday.getDay();
                        const diff = (day === 0 ? -6 : 1 - day); // adjust when day is sunday
                        monday.setDate(monday.getDate() + diff);
                        
                        const sunday = new Date(monday);
                        sunday.setDate(monday.getDate() + 6);
                        
                        fromDate = monday.toISOString().split('T')[0];
                        toDate = sunday.toISOString().split('T')[0];
                        break;
                    }
                    case 'Last Week': {
                        const lastWeekMonday = new Date(now);
                        const day = lastWeekMonday.getDay();
                        const diffToLastMonday = (day === 0 ? 6 : day - 1) + 7;
                        lastWeekMonday.setDate(lastWeekMonday.getDate() - diffToLastMonday);
                        
                        const lastWeekSunday = new Date(lastWeekMonday);
                        lastWeekSunday.setDate(lastWeekMonday.getDate() + 6);
                        
                        fromDate = lastWeekMonday.toISOString().split('T')[0];
                        toDate = lastWeekSunday.toISOString().split('T')[0];
                        break;
                    }
                    case 'This Month': {
                        const startMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                        const endMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        fromDate = startMonth.toISOString().split('T')[0];
                        toDate = endMonth.toISOString().split('T')[0];
                        break;
                    }
                    case 'Last Month': {
                        const startLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        const endLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                        fromDate = startLastMonth.toISOString().split('T')[0];
                        toDate = endLastMonth.toISOString().split('T')[0];
                        break;
                    }
                    case 'All Time':
                        fromDate = '2020-01-01';
                        toDate = '2030-12-31';
                        break;
                    default:
                        fromDate = '';
                        toDate = '';
                        break;
                }
                return { from: fromDate, to: toDate };
            };

            capsuleBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    capsuleBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const option = btn.getAttribute('data-value');
                    hiddenQuickSelect.value = option;
                    
                    const dates = calculateQuickSelectDates(option);
                    inlineFromInput.value = dates.from;
                    inlineToInput.value = dates.to;
                    
                    inlineFilterForm.submit();
                });
            });

            // If user modifies date inputs manually, clear the active capsule and hidden quick_select
            const handleManualDateChange = () => {
                const activeBtn = document.querySelector('.capsule-btn.active');
                if (activeBtn) {
                    const option = activeBtn.getAttribute('data-value');
                    const dates = calculateQuickSelectDates(option);
                    if (inlineFromInput.value !== dates.from || inlineToInput.value !== dates.to) {
                        activeBtn.classList.remove('active');
                        hiddenQuickSelect.value = '';
                    }
                }
            };
            
            inlineFromInput.addEventListener('change', handleManualDateChange);
            inlineToInput.addEventListener('change', handleManualDateChange);
        }
    }

    // 1. Table View vs Visual Analytics Switching
    const toggleTable = document.getElementById('toggle-table');
    const toggleAnalytics = document.getElementById('toggle-analytics');
    const tableView = document.getElementById('table-view-section');
    const analyticsView = document.getElementById('analytics-view-section');

    const switchView = (showTable) => {
        localStorage.setItem('campaign_report_view', showTable ? 'table' : 'analytics');
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

    if (toggleTable && toggleAnalytics) {
        toggleTable.addEventListener('click', () => switchView(true));
        toggleAnalytics.addEventListener('click', () => switchView(false));
        
        const savedView = localStorage.getItem('campaign_report_view') || 'table';
        switchView(savedView === 'table');
    }

    // Global CSS style mappings for theme matching
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
            const canvas = document.getElementById(canvasId);
            if (canvas) canvas.style.display = 'none';
            const emptyState = document.getElementById(emptyStateId);
            if (emptyState) emptyState.style.display = 'flex';
            return true;
        }
        return false;
    };

    const data = @json($analytics);
    const filterRecords = @json($filterRecords ?? []);

    // ==================== CHARTS INITIALIZATION ====================
    if (typeof Chart !== 'undefined') {
        const cData = data.campaign;

        // Chart 1: Top Campaigns by Engagement
        try {
            if (!checkEmpty(cData.topEngaged, 'chartTopEngaged', 'chartTopEngaged-empty')) {
                const barOptions = JSON.parse(JSON.stringify(globalOptions));
                barOptions.onHover = (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                };
                const chartTopEngaged = new Chart(document.getElementById('chartTopEngaged'), {
                    type: 'bar',
                    data: {
                        labels: cData.topEngaged.map(c => c.name),
                        datasets: [{
                            data: cData.topEngaged.map(c => c.engagement),
                            backgroundColor: 'rgba(168, 85, 247, 0.75)', // Purple
                            borderColor: '#a855f7',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barThickness: 24
                        }]
                    },
                    options: barOptions
                });

                const chartTopEngagedCanvas = document.getElementById('chartTopEngaged');
                if (chartTopEngagedCanvas) {
                    chartTopEngagedCanvas.onclick = (evt) => {
                        const points = chartTopEngaged.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length > 0) {
                            const index = points[0].index;
                            const campaignName = chartTopEngaged.data.labels[index];
                            const searchEl = document.querySelector('#drawerFilterForm input[name="campaign_name"]');
                            if (searchEl) {
                                searchEl.value = campaignName;
                                searchEl.form.submit();
                            }
                        }
                    };
                }
            }
        } catch (e) {
            console.error("Error loading Chart 1 (Top Engaged Campaigns):", e);
        }

        // Chart 2: Engagement Breakdown (Doughnut)
        try {
            const breakdownData = [
                cData.engagementBreakdown.shares,
                cData.engagementBreakdown.clicks,
                cData.engagementBreakdown.comments,
                cData.engagementBreakdown.likes
            ];
            const hasBreakdownData = breakdownData.some(v => v > 0);
            if (!hasBreakdownData) {
                checkEmpty(null, 'chartEngagementBreakdown', 'chartEngagementBreakdown-empty');
            } else {
                new Chart(document.getElementById('chartEngagementBreakdown'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Shares', 'Clicks', 'Comments', 'Likes'],
                        datasets: [{
                            data: breakdownData,
                            backgroundColor: [
                                'rgba(168, 85, 247, 0.8)',   // Purple
                                'rgba(139, 92, 246, 0.8)',   // Violet
                                'rgba(99, 102, 241, 0.8)',   // Indigo
                                'rgba(129, 140, 248, 0.8)'   // Soft Indigo
                            ],
                            borderColor: '#110f1b',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    color: labelColor,
                                    font: chartFont,
                                    boxWidth: 10,
                                    padding: 15
                                }
                            },
                            tooltip: globalOptions.plugins.tooltip
                        }
                    }
                });
            }
        } catch (e) {
            console.error("Error loading Chart 2 (Engagement Breakdown):", e);
        }

        // Chart 3: Reach by Client (Horizontal Bar)
        try {
            if (!checkEmpty(cData.reachByClient, 'chartReachByClient', 'chartReachByClient-empty')) {
                const horizontalOptions = JSON.parse(JSON.stringify(globalOptions));
                horizontalOptions.indexAxis = 'y';
                horizontalOptions.onHover = (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                };
                const chartReachByClient = new Chart(document.getElementById('chartReachByClient'), {
                    type: 'bar',
                    data: {
                        labels: cData.reachByClient.map(c => c.name),
                        datasets: [{
                            data: cData.reachByClient.map(c => c.reach),
                            backgroundColor: 'rgba(139, 92, 246, 0.75)', // Purple/Violet
                            borderColor: '#8b5cf6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barThickness: 16
                        }]
                    },
                    options: horizontalOptions
                });

                const chartReachByClientCanvas = document.getElementById('chartReachByClient');
                if (chartReachByClientCanvas) {
                    chartReachByClientCanvas.onclick = (evt) => {
                        const points = chartReachByClient.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length > 0) {
                            const index = points[0].index;
                            const clientName = chartReachByClient.data.labels[index];
                            const selectEl = document.querySelector('#drawerFilterForm input[name="client_name"]');
                            if (selectEl) {
                                selectEl.value = clientName;
                                selectEl.form.submit();
                            }
                        }
                    };
                }
            }
        } catch (e) {
            console.error("Error loading Chart 3 (Reach by Client):", e);
        }

        // Chart 4: Daily Trends (Line)
        try {
            if (!checkEmpty(cData.dailyTrends, 'chartDailyTrends', 'chartDailyTrends-empty')) {
                const dailyTrendCanvas = document.getElementById('chartDailyTrends');
                dailyTrendCanvas.style.cursor = 'pointer';
                const chartDailyTrend = new Chart(dailyTrendCanvas, {
                    type: 'line',
                    data: {
                        labels: cData.dailyTrends.map(c => c.date),
                        datasets: [
                            {
                                label: 'Shares',
                                data: cData.dailyTrends.map(c => c.shares),
                                borderColor: '#a855f7', // Purple
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 3
                            },
                            {
                                label: 'Clicks',
                                data: cData.dailyTrends.map(c => c.clicks),
                                borderColor: '#8b5cf6', // Violet
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 3
                            },
                            {
                                label: 'Registrations',
                                data: cData.dailyTrends.map(c => c.submissions),
                                borderColor: '#6366f1', // Indigo
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 3
                            }
                        ]
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
                                position: 'top',
                                labels: {
                                    color: labelColor,
                                    font: chartFont,
                                    boxWidth: 10
                                }
                            },
                            tooltip: globalOptions.plugins.tooltip
                        },
                        scales: globalOptions.scales
                    }
                });

                dailyTrendCanvas.onclick = (evt) => {
                    const points = chartDailyTrend.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if (points.length > 0) {
                        const index = points[0].index;
                        const dateVal = chartDailyTrend.data.labels[index];
                        const dateInput = document.querySelector('#drawerFilterForm input[name="campaign_date"]');
                        if (dateInput) {
                            dateInput.value = dateVal;
                            dateInput.form.submit();
                        }
                    }
                };
            }
        } catch (e) {
            console.error("Error loading Chart 4 (Daily Trends):", e);
        }

        // Chart 5: Paid Status Impact (Doughnut)
        try {
            const hasUgcData = cData.ugcPerformance && cData.ugcPerformance.length > 0;
            if (!hasUgcData) {
                checkEmpty(null, 'chartUgcPerformance', 'chartUgcPerformance-empty');
            } else {
                const ugcCanvas = document.getElementById('chartUgcPerformance');
                ugcCanvas.style.cursor = 'pointer';
                const chartUgc = new Chart(ugcCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: cData.ugcPerformance.map(u => u.label),
                        datasets: [{
                            data: cData.ugcPerformance.map(u => u.shares),
                            backgroundColor: [
                                'rgba(168, 85, 247, 0.8)',   // Purple
                                'rgba(99, 102, 241, 0.8)',   // Indigo
                                'rgba(139, 92, 246, 0.8)',   // Violet
                                'rgba(129, 140, 248, 0.8)',  // Soft Indigo
                                'rgba(236, 72, 153, 0.8)'    // Pink
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
                                labels: {
                                    color: labelColor,
                                    font: chartFont,
                                    boxWidth: 10,
                                    padding: 15
                                }
                            },
                            tooltip: globalOptions.plugins.tooltip
                        }
                    }
                });

                ugcCanvas.onclick = (evt) => {
                    const points = chartUgc.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if (points.length > 0) {
                        const index = points[0].index;
                        const paidStatus = chartUgc.data.labels[index];
                        const paidInput = document.querySelector('#drawerFilterForm input[name="paid_or_not"]');
                        if (paidInput) {
                            paidInput.value = paidStatus;
                            paidInput.form.submit();
                        }
                    }
                };
            }
        } catch (e) {
            console.error("Error loading Chart 5 (Paid Status Impact):", e);
        }

        // ==================== CLIENT CHARTS INITIALIZATION ====================
        const sData = data.client;

        // Chart 1: Campaign Performance (Shares per Campaign)
        try {
            if (!checkEmpty(sData.campaignPerformance, 'chartCampaignPerformance', 'chartCampaignPerformance-empty')) {
                const barOptions = JSON.parse(JSON.stringify(globalOptions));
                barOptions.onHover = (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                };
                const chartCampPerf = new Chart(document.getElementById('chartCampaignPerformance'), {
                    type: 'bar',
                    data: {
                        labels: sData.campaignPerformance.map(c => c.name),
                        datasets: [{
                            data: sData.campaignPerformance.map(c => c.shares),
                            backgroundColor: 'rgba(139, 92, 246, 0.75)', // Purple
                            borderColor: '#8b5cf6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barThickness: 24
                        }]
                    },
                    options: barOptions
                });

                const chartCampaignPerformanceCanvas = document.getElementById('chartCampaignPerformance');
                if (chartCampaignPerformanceCanvas) {
                    chartCampaignPerformanceCanvas.onclick = (evt) => {
                        const points = chartCampPerf.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length > 0) {
                            const index = points[0].index;
                            const campaignName = chartCampPerf.data.labels[index];
                            const searchEl = document.querySelector('#drawerFilterForm input[name="campaign_name"]');
                            if (searchEl) {
                                searchEl.value = campaignName;
                                searchEl.form.submit();
                            }
                        }
                    };
                }
            }
        } catch (e) {
            console.error("Error loading Client Chart 1 (Campaign Performance):", e);
        }

        // Chart 2: Top Clients Horizontal Bar
        try {
            if (!checkEmpty(sData.topClients, 'chartTopClients', 'chartTopClients-empty')) {
                const horizontalOptions = JSON.parse(JSON.stringify(globalOptions));
                horizontalOptions.indexAxis = 'y';
                horizontalOptions.onHover = (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                };
                const chartTopCli = new Chart(document.getElementById('chartTopClients'), {
                    type: 'bar',
                    data: {
                        labels: sData.topClients.map(c => c.name),
                        datasets: [{
                            data: sData.topClients.map(c => c.shares),
                            backgroundColor: 'rgba(139, 92, 246, 0.75)', // Purple/Violet
                            borderColor: '#8b5cf6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barThickness: 16
                        }]
                    },
                    options: horizontalOptions
                });

                const chartTopClientsCanvas = document.getElementById('chartTopClients');
                if (chartTopClientsCanvas) {
                    chartTopClientsCanvas.onclick = (evt) => {
                        const points = chartTopCli.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length > 0) {
                            const index = points[0].index;
                            const clientName = chartTopCli.data.labels[index];
                            const selectEl = document.querySelector('#drawerFilterForm input[name="client_name"]');
                            if (selectEl) {
                                selectEl.value = clientName;
                                selectEl.form.submit();
                            }
                        }
                    };
                }
            }
        } catch (e) {
            console.error("Error loading Client Chart 2 (Top Clients):", e);
        }

        // Chart 3: Shares Trend Line
        try {
            if (!checkEmpty(sData.sharesTrend, 'chartSharesTrend', 'chartSharesTrend-empty')) {
                const sharesTrendCanvas = document.getElementById('chartSharesTrend');
                sharesTrendCanvas.style.cursor = 'pointer';
                const chartSharesTrend = new Chart(sharesTrendCanvas, {
                    type: 'line',
                    data: {
                        labels: sData.sharesTrend.map(c => c.date),
                        datasets: [{
                            data: sData.sharesTrend.map(c => c.shares),
                            fill: true,
                            backgroundColor: 'rgba(168, 85, 247, 0.04)',
                            borderColor: '#a855f7', // Purple
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#a855f7',
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        ...globalOptions,
                        onHover: (event, chartElement) => {
                            event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                        }
                    }
                });

                sharesTrendCanvas.onclick = (evt) => {
                    const points = chartSharesTrend.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if (points.length > 0) {
                        const index = points[0].index;
                        const dateVal = chartSharesTrend.data.labels[index];
                        const dateInput = document.querySelector('#drawerFilterForm input[name="campaign_date"]');
                        if (dateInput) {
                            dateInput.value = dateVal;
                            dateInput.form.submit();
                        }
                    }
                };
            }
        } catch (e) {
            console.error("Error loading Client Chart 3 (Shares Trend):", e);
        }

        // Chart 4: Form Submissions Trend Line
        try {
            if (!checkEmpty(sData.submissionsTrend, 'chartSubmissionsTrend', 'chartSubmissionsTrend-empty')) {
                const subTrendCanvas = document.getElementById('chartSubmissionsTrend');
                subTrendCanvas.style.cursor = 'pointer';
                const chartSubmissionsTrend = new Chart(subTrendCanvas, {
                    type: 'line',
                    data: {
                        labels: sData.submissionsTrend.map(c => c.date),
                        datasets: [{
                            data: sData.submissionsTrend.map(c => c.submissions),
                            fill: true,
                            backgroundColor: 'rgba(99, 102, 241, 0.04)',
                            borderColor: '#6366f1', // Indigo
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1',
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        ...globalOptions,
                        onHover: (event, chartElement) => {
                            event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                        }
                    }
                });

                subTrendCanvas.onclick = (evt) => {
                    const points = chartSubmissionsTrend.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if (points.length > 0) {
                        const index = points[0].index;
                        const dateVal = chartSubmissionsTrend.data.labels[index];
                        const dateInput = document.querySelector('#drawerFilterForm input[name="campaign_date"]');
                        if (dateInput) {
                            dateInput.value = dateVal;
                            dateInput.form.submit();
                        }
                    }
                };
            }
        } catch (e) {
            console.error("Error loading Client Chart 4 (Form Submissions Trend):", e);
        }

        // Chart 6: Credits Consumed (Doughnut)
        try {
            if (!checkEmpty(sData.creditsConsumed, 'chartCreditsConsumed', 'chartCreditsConsumed-empty')) {
                const doughnutOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: labelColor,
                                font: chartFont,
                                boxWidth: 10,
                                padding: 15
                            }
                        },
                        tooltip: globalOptions.plugins.tooltip
                    }
                };
                const chartCredits = new Chart(document.getElementById('chartCreditsConsumed'), {
                    type: 'doughnut',
                    data: {
                        labels: sData.creditsConsumed.map(c => c.name),
                        datasets: [{
                            data: sData.creditsConsumed.map(c => c.consumed),
                            backgroundColor: [
                                'rgba(168, 85, 247, 0.75)',  // Purple
                                'rgba(139, 92, 246, 0.75)',  // Violet
                                'rgba(99, 102, 241, 0.75)',  // Indigo
                                'rgba(129, 140, 248, 0.75)',  // Soft Indigo
                                'rgba(236, 72, 153, 0.75)',  // Pink
                                'rgba(168, 85, 247, 0.45)',
                                'rgba(139, 92, 246, 0.45)',
                                'rgba(99, 102, 241, 0.45)'
                            ],
                            borderColor: '#110f1b',
                            borderWidth: 2
                        }]
                    },
                    options: doughnutOptions
                });

                const chartCreditsConsumedCanvas = document.getElementById('chartCreditsConsumed');
                if (chartCreditsConsumedCanvas) {
                    chartCreditsConsumedCanvas.onclick = (evt) => {
                        const points = chartCredits.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length > 0) {
                            const index = points[0].index;
                            const clientName = chartCredits.data.labels[index];
                            const selectEl = document.querySelector('#drawerFilterForm input[name="client_name"]');
                            if (selectEl) {
                                selectEl.value = clientName;
                                selectEl.form.submit();
                            }
                        }
                    };
                }
            }
        } catch (e) {
            console.error("Error loading Client Chart 6 (Credits Consumed):", e);
        }
    } else {
        console.warn("Chart.js failed to load. Chart visuals are disabled.");
    }

    // Table Row ROI Calculation Logic (Event Delegation)
    const tableBody = document.querySelector('#campaignsTable tbody');
    if (tableBody) {
        tableBody.addEventListener('click', (e) => {
            const btn = e.target.closest('.calculate-row-roi-btn');
            const span = e.target.closest('.row-roi-value');

            if (btn) {
                // Prevent double clicks
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner" style="width: 0.75rem; height: 0.75rem; border-width: 1.5px; border-top-color: white; margin-right: 0;"></span>';

                const container = btn.parentElement;
                const roiVal = btn.getAttribute('data-roi');
                const spanVal = container.querySelector('.row-roi-value');

                setTimeout(() => {
                    btn.style.display = 'none';
                    spanVal.textContent = roiVal;
                    spanVal.style.display = 'inline';
                    spanVal.style.opacity = 0;
                    spanVal.style.cursor = 'pointer'; // Make it look clickable
                    
                    // Fade in animation
                    let opacity = 0;
                    const fadeIn = setInterval(() => {
                        if (opacity >= 1) {
                            clearInterval(fadeIn);
                        }
                        spanVal.style.opacity = opacity;
                        opacity += 0.1;
                    }, 20);
                }, 400);
            } else if (span) {
                const container = span.parentElement;
                const calculateBtn = container.querySelector('.calculate-row-roi-btn');
                if (calculateBtn) {
                    span.style.display = 'none';
                    calculateBtn.style.display = 'inline-flex';
                    calculateBtn.disabled = false;
                    calculateBtn.innerHTML = 'Calculate';
                }
            }
        });
    }

    // Cascading filter dropdown logic
    const drawerFilterForm = document.getElementById('drawerFilterForm');
    const filterFieldConfig = [
        { name: 'client_name', label: 'All Clients', key: 'client_name' },
        { name: 'email', label: 'All Emails', key: 'email' },
        { name: 'campaign_date', label: 'All Dates', key: 'campaign_date' },
        { name: 'campaign_id', label: 'All Campaign IDs', key: 'campaign_id' },
        { name: 'campaign_name', label: 'All Campaign Names', key: 'campaign_name' },
        { name: 'paid_or_not', label: 'All Paid Statuses', key: 'paid_or_not' },
    ];

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    const getCurrentFilters = () => {
        const filters = {};
        if (!drawerFilterForm) return filters;

        filterFieldConfig.forEach(({ name }) => {
            const input = drawerFilterForm.querySelector(`input[name="${name}"]`);
            if (input && input.value.trim() !== '') {
                filters[name] = input.value.trim();
            }
        });

        return filters;
    };

    const recordMatches = (record, filters) => {
        if (filters.client_name && !record.client_name.toLowerCase().includes(filters.client_name.toLowerCase())) {
            return false;
        }
        if (filters.email && !(record.email || '').toLowerCase().includes(filters.email.toLowerCase())) {
            return false;
        }
        if (filters.campaign_date && record.campaign_date !== filters.campaign_date) {
            return false;
        }
        if (filters.campaign_id && !String(record.campaign_id).includes(filters.campaign_id)) {
            return false;
        }
        if (filters.campaign_name && !record.campaign_name.toLowerCase().includes(filters.campaign_name.toLowerCase())) {
            return false;
        }
        if (filters.paid_or_not && record.paid_or_not !== filters.paid_or_not) {
            return false;
        }

        return true;
    };

    const sortFilterValues = (key, values) => {
        if (key === 'campaign_date') {
            return values.sort((a, b) => b.localeCompare(a));
        }
        if (key === 'campaign_id') {
            return values.sort((a, b) => Number(b) - Number(a));
        }

        return values.sort((a, b) => String(a).localeCompare(String(b)));
    };

    const getUniqueValues = (records, key) => {
        const values = [...new Set(
            records
                .map(record => record[key])
                .filter(value => value !== null && value !== undefined && value !== '')
        )];

        return sortFilterValues(key, values);
    };

    const rebuildDropdown = (fieldName, defaultLabel, values) => {
        const input = drawerFilterForm?.querySelector(`input[name="${fieldName}"]`);
        const container = input?.closest('.searchable-select-container');
        const dropdown = container?.querySelector('.searchable-dropdown');
        if (!dropdown) return;

        const optionsHtml = [`<div class="searchable-option" data-value="">${escapeHtml(defaultLabel)}</div>`];
        values.forEach(value => {
            optionsHtml.push(`<div class="searchable-option" data-value="${escapeHtml(value)}">${escapeHtml(value)}</div>`);
        });

        dropdown.innerHTML = optionsHtml.join('');
    };

    const syncCascadeDropdowns = () => {
        if (!drawerFilterForm || !filterRecords.length) return;

        let filters = getCurrentFilters();
        let changed = true;
        let iterations = 0;

        // Keep validating and clearing invalid fields until we reach a stable state
        while (changed && iterations < 10) {
            changed = false;
            iterations++;
            
            for (const field of filterFieldConfig) {
                const input = drawerFilterForm.querySelector(`input[name="${field.name}"]`);
                if (!input || input.value.trim() === '') continue;

                const contextFilters = { ...filters };
                delete contextFilters[field.name];

                const matchingRecords = filterRecords.filter(record => recordMatches(record, contextFilters));
                const validValues = getUniqueValues(matchingRecords, field.key);

                const currentValue = input.value.trim();
                // Check if current input value is at least a substring match of any valid value (case-insensitive)
                const isValid = validValues.some(val => 
                    String(val).toLowerCase().includes(currentValue.toLowerCase())
                );

                if (!isValid) {
                    input.value = '';
                    filters = getCurrentFilters();
                    changed = true;
                    break; // break the inner loop and start validating again with updated filters
                }
            }
        }

        // Rebuild all dropdowns based on the final stable filters
        filterFieldConfig.forEach(field => {
            const contextFilters = { ...filters };
            delete contextFilters[field.name];

            const matchingRecords = filterRecords.filter(record => recordMatches(record, contextFilters));
            const values = getUniqueValues(matchingRecords, field.key);
            rebuildDropdown(field.name, field.label, values);
        });

        updateClientCampaignsPanel();
    };

    const findMatchingClient = (clientVal) => {
        if (!clientVal) return null;

        const lower = clientVal.toLowerCase().trim();
        const clients = [...new Set(filterRecords.map(record => record.client_name))];
        const exact = clients.find(client => client.toLowerCase() === lower);
        if (exact) return exact;

        const partialMatches = clients.filter(client => client.toLowerCase().includes(lower));
        return partialMatches.length === 1 ? partialMatches[0] : null;
    };

    const updateClientCampaignsPanel = () => {
        const clientInput = drawerFilterForm?.querySelector('input[name="client_name"]');
        const clientCampaignsGroup = document.getElementById('client-campaigns-group');
        const clientCampaignsList = document.getElementById('client-campaigns-list');
        const clientCampaignsSearch = document.getElementById('client-campaigns-search');

        if (!clientInput || !clientCampaignsGroup || !clientCampaignsList) return;

        const matchKey = findMatchingClient(clientInput.value.trim());
        if (!matchKey) {
            clientCampaignsGroup.style.display = 'none';
            clientCampaignsList.innerHTML = '';
            if (clientCampaignsSearch) clientCampaignsSearch.value = '';
            return;
        }

        const filters = getCurrentFilters();
        filters.client_name = matchKey;

        const campaigns = [];
        const seenIds = new Set();

        filterRecords
            .filter(record => record.client_name === matchKey && recordMatches(record, filters))
            .forEach(record => {
                if (seenIds.has(record.campaign_id)) return;
                seenIds.add(record.campaign_id);
                campaigns.push({ id: record.campaign_id, title: record.campaign_name });
            });

        if (!campaigns.length) {
            clientCampaignsGroup.style.display = 'none';
            clientCampaignsList.innerHTML = '';
            if (clientCampaignsSearch) clientCampaignsSearch.value = '';
            return;
        }

        clientCampaignsGroup.style.display = 'block';
        renderClientCampaignsList(campaigns);
    };

    const filterVisibleCampaignItems = () => {
        const clientCampaignsList = document.getElementById('client-campaigns-list');
        const clientCampaignsSearch = document.getElementById('client-campaigns-search');
        if (!clientCampaignsSearch || !clientCampaignsList) return;

        const query = clientCampaignsSearch.value.toLowerCase().trim();
        let visibleCount = 0;

        clientCampaignsList.querySelectorAll('.client-campaign-item').forEach(item => {
            const title = (item.getAttribute('data-title') || '').toLowerCase();
            const id = String(item.getAttribute('data-id') || '').toLowerCase();
            const matches = !query || title.includes(query) || id.includes(query);
            item.style.display = matches ? 'flex' : 'none';
            if (matches) visibleCount++;
        });

        let emptyState = clientCampaignsList.querySelector('.client-campaign-empty');
        if (visibleCount === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'searchable-option no-results client-campaign-empty';
                emptyState.textContent = 'No campaigns match your search';
                clientCampaignsList.appendChild(emptyState);
            }
            emptyState.style.display = 'block';
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    };

    const selectClientCampaign = (camp) => {
        const idInput = drawerFilterForm?.querySelector('input[name="campaign_id"]');
        const nameInput = drawerFilterForm?.querySelector('input[name="campaign_name"]');
        const clientCampaignsList = document.getElementById('client-campaigns-list');

        clientCampaignsList?.querySelectorAll('.client-campaign-item').forEach(el => el.classList.remove('active'));

        const selectedItem = clientCampaignsList?.querySelector(`.client-campaign-item[data-id="${camp.id}"]`);
        if (selectedItem) selectedItem.classList.add('active');

        if (idInput) idInput.value = camp.id;
        if (nameInput) nameInput.value = camp.title;

        syncCascadeDropdowns();

        if (drawerFilterForm) {
            drawerFilterForm.submit();
        }
    };

    const renderClientCampaignsList = (campaigns) => {
        const clientCampaignsList = document.getElementById('client-campaigns-list');
        const clientCampaignsSearch = document.getElementById('client-campaigns-search');
        const idInput = drawerFilterForm?.querySelector('input[name="campaign_id"]');
        const nameInput = drawerFilterForm?.querySelector('input[name="campaign_name"]');

        if (!clientCampaignsList) return;

        clientCampaignsList.innerHTML = '';

        campaigns.forEach(camp => {
            const item = document.createElement('div');
            item.className = 'searchable-option client-campaign-item';
            item.style.padding = '0.5rem 0.75rem';
            item.style.borderRadius = '6px';
            item.style.cursor = 'pointer';
            item.style.fontSize = '0.825rem';
            item.style.border = '1px solid transparent';
            item.style.transition = 'all 0.15s';
            item.style.display = 'flex';
            item.style.justifyContent = 'space-between';
            item.style.alignItems = 'center';
            item.style.color = '#d1d5db';
            item.innerHTML = `<span style="font-weight: 500;">${escapeHtml(camp.title)}</span><span style="color: var(--text-muted); font-size: 0.75rem;">ID: ${escapeHtml(camp.id)}</span>`;
            item.setAttribute('data-id', camp.id);
            item.setAttribute('data-title', camp.title);

            if (idInput && (String(idInput.value) === String(camp.id) || nameInput?.value === camp.title)) {
                item.classList.add('active');
            }

            item.addEventListener('mouseenter', () => {
                if (!item.classList.contains('active')) {
                    item.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                }
            });
            item.addEventListener('mouseleave', () => {
                if (!item.classList.contains('active')) {
                    item.style.backgroundColor = 'transparent';
                }
            });
            item.addEventListener('click', () => selectClientCampaign(camp));

            clientCampaignsList.appendChild(item);
        });

        if (clientCampaignsSearch) {
            clientCampaignsSearch.value = '';
        }
        filterVisibleCampaignItems();
    };

    const clientCampaignsSearch = document.getElementById('client-campaigns-search');
    if (clientCampaignsSearch) {
        clientCampaignsSearch.addEventListener('input', filterVisibleCampaignItems);
        clientCampaignsSearch.addEventListener('click', (e) => e.stopPropagation());
    }

    // Searchable Select Dropdown Logic
    const searchableContainers = drawerFilterForm
        ? drawerFilterForm.querySelectorAll('.searchable-select-container')
        : document.querySelectorAll('.searchable-select-container');

    searchableContainers.forEach(container => {
        const input = container.querySelector('.searchable-input');
        const dropdown = container.querySelector('.searchable-dropdown');

        const filterOptions = () => {
            const query = input.value.toLowerCase();
            const options = dropdown.querySelectorAll('.searchable-option');
            let visibleCount = 0;

            const existingNoResults = dropdown.querySelector('.no-results');
            if (existingNoResults) existingNoResults.remove();

            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                const val = (option.getAttribute('data-value') || '').toLowerCase();

                if (text.includes(query) || val.includes(query) || option.getAttribute('data-value') === '') {
                    option.style.display = 'block';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });

            if (visibleCount === 1 && options[0]?.getAttribute('data-value') === '' && query !== '') {
                options[0].style.display = 'none';
                visibleCount = 0;
            }

            if (visibleCount === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'searchable-option no-results';
                noResults.textContent = 'No matches found';
                dropdown.appendChild(noResults);
            }
        };

        input.addEventListener('focus', () => {
            document.querySelectorAll('.searchable-dropdown').forEach(d => {
                if (d !== dropdown) d.style.display = 'none';
            });
            dropdown.style.display = 'block';
            filterOptions();
        });

        input.addEventListener('input', () => {
            filterOptions();
            syncCascadeDropdowns();
        });

        dropdown.addEventListener('click', (e) => {
            const option = e.target.closest('.searchable-option');
            if (!option || option.classList.contains('no-results')) return;

            input.value = option.getAttribute('data-value');
            dropdown.style.display = 'none';
            syncCascadeDropdowns();
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    });

    // Columns Dropdown Toggle & Table Visibility Logic
    const columnsDropdownBtn = document.getElementById('columnsDropdownBtn');
    const columnsDropdownMenu = document.getElementById('columnsDropdownMenu');

    const loadColumnPreferences = () => {
        let prefs = localStorage.getItem('campaign_table_columns');
        if (prefs) {
            try {
                return JSON.parse(prefs);
            } catch (e) {
                return {};
            }
        }
        return {};
    };

    const saveColumnPreferences = (prefs) => {
        localStorage.setItem('campaign_table_columns', JSON.stringify(prefs));
    };

    if (columnsDropdownBtn && columnsDropdownMenu) {
        columnsDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            columnsDropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!columnsDropdownMenu.contains(e.target) && e.target !== columnsDropdownBtn) {
                columnsDropdownMenu.classList.remove('show');
            }
        });

        const toggleItems = columnsDropdownMenu.querySelectorAll('.column-toggle-item');
        const prefs = loadColumnPreferences();

        // Initialize state based on preferences
        toggleItems.forEach(item => {
            const colIndex = item.getAttribute('data-column');
            if (prefs[colIndex] === false) {
                item.classList.remove('active');
                setColumnVisibility(colIndex, false);
            } else {
                item.classList.add('active');
                setColumnVisibility(colIndex, true);
            }

            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const isActive = item.classList.contains('active');
                
                if (isActive) {
                    item.classList.remove('active');
                    setColumnVisibility(colIndex, false);
                    prefs[colIndex] = false;
                } else {
                    item.classList.add('active');
                    setColumnVisibility(colIndex, true);
                    prefs[colIndex] = true;
                }
                saveColumnPreferences(prefs);
            });
        });
    }

    function setColumnVisibility(colIndex, visible) {
        const table = document.getElementById('campaignsTable');
        if (!table) return;

        const cells = table.querySelectorAll(`tr th:nth-child(${colIndex}), tr td:nth-child(${colIndex})`);
        cells.forEach(cell => {
            cell.style.display = visible ? '' : 'none';
        });
    }

    syncCascadeDropdowns();

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
