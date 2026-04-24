@extends('admin.master')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Pixels Manage</h4>
    <a href="{{ route('admin.pixels.create') }}" class="btn btn-primary rounded-pill px-4">Create</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">

        {{-- Toolbar --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <button class="btn btn-sm btn-outline-secondary" id="btnCopy">Copy</button>
            <button class="btn btn-sm btn-outline-secondary" id="btnPrint">Print</button>
            <button class="btn btn-sm btn-outline-secondary" id="btnCSV">CSV</button>
        </div>

        <table id="pixelsTable" class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>SL</th>
                    <th>Pixels ID</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pixels as $index => $pixel)
                <tr>
                    <td>{{ $pixels->firstItem() + $index }}</td>
                    <td>{{ $pixel->pixels_id }}</td>
                    <td>
                        @if($pixel->status)
                            <span class="badge rounded-pill"
                                  style="background:#b2f5ea;color:#00695c;font-size:.75rem;padding:4px 12px;">
                                Active
                            </span>
                        @else
                            <span class="badge rounded-pill"
                                  style="background:#ffe0e0;color:#c62828;font-size:.75rem;padding:4px 12px;">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">

                            {{-- Toggle Status --}}
                            <form action="{{ route('admin.pixels.toggle-status', $pixel) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-secondary" title="Toggle Status"
                                        style="width:34px;height:34px;padding:0;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('admin.pixels.edit', $pixel) }}"
                               class="btn btn-sm btn-primary" title="Edit"
                               style="width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.pixels.destroy', $pixel) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this pixel?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                        style="width:34px;height:34px;padding:0;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2.2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Laravel Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                Showing {{ $pixels->firstItem() }} to {{ $pixels->lastItem() }} of {{ $pixels->total() }} entries
            </small>
            {{ $pixels->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>




<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#pixelsTable').DataTable({
        paging: false,
        info: false,
        searching: true,
        ordering: true,
        dom: '<"d-none"f>rt',
        columnDefs: [{ orderable: false, targets: [3] }]
    });

    // Copy
    $('#btnCopy').on('click', function () {
        var rows = ['SL\tPixels ID\tStatus'];
        $('#pixelsTable tbody tr').each(function () {
            var c = $(this).find('td');
            rows.push($(c[0]).text().trim()+'\t'+$(c[1]).text().trim()+'\t'+$(c[2]).text().trim());
        });
        navigator.clipboard.writeText(rows.join('\n')).then(function(){ alert('Copied!'); });
    });

    // Print
    $('#btnPrint').on('click', function () {
        var win = window.open('','','width=900,height=600');
        var h = '<html><head><title>Pixels Manage</title>';
        h += '<style>body{font-family:sans-serif}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:8px}th{background:#f0f0f0}</style>';
        h += '</head><body><h2>Pixels Manage</h2><table><thead><tr><th>SL</th><th>Pixels ID</th><th>Status</th></tr></thead><tbody>';
        $('#pixelsTable tbody tr').each(function () {
            var c = $(this).find('td');
            h += '<tr><td>'+$(c[0]).text().trim()+'</td><td>'+$(c[1]).text().trim()+'</td><td>'+$(c[2]).text().trim()+'</td></tr>';
        });
        h += '</tbody></table></body></html>';
        win.document.write(h); win.document.close(); win.focus(); win.print();
    });

    // CSV
    $('#btnCSV').on('click', function () {
        var rows = ['"SL","Pixels ID","Status"'];
        $('#pixelsTable tbody tr').each(function () {
            var c = $(this).find('td');
            rows.push('"'+$(c[0]).text().trim()+'","'+$(c[1]).text().trim()+'","'+$(c[2]).text().trim()+'"');
        });
        var blob = new Blob([rows.join('\n')],{type:'text/csv'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a'); a.href=url; a.download='pixels.csv'; a.click();
        URL.revokeObjectURL(url);
    });
});
</script>
@endsection
