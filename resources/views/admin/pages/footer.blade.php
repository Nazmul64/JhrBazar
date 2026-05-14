
<!-- Bootstrap JS -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ── SIDEBAR TOGGLE ── */
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const toggleBtn = document.getElementById('sidebarToggle');
const header = document.getElementById('header');
const main = document.getElementById('main');

function openSidebar() {
  if (sidebar && overlay) {
    sidebar.classList.add('show');
    overlay.classList.add('show');
  }
}
function closeSidebar() {
  if (sidebar && overlay) {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
  }
}

if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      if (!sidebar) return;
      if (window.innerWidth >= 992) {
        if (sidebar.style.transform === 'translateX(-100%)' || getComputedStyle(sidebar).transform === 'matrix(1, 0, 0, 1, 0, 0)') {
          // desktop collapse
          if (sidebar) sidebar.style.width = '0';
          if (header) header.style.left = '0';
          if (main) main.style.marginLeft = '0';
        }
        // simpler: just toggle a collapsed class
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
          sidebar.style.transform = 'translateX(-100%)';
          if (header) header.style.left = '0';
          if (main) main.style.marginLeft = '0';
        } else {
          sidebar.style.transform = '';
          if (header) header.style.left = 'var(--sidebar-w)';
          if (main) main.style.marginLeft = 'var(--sidebar-w)';
        }
      } else {
        openSidebar();
      }
    });
}

if (overlay) overlay.addEventListener('click', closeSidebar);

/* ── SUBMENU TOGGLE ── */
document.querySelectorAll('.has-sub').forEach(item => {
  item.addEventListener('click', () => {
    const key = item.dataset.sub;
    const sub = document.getElementById('sub-' + key);
    if (!sub) return;
    item.classList.toggle('open');
    sub.classList.toggle('show');
  });
});

/* ── NAV ACTIVE ── */
document.querySelectorAll('.nav-item-custom[data-page]').forEach(el => {
  el.addEventListener('click', e => {
    e.preventDefault();
    document.querySelectorAll('.nav-item-custom').forEach(x => x.classList.remove('active'));
    el.classList.add('active');
    if (window.innerWidth < 992) closeSidebar();
  });
});

/* ── DARK MODE ── */
function toggleDark() {
  const html = document.documentElement;
  if (html.getAttribute('data-bs-theme') === 'dark') {
    html.removeAttribute('data-bs-theme');
  } else {
    html.setAttribute('data-bs-theme', 'dark');
  }
}
const darkToggle = document.getElementById('darkToggle');
const darkToggleBtn = document.getElementById('darkToggleBtn');
if (darkToggle) darkToggle.addEventListener('click', toggleDark);
if (darkToggleBtn) darkToggleBtn.addEventListener('click', toggleDark);

/* ── CHART TABS ── */
document.querySelectorAll('.chart-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.chart-tab-group').querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});

/* ── ORDERS CHART ── */
const ordersChartEl = document.getElementById('ordersChart');
if (ordersChartEl) {
    const ordersCtx = ordersChartEl.getContext('2d');
    const years = ['2020','2021','2022','2023','2024','2025','2026'];
    const ordersData = [0, 0, 0, 2, 4, 100, 3];
    
    const gradient = ordersCtx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(244, 63, 127, 0.35)');
    gradient.addColorStop(1, 'rgba(244, 63, 127, 0.0)');
    
    new Chart(ordersCtx, {
      type: 'bar',
      data: {
        labels: years.slice().reverse(),
        datasets: [
          {
            type: 'line',
            label: 'Orders',
            data: ordersData.slice().reverse(),
            borderColor: '#f43f7f',
            borderWidth: 2.5,
            pointBackgroundColor: '#f43f7f',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            backgroundColor: gradient,
            tension: 0.4,
            order: 1,
          },
          {
            type: 'bar',
            label: 'Orders',
            data: ordersData.slice().reverse(),
            backgroundColor: 'rgba(244, 63, 127, 0.18)',
            borderRadius: 6,
            borderSkipped: false,
            order: 2,
          }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false }, tooltip: { callbacks: {
          label: ctx => ` ${ctx.parsed.y} Orders`
        }}},
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11 } } },
          y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 11 }, stepSize: 10 }, beginAtZero: true }
        }
      }
    });
}

/* ── DONUT CHART ── */
const donutChartEl = document.getElementById('donutChart');
if (donutChartEl) {
    const donutCtx = donutChartEl.getContext('2d');
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['Customer', 'Shop', 'Rider'],
        datasets: [{
          data: [26, 10, 3],
          backgroundColor: ['#f43f7f', '#3b82f6', '#ef4444'],
          borderWidth: 3,
          borderColor: 'var(--card)',
          hoverOffset: 8,
        }]
      },
      options: {
        responsive: true,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` }}
        }
      }
    });
}

</script>
 {{-- ══════════════════════════════════════════
         SCRIPTS
    ══════════════════════════════════════════ --}}

    {{-- ── jQuery 3.7.1 ── --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    {{-- ── Bootstrap 5.3.3 Bundle (Popper included) ── --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ── Chart.js 4.4.3 ── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- ── SweetAlert2 ── --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    {{-- ── Select2 ── --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- ── Summernote ── --}}
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

    {{-- ── DataTables ── --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    {{-- ── Toastr / SweetAlert Handling ── --}}
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(Session::has('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ Session::get('success') }}"
            });
        @endif

        @if(Session::has('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ Session::get('error') }}"
            });
        @endif

        @if(Session::has('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ Session::get('warning') }}"
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>

