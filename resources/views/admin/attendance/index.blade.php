@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Attendance Register</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage daily attendance logs, work shifts, late minutes, and punch details.</p>
        </div>
        
        {{-- Month/Year Filter Form --}}
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="d-flex gap-2">
            <select name="month" class="form-select select-hrm">
                @for ($m=1; $m<=12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select select-hrm">
                @for ($y=date('Y')-2; $y<=date('Y')+2; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn-hrm-filter">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </form>
    </div>

    {{-- Alert --}}
    <div id="ajaxAlert" class="alert alert-success d-none rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><span id="ajaxAlertMsg"></span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0" style="color:#222;">Monthly Registry Sheets</h6>
                <div class="d-flex gap-3 text-muted flex-wrap" style="font-size:12.5px;">
                    <span><i class="fas fa-check-circle text-success me-1"></i> Checked = Present</span>
                    <span><i class="fas fa-times-circle text-danger me-1"></i> Unchecked = Absent</span>
                    <span class="text-premium"><i class="fas fa-info-circle me-1"></i> Click cells status label (P/A/L) to manage Time punches</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-nowrap table-bordered">
                    <thead>
                        <tr class="table-light-hrm">
                            <th class="ps-4 py-3 emp-th-fixed">Employee Details</th>
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                <th class="py-3 text-center day-th">{{ $day }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            {{-- Fixed Left Employee Column --}}
                            <td class="ps-4 emp-td-fixed">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $employee->profile_image ? asset($employee->profile_image) : asset('admin/images/default-avatar.png') }}"
                                         alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold" style="font-size:13.5px; color:#222;">{{ $employee->name }}</div>
                                        <span class="text-muted" style="font-size:11px;">{{ $employee->department->name ?? 'No Dept' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 30-Day Checkboxes --}}
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $dayStr = sprintf('%02d', $day);
                                    $fullDate = "$year-$month-$dayStr";
                                    
                                    // Check status from grouped collection
                                    $status = 'Absent';
                                    $hasPunch = false;
                                    if (isset($attendances[$employee->id][$day])) {
                                        $punchRecord = $attendances[$employee->id][$day]->first();
                                        $status = $punchRecord->status;
                                        $hasPunch = true;
                                    }
                                    $checked = ($status === 'Present' || $status === 'Late' || $status === 'Leave');
                                    
                                    // Colors based on Status
                                    $color = '#c62828';
                                    $char = 'A';
                                    if ($status === 'Present') { $color = '#2e7d32'; $char = 'P'; }
                                    elseif ($status === 'Late') { $color = '#f59e0b'; $char = 'L'; }
                                    elseif ($status === 'Leave') { $color = '#c2185b'; $char = 'LV'; }
                                @endphp
                                <td class="text-center py-2 day-td-cell">
                                    <div class="form-check d-inline-block m-0">
                                        <input class="form-check-input attendance-checkbox" 
                                               type="checkbox" 
                                               data-employee="{{ $employee->id }}" 
                                               data-date="{{ $fullDate }}"
                                               {{ $checked ? 'checked' : '' }}
                                               style="cursor: pointer; width: 1.2rem; height: 1.2rem;">
                                    </div>
                                    <div class="cell-status-label" 
                                         data-employee="{{ $employee->id }}" 
                                         data-date="{{ $fullDate }}" 
                                         data-name="{{ $employee->name }}"
                                         style="font-size:10px; font-weight:bold; color: {{ $color }}; cursor: pointer; text-decoration: underline;" 
                                         onclick="openPunchModal(this)">
                                        {{ $char }}
                                    </div>
                                </td>
                            @endfor
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="text-center py-5">
                                <i class="fas fa-user-clock fa-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted">No employees available for registry.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Advanced Punch Detail Modal --}}
    <div class="modal fade" id="punchLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="fw-bold text-premium" style="font-size:18px; margin-bottom: 2px;">Shift & Punch Card Editor</h5>
                        <span id="modalEmployeeHeader" class="text-muted" style="font-size:13px; font-weight: 500;">Employee</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.attendance.updatePunch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" id="modal_employee_id">
                    <input type="hidden" name="date" id="modal_date">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="hrm-lbl">Work Status</label>
                                <select name="status" id="modal_status" class="form-select hrm-in" required>
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Late">Late</option>
                                    <option value="Leave">Leave</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Active Shift</label>
                                <input type="text" name="shift_name" id="modal_shift_name" class="form-control hrm-in" required>
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Clock Punch In Time</label>
                                <input type="time" name="clock_in" id="modal_clock_in" class="form-control hrm-in">
                            </div>
                            <div class="col-md-6">
                                <label class="hrm-lbl">Clock Punch Out Time</label>
                                <input type="time" name="clock_out" id="modal_clock_out" class="form-control hrm-in">
                            </div>

                            {{-- Dynamic metadata columns --}}
                            <div class="col-12 mt-2">
                                <div class="bg-light p-3 rounded-3 border">
                                    <h6 class="fw-bold mb-2 text-muted" style="font-size: 11px; text-transform: uppercase;">Timecard Telemetry Details</h6>
                                    <div class="row g-2" style="font-size: 12.5px;">
                                        <div class="col-6">
                                            <strong>Hours Worked:</strong> <span id="telemetry_hours" class="text-primary fw-bold">—</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Lateness:</strong> <span id="telemetry_lateness" class="text-danger fw-bold">—</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Logged IP:</strong> <span id="telemetry_ip" class="text-muted">—</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Coordinates:</strong> <span id="telemetry_coords" class="text-muted">—</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="hrm-lbl">Admin Notes</label>
                                <textarea name="note" id="modal_note" rows="2" class="form-control hrm-in" placeholder="Enter status reasons or clock references..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="font-size:13px; border-radius:8px;">Close</button>
                        <button type="submit" class="btn-hrm-save">Update Timecard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
