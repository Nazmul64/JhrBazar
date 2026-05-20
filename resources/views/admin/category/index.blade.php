@extends('admin.master')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Category List</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-danger px-4">
        <i class="bi bi-plus-circle-fill me-1"></i> Add Category
    </a>
</div>

{{-- Success / Error Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Table Card --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold text-muted">Categories</span>
            <button id="bulkDeleteBtn" class="btn btn-danger btn-sm px-3 d-none animate__animated animate__fadeIn" onclick="bulkDelete()">
                <i class="bi bi-trash-fill me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:40px"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                        <th style="width:60px">SL</th>
                        <th style="width:200px">Thumbnail</th>
                        <th>Name</th>
                        <th class="text-center" style="width:130px">Status</th>
                        <th class="text-end pe-4" style="width:120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                    <tr class="border-bottom">
                        <td class="ps-4">
                            <input type="checkbox" class="category-checkbox form-check-input" value="{{ $category->id }}">
                        </td>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($category->thumbnail)
                                <img src="{{ asset($category->thumbnail) }}"
                                     alt="{{ $category->name }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white"
                                     style="width:60px;height:60px;font-size:10px;">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $category->name }}</td>
                        <td class="text-center">
                            {{-- Toggle switch --}}
                            <form action="{{ route('admin.categories.toggle', $category->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                    <input class="form-check-input toggle-status"
                                           type="checkbox"
                                           role="switch"
                                           style="width:44px;height:22px;cursor:pointer;"
                                           {{ $category->is_active ? 'checked' : '' }}
                                           onchange="this.closest('form').submit()">
                                </div>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                               class="text-primary fs-5 me-2" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                  method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="border-0 bg-transparent text-danger fs-5 p-0 align-middle"
                                        title="Delete" onclick="deleteSingle(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-25"></i>
                            No categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkDeleteButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            selectAll.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const checkedCount = Array.from(checkboxes).filter(c => c.checked).length;
        selectedCount.textContent = checkedCount;
        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('d-none');
        } else {
            bulkDeleteBtn.classList.add('d-none');
        }
    }

    function deleteSingle(button) {
        const form = button.closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this category delete!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e8174a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    function bulkDelete() {
        const selectedIds = Array.from(checkboxes)
            .filter(c => c.checked)
            .map(c => c.value);

        if (selectedIds.length === 0) {
            Swal.fire('Error', 'Please select at least one category.', 'error');
            return;
        }

        Swal.fire({
            title: 'Delete Selected Categories?',
            text: `Are you sure you want to delete ${selectedIds.length} categories? This action is permanent!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e8174a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('admin.categories.bulkDelete') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#e8174a'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Failed to communicate with server', 'error');
                });
            }
        });
    }
</script>
@endsection

@push('styles')
<style>
    /* Pink/Red toggle switch matching screenshot */
    .form-check-input:checked {
        background-color: #e8174a !important;
        border-color:     #e8174a !important;
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(232,23,74,.25) !important;
    }
</style>
@endpush
