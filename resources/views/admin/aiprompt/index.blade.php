@extends('admin.master')

@section('content')

<style>
    /* ── Page ──────────────────────────────────────────── */
    .aip-page-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 24px;
    }

    /* ── Two-column outer grid ─────────────────────────── */
    .aip-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 992px) { .aip-grid { grid-template-columns: 1fr; } }

    /* ── Left column stacks two cards ──────────────────── */
    .aip-left { display: flex; flex-direction: column; gap: 20px; }

    /* ── Card ──────────────────────────────────────────── */
    .aip-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
    }

    /* ── Card header bar ───────────────────────────────── */
    .aip-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 20px;
        border-bottom: 1px solid #eceef3;
        font-size: .92rem;
        font-weight: 700;
        color: #1a1a2e;
    }
    .aip-card-header i { font-size: .85rem; color: #6c6c8a; }

    /* ── Card body ─────────────────────────────────────── */
    .aip-card-body { padding: 22px 22px 24px; }

    /* ── Info note box ─────────────────────────────────── */
    .aip-note {
        background: #fdf8fb;
        border: 1px solid #f0dde9;
        border-radius: 9px;
        padding: 12px 16px;
        font-size: .87rem;
        color: #4a4a66;
        margin-bottom: 18px;
        line-height: 1.6;
    }
    .aip-note .kw {
        color: #e91e7a;
        font-style: italic;
        font-weight: 600;
    }

    /* ── Field label ───────────────────────────────────── */
    .aip-label {
        display: block;
        font-size: .88rem;
        font-weight: 600;
        color: #3d3d55;
        margin-bottom: 8px;
    }
    .aip-label span.req { color: #e91e7a; margin-left: 2px; }

    /* ── Textarea ──────────────────────────────────────── */
    .aip-textarea {
        width: 100%;
        min-height: 140px;
        padding: 12px 14px;
        border: 1px solid #e0e3ec;
        border-radius: 9px;
        font-size: .88rem;
        color: #3d3d55;
        background: #fff;
        resize: vertical;
        outline: none;
        line-height: 1.65;
        transition: border-color .2s, box-shadow .2s;
        font-family: inherit;
    }
    .aip-textarea:focus {
        border-color: #e91e7a;
        box-shadow: 0 0 0 3px rgba(233,30,122,.08);
    }
    .aip-textarea::placeholder { color: #b0b0c8; }

    /* ── Save button (active - pink) ───────────────────── */
    .btn-aip-save {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: none;
        border-radius: 8px;
        padding: 11px 26px;
        font-size: .92rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, transform .15s, box-shadow .2s;
        float: right;
        margin-top: 16px;
    }
    .btn-aip-save.active {
        background: #e91e7a;
        color: #fff;
    }
    .btn-aip-save.active:hover {
        background: #c9165f;
        box-shadow: 0 4px 14px rgba(233,30,122,.35);
        transform: translateY(-1px);
    }
    .btn-aip-save.inactive {
        background: #6c757d;
        color: #fff;
        cursor: not-allowed;
        opacity: .75;
    }
    .btn-aip-save:active { transform: translateY(0); }

    /* ── Alert flash ───────────────────────────────────── */
    .aip-alert {
        padding: 11px 16px;
        border-radius: 8px;
        font-size: .87rem;
        font-weight: 500;
        margin-bottom: 14px;
    }
    .aip-alert.success { background: #e8f8f1; color: #1a7a4a; border: 1px solid #b2dfcc; }
    .aip-alert.error   { background: #fef0f4; color: #b91c5c; border: 1px solid #f5b8d0; }

    /* clearfix */
    .aip-form-footer { overflow: hidden; padding-bottom: 2px; }
</style>

<div class="container-fluid py-2">

    {{-- Page title --}}
    <h4 class="aip-page-title">
        <i class="fas fa-cog"></i> Ai Prompt
    </h4>

    {{-- Flash messages --}}
    @if(session('success_product'))
        <div class="aip-alert success"><i class="fas fa-check-circle me-1"></i> {{ session('success_product') }}</div>
    @endif
    @if(session('success_page'))
        <div class="aip-alert success"><i class="fas fa-check-circle me-1"></i> {{ session('success_page') }}</div>
    @endif
    @if(session('success_blog'))
        <div class="aip-alert success"><i class="fas fa-check-circle me-1"></i> {{ session('success_blog') }}</div>
    @endif
    @if($errors->any())
        <div class="aip-alert error"><i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}</div>
    @endif

    <div class="aip-grid">

        {{-- ══════════════════════════════════════════════
             LEFT COLUMN
        ══════════════════════════════════════════════ --}}
        <div class="aip-left">

            {{-- ── Card 1: Product Description ─────────── --}}
            <div class="aip-card">
                <div class="aip-card-header">
                    <i class="fas fa-expand-arrows-alt"></i>
                    Product Description Note
                </div>
                <div class="aip-card-body">
                    <div class="aip-note">
                        <strong>Note:</strong> Use <span class="kw">{product_name}</span> to insert the product's name, and <span class="kw">{short_description}</span> to insert the product's short description in the prompt.
                    </div>

                    <form action="{{ route('admin.aiprompt.update-product') }}" method="POST" id="formProduct">
                        @csrf
                        <label class="aip-label" for="product_description">
                            Product Description <span class="req">*</span>
                        </label>
                        <textarea
                            id="product_description"
                            name="product_description"
                            class="aip-textarea"
                            placeholder="Enter Product Description"
                            required
                        >{{ old('product_description', $setting->product_description) }}</textarea>

                        <div class="aip-form-footer">
                            <button type="submit" class="btn-aip-save active">
                                <i class="fas fa-save"></i> Save And Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Card 3: Blog Description ─────────────── --}}
            <div class="aip-card">
                <div class="aip-card-header">
                    <i class="fas fa-expand-arrows-alt"></i>
                    Blog Description Note
                </div>
                <div class="aip-card-body">
                    <div class="aip-note">
                        <strong>Note:</strong> If you use <span class="kw">{title}</span>, the blog title will be automatically retrieved and inserted into the main prompt.
                    </div>

                    <form action="{{ route('admin.aiprompt.update-blog') }}" method="POST" id="formBlog">
                        @csrf
                        <label class="aip-label" for="blog_description">
                            Blog Description <span class="req">*</span>
                        </label>
                        <textarea
                            id="blog_description"
                            name="blog_description"
                            class="aip-textarea"
                            placeholder="Enter Page Description"
                            required
                        >{{ old('blog_description', $setting->blog_description) }}</textarea>

                        <div class="aip-form-footer">
                            <button
                                type="submit"
                                class="btn-aip-save {{ $setting->blog_description ? 'active' : 'inactive' }}"
                                id="btnBlogSave"
                            >
                                <i class="fas fa-save"></i> Save And Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- /.aip-left --}}

        {{-- ══════════════════════════════════════════════
             RIGHT COLUMN
        ══════════════════════════════════════════════ --}}
        <div class="aip-right">

            {{-- ── Card 2: Page Description ─────────────── --}}
            <div class="aip-card">
                <div class="aip-card-header">
                    <i class="fas fa-expand-arrows-alt"></i>
                    Page Description Note
                </div>
                <div class="aip-card-body">
                    <div class="aip-note">
                        <strong>Note:</strong> If you use <span class="kw">{title}</span>, the page title will be automatically retrieved and inserted into the main prompt.
                    </div>

                    <form action="{{ route('admin.aiprompt.update-page') }}" method="POST" id="formPage">
                        @csrf
                        <label class="aip-label" for="page_description">
                            Page Description <span class="req">*</span>
                        </label>
                        <textarea
                            id="page_description"
                            name="page_description"
                            class="aip-textarea"
                            placeholder="Enter Page Description"
                            required
                        >{{ old('page_description', $setting->page_description) }}</textarea>

                        <div class="aip-form-footer">
                            <button type="submit" class="btn-aip-save active">
                                <i class="fas fa-save"></i> Save And Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- /.aip-right --}}

    </div>{{-- /.aip-grid --}}

</div>

{{-- ── Blog textarea → toggle button color dynamically ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const blogTextarea = document.getElementById('blog_description');
    const btnBlog      = document.getElementById('btnBlogSave');

    function toggleBlogBtn() {
        if (blogTextarea.value.trim().length > 0) {
            btnBlog.classList.remove('inactive');
            btnBlog.classList.add('active');
            btnBlog.disabled = false;
        } else {
            btnBlog.classList.remove('active');
            btnBlog.classList.add('inactive');
        }
    }

    blogTextarea.addEventListener('input', toggleBlogBtn);
    toggleBlogBtn(); // run on load
});
</script>

@endsection
