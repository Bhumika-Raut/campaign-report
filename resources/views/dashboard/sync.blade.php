@extends('layouts.layout')

@section('main')
<div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--color-success); color: var(--color-success); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <!-- Option 1: Google Sheets Live Sync -->
        <div class="card" style="flex: 1; min-width: 320px; display: flex; flex-direction: column;">
            <h2 style="margin-top: 0; font-size: 1.35rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                🟢 Google Sheets Live Sync
            </h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                Synchronize the dashboard directly with your live Google Sheet workbook. Make sure the sheet is shared so that "Anyone with the link can view" to allow the sync process to read the sheets.
            </p>

            <form action="{{ route('sync.google-sheet') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: auto;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Google Sheet URL</label>
                    <input type="url" name="sheet_url" value="https://docs.google.com/spreadsheets/d/1zGND31Why2-EqHTZHg5xuOOFdlS9HpO_lpuKfGzuEMI/edit?usp=sharing" class="form-control" style="background-color: var(--bg-base); padding: 0.75rem 1rem;" placeholder="https://docs.google.com/spreadsheets/d/.../edit" required>
                </div>
                <button type="submit" class="btn" style="justify-content: center; padding: 0.875rem; font-weight: 600; background: linear-gradient(135deg, var(--color-primary) 0%, #6366f1 100%);">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.35rem;"><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/></svg>
                    Sync Live from Google Sheets
                </button>
            </form>
        </div>

        <!-- Option 2: Upload Local Excel Workbook -->
        <div class="card" style="flex: 1; min-width: 320px; display: flex; flex-direction: column;">
            <h2 style="margin-top: 0; font-size: 1.35rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                📂 Upload Excel Workbook
            </h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                Alternatively, upload a local campaign Excel workbook (`.xlsx`, `.xls`, or `.csv`) containing the necessary sheets (`client consumption`, `campaign report`, and `client summary`) to update the database manually.
            </p>

            <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: auto;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Excel file</label>
                    <input type="file" name="excel_file" class="form-control" style="background-color: var(--bg-base); padding: 0.75rem 1rem;" required>
                </div>
                <button type="submit" class="btn btn-outline" style="justify-content: center; padding: 0.875rem; font-weight: 600;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.35rem;"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 1.146a.5.5 0 0 1 .708 0l3-3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/></svg>
                    Upload and Synchronize
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
