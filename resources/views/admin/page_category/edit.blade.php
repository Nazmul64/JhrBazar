@extends('admin.master')

@section('content')

<style>
:root {
    --accent:#4361ee; --text:#1a1f36; --muted:#6b7a99; --border:#e4e9f2; --white:#ffffff; --radius:8px; --bg:#f0f2f5;
}
.pc-page{padding:24px;background:var(--bg);min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;}
.pc-card{background:var(--white);border-radius:var(--radius);box-shadow:0 1px 4px rgba(0,0,0,.07);max-width:600px;margin:0 auto;overflow:hidden;}
.pc-card-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.pc-card-title{font-size:16px;font-weight:800;color:var(--text);margin:0;}
.pc-card-body{padding:24px;}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:700;color:var(--text);margin-bottom:8px;}
.form-input{width:100%;height:42px;border:1.5px solid var(--border);border-radius:6px;padding:0 12px;font-size:14px;outline:none;transition:border-color .15s;}
.form-input:focus{border-color:var(--accent);}
.btn-submit{width:100%;height:44px;background:var(--accent);color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer;}
.btn-submit:hover{opacity:.9;}
</style>

<div class="pc-page">
    <div class="pc-card">
        <div class="pc-card-head">
            <h2 class="pc-card-title">Edit Page Category</h2>
            <a href="{{ route('admin.page_categories.index') }}" style="font-size:13px;color:var(--accent);text-decoration:none;font-weight:600;">Back to List</a>
        </div>
        <div class="pc-card-body">
            <form action="{{ route('admin.page_categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-input" value="{{ $category->name }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Update Category</button>
            </form>
        </div>
    </div>
</div>

@endsection
