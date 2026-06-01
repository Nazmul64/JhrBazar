@extends('admin.master')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e;font-size:22px;">Expense Categories</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage categories for office expenses.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.hrm.expense.category.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="d-flex gap-2">
                    <input name="name" class="form-control" style="font-size:13.5px; border-radius:8px;" placeholder="New category name" required>
                    <input name="color" type="color" value="#6366f1" style="width:50px;height:38px;padding:3px;border-radius:8px;border:1px solid #ccc;">
                    <button class="btn btn-primary px-4" style="border-radius:8px;font-size:13.5px;background:linear-gradient(135deg,#6366f1,#4f46e5);border:none;">Add</button>
                </div>
            </form>
            <ul class="list-group">
                @foreach($categories as $cat)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-start-0 border-end-0" style="border-color:#f1f5f9;">
                        <div class="d-flex align-items-center">
                            <span class="badge me-2" style="background:{{ $cat->color }};width:12px;height:12px;display:inline-block;border-radius:3px"></span>
                            <span class="fw-medium text-dark" style="font-size:14px;">{{ $cat->name }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 px-3" style="border-radius:6px; font-size:12px;"
                                    onclick="openEditModal('{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->color }}')">
                                Edit
                            </button>
                            <form action="{{ route('admin.hrm.expense.category.destroy', $cat->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger px-3" style="border-radius:6px; font-size:12px;" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel" style="color:#1a1a2e; font-size:18px;">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12.5px;font-weight:500;color:#333;">Category Name *</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" style="font-size:13.5px;border-radius:8px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12.5px;font-weight:500;color:#333;">Category Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" id="editCategoryColor" class="form-control form-control-color" style="width:50px;height:38px;padding:3px;border-radius:8px;border:1px solid #ccc;">
                            <span class="text-muted" style="font-size:12px;">Choose a color marker for this category.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-3" style="border-radius:8px;font-size:13px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-3" style="border-radius:8px;font-size:13px;background:linear-gradient(135deg,#6366f1,#4f46e5);border:none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, color) {
    var form = document.getElementById('editCategoryForm');
    form.action = "{{ url('admin/hrm/expense-categories') }}/" + id;
    
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryColor').value = color || '#6366f1';
    
    var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}
</script>
@endsection
