@extends('admin.master')

@section('content')
<style>
    .ibm-wrapper { padding: 10px 0; }
    .ibm-page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }

    .ibm-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 28px; }

    .ibm-form-group { margin-bottom: 20px; }
    .ibm-form-label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
    .ibm-form-label span { color: #e53e3e; }

    .ibm-form-control {
        width: 100%;
        padding: 11px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        outline: none;
        box-sizing: border-box;
        font-family: inherit;
        transition: border 0.2s, box-shadow 0.2s;
        background: #fff;
    }
    .ibm-form-control:focus { border-color: #20c997; box-shadow: 0 0 0 3px rgba(32,201,151,0.12); }
    .ibm-form-control.is-invalid { border-color: #e53e3e; }
    textarea.ibm-form-control { min-height: 110px; resize: vertical; line-height: 1.6; }
    .invalid-feedback-custom { color: #e53e3e; font-size: 12px; margin-top: 5px; display: block; }

    .btn-submit {
        background: #20c997;
        color: #fff;
        border: none;
        padding: 11px 30px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #17a97f; }

    .ibm-divider { border: none; border-top: 1px solid #f0f0f0; margin: 28px 0; }

    /* DataTable toolbar */
    .ibm-toolbar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
    .ibm-btn-tool {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 13px;
        cursor: pointer;
        color: #444;
        transition: background 0.15s;
    }
    .ibm-btn-tool:hover { background: #e8e8e8; }
    .ibm-search-wrap { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .ibm-search-wrap label { font-size: 13px; color: #555; font-weight: 500; }
    .ibm-search-input {
        padding: 7px 12px;
        border: 1px solid #ddd;
        border-radius: 7px;
        font-size: 13px;
        outline: none;
        transition: border 0.2s;
    }
    .ibm-search-input:focus { border-color: #20c997; }

    /* Table */
    .ibm-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .ibm-table thead tr { background: #f8f8f8; }
    .ibm-table th { padding: 12px 14px; text-align: left; font-weight: 600; color: #555; font-size: 13px; border-bottom: 2px solid #eee; white-space: nowrap; }
    .ibm-table td { padding: 11px 14px; color: #333; border-bottom: 1px solid #f2f2f2; vertical-align: middle; }
    .ibm-table tbody tr:hover { background: #fafafa; }
    .ibm-table .no-data { text-align: center; color: #999; padding: 30px; }

    /* Badges */
    .badge-active { background: #d4f5ec; color: #0d9e6e; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-inactive { background: #fde8e8; color: #e53e3e; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Action buttons */
    .btn-edit { background: #6c63ff; color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s; }
    .btn-edit:hover { background: #5a52cc; color: #fff; }
    .btn-delete { background: #e53e3e; color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s; }
    .btn-delete:hover { background: #c0392b; }
    .btn-toggle-on  { background: #20c997; color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-toggle-off { background: #aaa;     color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }

    .action-group { display: flex; gap: 6px; flex-wrap: wrap; }

    /* Pagination info */
    .ibm-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; flex-wrap: wrap; gap: 8px; }
    .ibm-info { font-size: 13px; color: #888; }
    .ibm-pag { display: flex; gap: 6px; }
    .ibm-pag-btn { background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; padding: 5px 12px; font-size: 13px; cursor: pointer; color: #444; }
    .ibm-pag-btn:hover { background: #e0e0e0; }

    /* Alerts */
    .alert-success-custom { background: #d4f5ec; color: #0d9e6e; border-left: 4px solid #0d9e6e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
    .alert-error-custom   { background: #fde8e8; color: #e53e3e; border-left: 4px solid #e53e3e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
</style>

<div class="ibm-wrapper">

    <div class="ibm-page-title">Ip Block Manage</div>

    @if(session('success'))
        <div class="alert-success-custom"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error-custom"><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</div>
    @endif

    <div class="ibm-card">

        {{-- ═══ CREATE FORM ═══ --}}
        <form action="{{ route('admin.Ipblockmanage.store') }}" method="POST" id="ipBlockForm">
            @csrf

            <div class="ibm-form-group">
                <label class="ibm-form-label" for="ip_address">IP No <span>*</span></label>
                <input
                    type="text"
                    name="ip_address"
                    id="ip_address"
                    class="ibm-form-control @error('ip_address') is-invalid @enderror"
                    value="{{ old('ip_address') }}"
                    placeholder="e.g. 192.168.1.1"
                />
                @error('ip_address')
                    <span class="invalid-feedback-custom">{{ $message }}</span>
                @enderror
            </div>

            <div class="ibm-form-group">
                <label class="ibm-form-label" for="reason">Reason <span>*</span></label>
                <textarea
                    name="reason"
                    id="reason"
                    class="ibm-form-control @error('reason') is-invalid @enderror"
                    placeholder="Enter reason for blocking this IP..."
                >{{ old('reason') }}</textarea>
                @error('reason')
                    <span class="invalid-feedback-custom">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Submit</button>
        </form>

        <hr class="ibm-divider">

        {{-- ═══ TABLE TOOLBAR ═══ --}}
        <div class="ibm-toolbar">
            <button class="ibm-btn-tool" onclick="copyTable()">Copy</button>
            <button class="ibm-btn-tool" onclick="window.print()">Print</button>
            <button class="ibm-btn-tool" onclick="exportPDF()">PDF</button>
            <div class="ibm-search-wrap">
                <label>Search:</label>
                <input type="text" class="ibm-search-input" id="tableSearch" placeholder="" oninput="filterTable(this.value)" />
            </div>
        </div>

        {{-- ═══ TABLE ═══ --}}
        <table class="ibm-table" id="ipTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>IP</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="ipTableBody">
                @forelse($ipblocks as $index => $ipblock)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code style="background:#f5f5f5; padding:2px 8px; border-radius:4px;">{{ $ipblock->ip_address }}</code></td>
                        <td>{{ $ipblock->reason }}</td>
                        <td>
                            @if($ipblock->is_active)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                {{-- Edit --}}
                                <a href="{{ route('admin.Ipblockmanage.edit', $ipblock->id) }}" class="btn-edit">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                {{-- Toggle --}}
                                <form action="{{ route('admin.Ipblockmanage.toggleStatus', $ipblock->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $ipblock->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                        <i class="bi bi-toggle-{{ $ipblock->is_active ? 'on' : 'off' }}"></i>
                                        {{ $ipblock->is_active ? 'On' : 'Off' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('admin.Ipblockmanage.destroy', $ipblock->id) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Remove this IP block?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-data">No data available in table</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ═══ FOOTER ═══ --}}
        <div class="ibm-footer">
            <div class="ibm-info" id="tableInfo">
                Showing {{ $ipblocks->count() > 0 ? '1' : '0' }} to {{ $ipblocks->count() }} of {{ $ipblocks->count() }} entries
            </div>
        </div>

    </div>
</div>

<script>
    // Search filter
    function filterTable(query) {
        const rows = document.querySelectorAll('#ipTableBody tr');
        let visible = 0;
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const match = text.includes(query.toLowerCase());
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('tableInfo').innerText =
            `Showing ${visible} of {{ $ipblocks->count() }} entries`;
    }

    // Copy table
    function copyTable() {
        const rows = document.querySelectorAll('#ipTableBody tr');
        let text = 'SL\tIP\tReason\tStatus\n';
        rows.forEach((row, i) => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 1) {
                text += `${cells[0].innerText}\t${cells[1].innerText}\t${cells[2].innerText}\t${cells[3].innerText}\n`;
            }
        });
        navigator.clipboard.writeText(text).then(() => alert('Table copied to clipboard!'));
    }

    // PDF export (print-based)
    function exportPDF() { window.print(); }
</script>

@endsection
