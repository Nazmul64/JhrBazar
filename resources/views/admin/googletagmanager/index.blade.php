@extends('admin.master')

@section('content')

<style>
    .gtm-wrapper {
        padding: 10px 0;
    }

    .gtm-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .gtm-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .btn-create {
        background: #6c63ff;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-create:hover {
        background: #574fd6;
        color: #fff;
    }

    .gtm-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    /* DataTable toolbar */
    .dt-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dt-toolbar-left {
        display: flex;
        gap: 8px;
    }

    .btn-tool {
        background: #f0f0f5;
        color: #444;
        border: 1px solid #ddd;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
    }

    .btn-tool:hover {
        background: #e0e0eb;
        color: #222;
    }

    .dt-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dt-toolbar-right label {
        font-size: 13px;
        color: #666;
        font-weight: 500;
        margin: 0;
    }

    .dt-search-input {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        outline: none;
        transition: border 0.2s;
        width: 180px;
    }

    .dt-search-input:focus {
        border-color: #6c63ff;
    }

    /* Table */
    .gtm-table {
        width: 100%;
        border-collapse: collapse;
    }

    .gtm-table thead tr {
        background: #f5f5fa;
    }

    .gtm-table thead th {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        text-align: left;
        border-bottom: 1px solid #eee;
        white-space: nowrap;
    }

    .gtm-table tbody td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #333;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .gtm-table tbody tr:last-child td {
        border-bottom: none;
    }

    .gtm-table tbody tr:hover {
        background: #fafafa;
    }

    /* Badge */
    .badge-active {
        background: #d4f5ec;
        color: #0d9e6e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-inactive {
        background: #fde8e8;
        color: #e53e3e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    /* Action buttons */
    .action-btns {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .btn-action {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: opacity 0.2s;
        text-decoration: none;
    }

    .btn-action:hover {
        opacity: 0.85;
    }

    .btn-toggle {
        background: #3d3d3d;
        color: #fff;
    }

    .btn-edit {
        background: #4fc08d;
        color: #fff;
    }

    .btn-delete {
        background: #f56565;
        color: #fff;
    }

    /* Pagination */
    .dt-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dt-info {
        font-size: 13px;
        color: #888;
    }

    .dt-pagination {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .dt-pagination .page-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: #fff;
        color: #555;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }

    .dt-pagination .page-btn.active,
    .dt-pagination .page-btn:hover {
        background: #6c63ff;
        color: #fff;
        border-color: #6c63ff;
    }

    /* Alert */
    .alert-success-custom {
        background: #d4f5ec;
        color: #0d9e6e;
        border-left: 4px solid #0d9e6e;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-error-custom {
        background: #fde8e8;
        color: #e53e3e;
        border-left: 4px solid #e53e3e;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        font-weight: 500;
    }

    @media (max-width: 600px) {
        .gtm-header h2 { font-size: 18px; }
        .dt-toolbar { flex-direction: column; align-items: flex-start; }
        .dt-footer { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="gtm-wrapper">

    {{-- Header --}}
    <div class="gtm-header">
        <h2>Tag Manager Manage</h2>
        <a href="{{ route('admin.googletagmanager.create') }}" class="btn-create">Create</a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert-success-custom">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-custom">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="gtm-card">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <button class="btn-tool" onclick="copyTable()">Copy</button>
                <button class="btn-tool" onclick="window.print()">Print</button>
                <button class="btn-tool" onclick="exportPDF()">PDF</button>
            </div>
            <div class="dt-toolbar-right">
                <label>Search:</label>
                <input type="text" class="dt-search-input" id="tableSearch" placeholder="" onkeyup="searchTable()" />
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="gtm-table" id="gtmTable">
                <thead>
                    <tr>
                        <th>SL <span style="color:#aaa;">&#8597;</span></th>
                        <th>Tag Manager ID <span style="color:#aaa;">&#8597;</span></th>
                        <th>Status <span style="color:#aaa;">&#8597;</span></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagmanagers as $index => $item)
                    <tr>
                        <td>{{ $tagmanagers->firstItem() + $index }}</td>
                        <td>{{ $item->tag_manager_id }}</td>
                        <td>
                            @if($item->status)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                {{-- Toggle Status --}}
                                <form action="{{ route('admin.googletagmanager.toggle-status', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-action btn-toggle" title="Toggle Status">
                                        <i class="fas fa-tag"></i>
                                    </button>
                                </form>

                                {{-- Edit --}}
                                <a href="{{ route('admin.googletagmanager.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('admin.googletagmanager.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this Tag Manager?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Delete">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:30px; color:#aaa; font-size:14px;">
                            <i class="fas fa-inbox" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                            No Tag Manager found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                @if($tagmanagers->total() > 0)
                    Showing {{ $tagmanagers->firstItem() }} to {{ $tagmanagers->lastItem() }} of {{ $tagmanagers->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </div>
            <div class="dt-pagination">
                {{-- Prev --}}
                @if($tagmanagers->onFirstPage())
                    <span class="page-btn" style="cursor:default; opacity:0.4;">&#8249;</span>
                @else
                    <a href="{{ $tagmanagers->previousPageUrl() }}" class="page-btn">&#8249;</a>
                @endif

                {{-- Page numbers --}}
                @for($i = 1; $i <= $tagmanagers->lastPage(); $i++)
                    <a href="{{ $tagmanagers->url($i) }}" class="page-btn {{ $tagmanagers->currentPage() == $i ? 'active' : '' }}">{{ $i }}</a>
                @endfor

                {{-- Next --}}
                @if($tagmanagers->hasMorePages())
                    <a href="{{ $tagmanagers->nextPageUrl() }}" class="page-btn">&#8250;</a>
                @else
                    <span class="page-btn" style="cursor:default; opacity:0.4;">&#8250;</span>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
    function searchTable() {
        const input = document.getElementById('tableSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#gtmTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }

    function copyTable() {
        const rows = document.querySelectorAll('#gtmTable tbody tr');
        let text = '';
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = [];
            cells.forEach((cell, i) => { if(i < 3) rowData.push(cell.innerText.trim()); });
            text += rowData.join('\t') + '\n';
        });
        navigator.clipboard.writeText(text).then(() => alert('Table copied to clipboard!'));
    }

    function exportPDF() {
        alert('PDF export: integrate jsPDF or server-side PDF generation.');
    }
</script>

@endsection