.select-hrm {
    width: 140px;
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.btn-hrm-filter {
    background: linear-gradient(135deg, #1a1a2e, #162447);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 13.5px;
    font-weight: 500;
    transition: all .2s;
}
.btn-hrm-filter:hover {
    transform: translateY(-1px);
    opacity: .95;
    color: #fff;
}
.table-light-hrm {
    background: #f8fafc;
}
.emp-th-fixed {
    position: sticky;
    left: 0;
    background: #f8fafc !important;
    z-index: 2;
    min-width: 200px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.02);
    border-right: 1px solid #dee2e6 !important;
    font-size: 13px;
    font-weight: 600;
    color: #555;
}
.emp-td-fixed {
    position: sticky;
    left: 0;
    background: #fff !important;
    z-index: 2;
    box-shadow: 2px 0 5px rgba(0,0,0,0.02);
    border-right: 1px solid #dee2e6 !important;
}
.day-th {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    width: 45px;
    text-align: center;
}
.day-td-cell {
    width: 45px;
    border-right: 1px solid #f1f5f9;
}
.day-td-cell:hover {
    background: #f8fafc;
}
.attendance-checkbox:checked {
    background-color: #2e7d32;
    border-color: #2e7d32;
}
.attendance-checkbox {
    border-color: #ced4da;
}
.text-premium { color: #c2185b !important; }
.hrm-lbl { font-size: 12.5px; font-weight: 500; color: #333; margin-bottom: 4px; display: block; }
.hrm-in { font-size: 13.5px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 12px; }
.btn-hrm-save {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 24px; font-size: 13.5px; font-weight: 500;
}
.btn-hrm-save:hover { opacity: .95; }
.bg-soft-premium {
    background: rgba(233,30,99,0.1);
    color: #c2185b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.attendance-checkbox');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const employeeId = this.dataset.employee;
            const date = this.dataset.date;
            const isChecked = this.checked;
            const status = isChecked ? 'Present' : 'Absent';
            
            const cell = this.closest('.day-td-cell');
            const label = cell.querySelector('.cell-status-label');

            label.textContent = '...';

            fetch("{{ route('admin.attendance.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    date: date,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'Present') {
                        label.textContent = 'P';
                        label.style.color = '#2e7d32';
                        this.checked = true;
                    } else {
                        label.textContent = 'A';
                        label.style.color = '#c62828';
                        this.checked = false;
                    }
                    
                    const alertBox = document.getElementById('ajaxAlert');
                    const alertMsg = document.getElementById('ajaxAlertMsg');
                    alertMsg.textContent = `Attendance updated for date ${date}.`;
                    alertBox.classList.remove('d-none');
                    
                    setTimeout(() => {
                        alertBox.classList.add('d-none');
                    }, 2000);
                } else {
                    alert('Error updating attendance record. Please reload.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection failure. Check credentials.');
            });
        });
    });
});

// Open and load Dynamic Punch Logs detail Modal
function openPunchModal(element) {
    const employeeId = element.dataset.employee;
    const date = element.dataset.date;
    const employeeName = element.dataset.name;

    // Prefill modal placeholders
    document.getElementById('modalEmployeeHeader').textContent = `${employeeName} — Log Date: ${date}`;
    document.getElementById('modal_employee_id').value = employeeId;
    document.getElementById('modal_date').value = date;

    // Load dynamic data via Fetch API
    fetch(`{{ route('admin.attendance.index') }}/punch-details?employee_id=${employeeId}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                document.getElementById('modal_status').value = data.status;
                document.getElementById('modal_shift_name').value = data.shift_name;
                document.getElementById('modal_clock_in').value = data.clock_in;
                document.getElementById('modal_clock_out').value = data.clock_out;
                document.getElementById('modal_note').value = data.note;

                document.getElementById('telemetry_hours').textContent = `${data.working_hours} hours`;
                document.getElementById('telemetry_lateness').textContent = data.late_minutes > 0 ? `${data.late_minutes} mins` : 'On Time';
                document.getElementById('telemetry_ip').textContent = data.device_ip;
                document.getElementById('telemetry_coords').textContent = data.location_coordinates;
            } else {
                // Pre-fill default values
                document.getElementById('modal_status').value = 'Absent';
                document.getElementById('modal_shift_name').value = 'Standard Day Shift';
                document.getElementById('modal_clock_in').value = '';
                document.getElementById('modal_clock_out').value = '';
                document.getElementById('modal_note').value = '';

                document.getElementById('telemetry_hours').textContent = '—';
                document.getElementById('telemetry_lateness').textContent = '—';
                document.getElementById('telemetry_ip').textContent = 'N/A';
                document.getElementById('telemetry_coords').textContent = 'N/A';
            }

            // Launch Modal
            const myModal = new bootstrap.Modal(document.getElementById('punchLogModal'));
            myModal.show();
        })
        .catch(err => {
            console.error(err);
            alert('Failed to retrieve Time punch metadata details.');
        });
}
</script>
@endsection
