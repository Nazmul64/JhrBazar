@extends('admin.master')

@section('content')
<div class="container-fluid py-4">

    {{-- Success / Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0" style="font-size: 1.1rem; color: #222;">Customer Contact Messages</h5>
                <span class="badge bg-secondary px-2.5 py-1.5 fs-7">{{ $messages->total() }} Messages</span>
            </div>
            <hr class="mb-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%">#</th>
                            <th scope="col" style="width: 20%">Customer Name</th>
                            <th scope="col" style="width: 15%">Phone Number</th>
                            <th scope="col" style="width: 20%">Subject</th>
                            <th scope="col" style="width: 25%">Message</th>
                            <th scope="col" style="width: 10%">Submitted At</th>
                            <th scope="col" class="text-end" style="width: 5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $key => $message)
                            <tr>
                                <td>{{ $messages->firstItem() + $key }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $message->full_name }}</span>
                                </td>
                                <td>
                                    <a href="tel:{{ $message->phone_number }}" class="text-decoration-none text-muted">
                                        <i class="bi bi-telephone me-1"></i>{{ $message->phone_number }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium">{{ $message->subject }}</span>
                                </td>
                                <td>
                                    <div style="max-height: 80px; overflow-y: auto; white-space: pre-wrap; font-size: 0.9rem;" class="text-muted">
                                        {{ $message->message }}
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $message->created_at->format('d M Y, h:i A') }}</span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.contact_messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-envelope-open fs-2 d-block mb-2 text-secondary"></i>
                                    No messages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $messages->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
