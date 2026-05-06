@extends('admin.master')
@section('title', 'Withdraw Requests')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Withdraw Requests</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.withdraws.settings') }}" class="btn btn-primary"><i class="ri-settings-line me-1"></i> Withdraw Settings</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Seller</th>
                                    <th>Amount</th>
                                    <th>Bank Info</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdraws as $key => $item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        <h6 class="mb-0">{{ $item->seller->name }}</h6>
                                        <small>{{ $item->seller->email }}</small>
                                    </td>
                                    <td><h5 class="text-primary mb-0">৳{{ number_format($item->amount, 2) }}</h5></td>
                                    <td>
                                        <strong>{{ $item->bank->name }}</strong><br>
                                        <small>Acc: {{ $item->account_name }} ({{ $item->account_number }})</small><br>
                                        <small>Contact: {{ $item->contact_number }}</small>
                                    </td>
                                    <td>{{ $item->created_at->format('d M, Y') }}<br><small>{{ $item->created_at->diffForHumans() }}</small></td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#actionModal{{ $item->id }}">
                                                Take Action
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="actionModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Withdraw Action: #{{ $item->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.withdraws.status', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="approved">Approve</option>
                                                                        <option value="rejected">Reject</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Admin Note</label>
                                                                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Add a note..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Submit Action</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <button class="btn btn-light btn-sm" disabled>No Action</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $withdraws->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
