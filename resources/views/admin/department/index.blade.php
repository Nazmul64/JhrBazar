@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Departments & Designations</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage corporate departments, job roles, grading systems, and organizational layouts.</p>
        </div>
        <button class="btn-hrm-add" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="fas fa-plus me-1"></i> Add Department
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Validation Failed</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Grid Layout of Departments --}}
    <div class="row g-4">
        @forelse($departments as $dept)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 dept-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-premium-color" style="font-size:17px;">{{ $dept->name }}</h5>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size:11px;">
                                <i class="fas fa-users me-1 text-muted"></i> {{ $dept->employees->count() }} Employees
                            </span>
                        </div>
                        <form action="{{ route('admin.department.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>

                    <p class="text-muted mb-4 text-justify" style="font-size:12.5px; line-height: 1.4;">
                        {{ $dept->description ?? 'No description provided.' }}
                    </p>

                    <hr class="mt-0 mb-3 opacity-25">

                    <h6 class="fw-bold mb-2 text-secondary" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Designations & Titles</h6>
                    
                    <div class="designation-list flex-grow-1">
                        @forelse($dept->designations as $desig)
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light mb-2 hover-desig">
                            <div>
                                <span class="fw-bold d-block text-dark" style="font-size:13px;">{{ $desig->name }}</span>
                                @if($desig->grade)
                                    <span class="badge bg-soft-premium" style="font-size:9px;">{{ $desig->grade }}</span>
                                @endif
                            </div>
                            <form action="{{ route('admin.department.designation.destroy', $desig->id) }}" method="POST" onsubmit="return confirm('Delete this designation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger opacity-50 hover-opacity-100 p-0 border-0 bg-transparent" title="Delete Designation">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                        </div>
                        @empty
                        <span class="text-muted" style="font-size:12px; font-style:italic;">No roles configured.</span>
                        @endforelse
                    </div>

                    <button class="btn btn-sm btn-outline-premium-dotted mt-3 w-100" data-bs-toggle="modal" data-bs-target="#addDesignationModal_{{ $dept->id }}">
                        <i class="fas fa-plus-circle me-1"></i> Add Designation Role
                    </button>
                </div>
            </div>
        </div>

        {{-- Add Designation Modal --}}
        <div class="modal fade" id="addDesignationModal_{{ $dept->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="fw-bold text-premium-color" style="font-size:18px;">Add Job Designation to {{ $dept->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.department.designation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ $dept->id }}">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="hrm-lbl">Designation Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control hrm-in" required placeholder="e.g. Senior Software Engineer, Operations Executive">
                                </div>
                                <div class="col-12">
                                    <label class="hrm-lbl">Job Grade / Scale</label>
                                    <input type="text" name="grade" class="form-control hrm-in" placeholder="e.g. Grade A, Junior, Lead">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13px; border-radius:8px;">Close</button>
                            <button type="submit" class="btn-hrm-save">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No organizational departments configured yet.</h5>
        </div>
        @endforelse
    </div>

    {{-- Add Department Modal --}}
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-premium-color" style="font-size:18px;">Configure Corporate Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.department.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="hrm-lbl">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control hrm-in" required placeholder="e.g. IT & Software Development, Accounts & Finance">
                            </div>
                            <div class="col-12">
                                <label class="hrm-lbl">Description</label>
                                <textarea name="description" rows="3" class="form-control hrm-in" placeholder="Enter department objectives, targets or functions..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13px; border-radius:8px;">Close</button>
                        <button type="submit" class="btn-hrm-save">Create Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
.text-premium-color { color: #1a1a2e; }
.btn-hrm-add {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff !important; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-hrm-add:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.dept-card {
    transition: all .2s ease;
    border: 1px solid #f0f0f0 !important;
}
.dept-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06) !important;
}
.hover-desig {
    transition: background .15s;
}
.hover-desig:hover {
    background: #edf2f7 !important;
}
.bg-soft-premium {
    background: rgba(233,30,99,0.1);
    color: #c2185b;
}
.btn-outline-premium-dotted {
    border: 1.5px dashed #c2185b;
    color: #c2185b;
    background: transparent;
    font-size: 12.5px;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px;
    transition: all .2s;
}
.btn-outline-premium-dotted:hover {
    background: rgba(233,30,99,0.05);
    color: #c2185b;
}
.hrm-lbl { font-size: 12.5px; font-weight: 500; color: #333; margin-bottom: 4px; display: block; }
.hrm-in { font-size: 13.5px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 12px; }
.btn-hrm-save {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 24px; font-size: 13.5px; font-weight: 500;
}
.btn-hrm-save:hover { opacity: .95; }
</style>
@endsection
