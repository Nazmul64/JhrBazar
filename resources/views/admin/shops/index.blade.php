@extends('admin.master')
@section('content')

<style>
* { box-sizing: border-box; }

.page-wrap {
    background: #f4f6f9;
    min-height: 100vh;
    padding: 24px;
}

/* ── Header ── */
.page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-head-left h4 {
    font-size: 20px;
    font-weight: 700;
    color: #222;
    margin: 0 0 3px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.page-head-left h4 i { color: #e83e8c; }
.page-head-left p { font-size: 13px; color: #888; margin: 0; }
.page-head-right { display: flex; align-items: center; gap: 10px; }

.view-btns {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
}
.view-btns button {
    background: #fff;
    border: none;
    padding: 7px 14px;
    cursor: pointer;
    color: #888;
    font-size: 15px;
    transition: all .2s;
    line-height: 1;
}
.view-btns button.active,
.view-btns button:hover { background: #e83e8c; color: #fff; }

.btn-add-shop {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e83e8c;
    color: #fff;
    border: none;
    padding: 9px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background .2s;
}
.btn-add-shop:hover { background: #d6317e; color: #fff; }

/* ── Success Alert ── */
.alert-ok {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    border-radius: 6px;
    padding: 11px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}
.alert-ok button {
    margin-left: auto; background: none; border: none;
    font-size: 20px; cursor: pointer; color: #155724; line-height: 1;
}

/* ══════════════════════════
   GRID VIEW
══════════════════════════ */
.shops-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 20px;
}

.shop-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8e8e8;
    overflow: hidden;
    transition: box-shadow .25s, transform .25s;
}
.shop-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.10);
    transform: translateY(-3px);
}

/* Banner */
.card-banner {
    height: 120px;
    overflow: hidden;
    background: #f0f0f0;
    position: relative;
}
.card-banner img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.card-banner-ph {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    color: #ccc; font-size: 32px;
}

/* Logo + Name — overlaps the banner */
.card-top {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    padding: 0 16px;
    margin-top: -26px;
    margin-bottom: 10px;
}
.card-logo {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 3px solid #fff;
    background: #f5f5f5;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 2px 10px rgba(0,0,0,.12);
    display: flex; align-items: center; justify-content: center;
}
.card-logo img { width: 100%; height: 100%; object-fit: cover; }
.card-logo i   { color: #bbb; font-size: 20px; }

.card-meta { overflow: hidden; padding-bottom: 2px; padding-top: 22px; }
.card-name {
    font-size: 15px; font-weight: 700; color: #1a1a1a;
    margin: 0 0 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-email {
    font-size: 12px; color: #999;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Info rows */
.card-rows { padding: 2px 16px 8px; }
.card-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 14px;
    color: #333;
    font-weight: 500;
}
.card-row:last-child { border-bottom: none; }
.card-row-label { color: #333; font-weight: 500; }

/* Count badges */
.badge-ct {
    display: inline-block;
    min-width: 36px;
    text-align: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}
.badge-dark { background: #555; color: #fff; }
.badge-pink { background: #e83e8c; color: #fff; }

/* Toggle switch */
.tog { position: relative; display: inline-block; width: 46px; height: 25px; }
.tog input { opacity: 0; width: 0; height: 0; }
.tog-track {
    position: absolute; inset: 0;
    background: #ccc; border-radius: 50px;
    cursor: pointer; transition: background .3s;
}
.tog-track::before {
    content: '';
    position: absolute;
    width: 19px; height: 19px;
    left: 3px; top: 3px;
    background: #fff; border-radius: 50%;
    transition: transform .3s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.tog input:checked + .tog-track { background: #e83e8c; }
.tog input:checked + .tog-track::before { transform: translateX(21px); }

/* Card actions */
.card-actions {
    display: flex;
    gap: 8px;
    padding: 0 16px 14px;
}
.btn-c-edit {
    flex: 1;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    background: #fff;
    border: 1px solid #e83e8c;
    color: #e83e8c;
    border-radius: 6px;
    padding: 8px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all .2s;
    cursor: pointer;
}
.btn-c-edit:hover { background: #e83e8c; color: #fff; }
.btn-c-del {
    display: inline-flex; align-items: center; justify-content: center;
    background: #fff;
    border: 1px solid #dc3545;
    color: #dc3545;
    border-radius: 6px;
    padding: 8px 13px;
    font-size: 13px; cursor: pointer; transition: all .2s;
}
.btn-c-del:hover { background: #dc3545; color: #fff; }

/* Empty state */
.shops-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 70px 20px;
    color: #aaa;
}
.shops-empty i { font-size: 52px; display: block; margin-bottom: 16px; color: #ddd; }
.shops-empty p { font-size: 15px; }
.shops-empty a { color: #e83e8c; text-decoration: none; font-weight: 600; }

/* ══════════════════════════
   LIST / TABLE VIEW
══════════════════════════ */
.table-wrap {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8e8e8;
    overflow: hidden;
}
.shops-tbl { width: 100%; border-collapse: collapse; }
.shops-tbl thead th {
    background: #f8f9fa;
    padding: 13px 16px;
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 2px solid #eee;
    white-space: nowrap;
    text-align: left;
}
.shops-tbl tbody td {
    padding: 13px 16px;
    font-size: 14px;
    color: #444;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle;
}
.shops-tbl tbody tr:last-child td { border-bottom: none; }
.shops-tbl tbody tr:hover { background: #fafafa; }

.tbl-shop-cell { display: flex; align-items: center; gap: 10px; }
.tbl-logo {
    width: 42px; height: 42px;
    border-radius: 50%;
    overflow: hidden;
    background: #f0f0f0;
    border: 1px solid #eee;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.tbl-logo img { width: 100%; height: 100%; object-fit: cover; }
.tbl-logo i   { color: #bbb; font-size: 15px; }
.tbl-shop-name  { font-weight: 700; color: #222; font-size: 14px; line-height: 1.3; }
.tbl-owner-name { font-weight: 600; color: #333; font-size: 14px; }
.tbl-owner-mail { font-size: 12px; color: #999; margin-top: 2px; }

.tbl-acts { display: flex; align-items: center; gap: 6px; }
.btn-t-edit {
    display: inline-flex; align-items: center; gap: 4px;
    background: #fff; border: 1px solid #e83e8c; color: #e83e8c;
    border-radius: 5px; padding: 5px 13px;
    font-size: 12px; font-weight: 600; text-decoration: none; transition: all .2s;
}
.btn-t-edit:hover { background: #e83e8c; color: #fff; }
.btn-t-del {
    display: inline-flex; align-items: center; justify-content: center;
    background: #fff; border: 1px solid #dc3545; color: #dc3545;
    border-radius: 5px; padding: 5px 10px;
    font-size: 12px; cursor: pointer; transition: all .2s;
}
.btn-t-del:hover { background: #dc3545; color: #fff; }

/* Pagination */
.pag-wrap { margin-top: 24px; display: flex; justify-content: center; }

/* Hide helper */
.d-none-v { display: none !important; }
</style>

<div class="page-wrap">

    {{-- Page Header --}}
    <div class="page-head">
        <div class="page-head-left">
            <h4><i class="fas fa-store"></i> Shops</h4>
            <p>This is a shops list.</p>
        </div>
        <div class="page-head-right">
            <div class="view-btns" id="viewToggle">
                <button class="active" data-view="grid" title="Grid view">
                    <i class="fas fa-th-large"></i>
                </button>
                <button data-view="list" title="List view">
                    <i class="fas fa-list-ul"></i>
                </button>
            </div>
            <a href="{{ route('admin.shops.create') }}" class="btn-add-shop">
                <i class="fas fa-plus"></i> Add Shop
            </a>
        </div>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert-ok" id="shopAlert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button onclick="this.closest('#shopAlert').remove()">&times;</button>
        </div>
    @endif

    {{-- ═══════ GRID VIEW ═══════ --}}
    <div id="gridView" class="shops-grid">
        @forelse($shops as $shop)
        <div class="shop-card">

            {{-- Banner --}}
            <div class="card-banner">
                @if($shop->banner_url)
                    <img src="{{ $shop->banner_url }}" alt="banner">
                @else
                    <div class="card-banner-ph"><i class="fas fa-image"></i></div>
                @endif
            </div>

            {{-- Logo + Name --}}
            <div class="card-top">
                <div class="card-logo">
                    @if($shop->logo_url)
                        <img src="{{ $shop->logo_url }}" alt="logo">
                    @else
                        <i class="fas fa-store"></i>
                    @endif
                </div>
                <div class="card-meta">
                    <div class="card-name">{{ $shop->name }}</div>
                    <div class="card-email">{{ $shop->user->email ?? '—' }}</div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card-rows">
                <div class="card-row">
                    <span class="card-row-label">Status</span>
                    <label class="tog">
                        <input type="checkbox"
                               class="shop-status-toggle"
                               {{ $shop->status ? 'checked' : '' }}
                               data-id="{{ $shop->id }}"
                               data-url="{{ route('admin.shops.toggle-status', $shop) }}">
                        <span class="tog-track"></span>
                    </label>
                </div>
                <div class="card-row">
                    <span class="card-row-label">Products</span>
                    <span class="badge-ct badge-dark">{{ $shop->products_count ?? 0 }}</span>
                </div>
                <div class="card-row">
                    <span class="card-row-label">Orders</span>
                    <span class="badge-ct badge-pink">{{ $shop->orders_count ?? 0 }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card-actions">
                <a href="{{ route('admin.shops.edit', $shop) }}" class="btn-c-edit">
                    <i class="fas fa-pen"></i> Edit
                </a>
                <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST"
                      onsubmit="return confirm('Delete this shop?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-c-del">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="shops-empty">
            <i class="fas fa-store-slash"></i>
            <p>No shops yet. <a href="{{ route('admin.shops.create') }}">Create your first shop →</a></p>
        </div>
        @endforelse
    </div>

    {{-- ═══════ LIST VIEW ═══════ --}}
    <div id="listView" class="d-none-v">
        <div class="table-wrap">
            <div style="overflow-x:auto">
                <table class="shops-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Shop</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th style="text-align:center">Products</th>
                            <th style="text-align:center">Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shops as $i => $shop)
                        <tr>
                            <td style="color:#bbb; font-size:13px; font-weight:600">
                                {{ $shops->firstItem() + $i }}
                            </td>
                            <td>
                                <div class="tbl-shop-cell">
                                    <div class="tbl-logo">
                                        @if($shop->logo_url)
                                            <img src="{{ $shop->logo_url }}" alt="">
                                        @else
                                            <i class="fas fa-store"></i>
                                        @endif
                                    </div>
                                    <span class="tbl-shop-name">{{ $shop->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="tbl-owner-name">{{ $shop->user->name ?? '—' }}</div>
                                <div class="tbl-owner-mail">{{ $shop->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <label class="tog">
                                    <input type="checkbox"
                                           class="shop-status-toggle"
                                           {{ $shop->status ? 'checked' : '' }}
                                           data-id="{{ $shop->id }}"
                                           data-url="{{ route('admin.shops.toggle-status', $shop) }}">
                                    <span class="tog-track"></span>
                                </label>
                            </td>
                            <td style="text-align:center">
                                <span class="badge-ct badge-dark">{{ $shop->products_count ?? 0 }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge-ct badge-pink">{{ $shop->orders_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="tbl-acts">
                                    <a href="{{ route('admin.shops.edit', $shop) }}" class="btn-t-edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST"
                                          style="display:inline"
                                          onsubmit="return confirm('Delete this shop?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-t-del">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:50px; color:#aaa">
                                <i class="fas fa-store-slash" style="font-size:32px; display:block; margin-bottom:12px; color:#ddd"></i>
                                No shops found.
                                <a href="{{ route('admin.shops.create') }}" style="color:#e83e8c; font-weight:600">Create one →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="pag-wrap">
        {{ $shops->links() }}
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── View toggle (grid / list)
    document.querySelectorAll('#viewToggle [data-view]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#viewToggle [data-view]').forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');

            var isGrid = this.dataset.view === 'grid';
            document.getElementById('gridView').classList.toggle('d-none-v', !isGrid);
            document.getElementById('listView').classList.toggle('d-none-v',  isGrid);
        });
    });

    // ── AJAX status toggle
    document.querySelectorAll('.shop-status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            var url  = this.dataset.url;
            var id   = this.dataset.id;
            var self = this;

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                document.querySelectorAll('.shop-status-toggle[data-id="' + id + '"]').forEach(function (t) {
                    t.checked = data.status;
                });
            })
            .catch(function () {
                self.checked = !self.checked;
            });
        });
    });

});
</script>

@endsection
