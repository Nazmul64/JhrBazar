@extends('admin.master')

@section('content')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Page Manage</h4>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary rounded-pill px-4">
            Create
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- DataTable Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <button class="btn btn-sm btn-secondary me-1" onclick="copyTable()">Copy</button>
                    <button class="btn btn-sm btn-secondary me-1" onclick="window.print()">Print</button>
                    <button class="btn btn-sm btn-secondary" onclick="exportPDF()">PDF</button>
                </div>
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0">Search:</label>
                    <input type="text" id="searchInput" class="form-control form-control-sm" style="width:200px"
                           onkeyup="searchTable()" placeholder="">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pageTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">SL <span class="text-muted">↕</span></th>
                            <th>Name <span class="text-muted">↕</span></th>
                            <th>Title <span class="text-muted">↕</span></th>
                            <th>Status <span class="text-muted">↕</span></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $index => $page)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $page->name }}</td>
                                <td>{{ $page->title }}</td>
                                <td>
                                    @if($page->status == 1)
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- View --}}
                                    <a href="{{ route('admin.pages.show', $page->id) }}"
                                       class="btn btn-sm btn-secondary me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pages.edit', $page->id) }}"
                                       class="btn btn-sm btn-warning me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.pages.destroy', $page->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this page?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No pages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


<script>
    // Simple table search
    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#pageTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
        });
    }

    // Copy table to clipboard
    function copyTable() {
        const table = document.getElementById('pageTable');
        const range = document.createRange();
        range.selectNode(table);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        alert('Table copied to clipboard!');
    }

    // Export PDF using browser print
    function exportPDF() {
        window.print();
    }
</script>


@endsection
