
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
  sidebar.classList.add('show');
  overlay.classList.add('show');
}
function closeSidebar() {
  sidebar.classList.remove('show');
  overlay.classList.remove('show');
}
toggleBtn.addEventListener('click', () => {
  if (window.innerWidth >= 992) {
    if (sidebar.style.transform === 'translateX(-100%)' || getComputedStyle(sidebar).transform === 'matrix(1, 0, 0, 1, 0, 0)') {
      // desktop collapse
      sidebar.style.width = '0';
      header.style.left = '0';
      main.style.marginLeft = '0';
    }
    // simpler: just toggle a collapsed class
    sidebar.classList.toggle('collapsed');
    if (sidebar.classList.contains('collapsed')) {
      sidebar.style.transform = 'translateX(-100%)';
      header.style.left = '0';
      main.style.marginLeft = '0';
    } else {
      sidebar.style.transform = '';
      header.style.left = 'var(--sidebar-w)';
      main.style.marginLeft = 'var(--sidebar-w)';
    }
  } else {
    openSidebar();
  }
});
overlay.addEventListener('click', closeSidebar);

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
document.getElementById('darkToggle').addEventListener('click', toggleDark);
document.getElementById('darkToggleBtn').addEventListener('click', toggleDark);

/* ── CHART TABS ── */
document.querySelectorAll('.chart-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.chart-tab-group').querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});

/* ── ORDERS CHART ── */
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
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

/* ── DONUT CHART ── */
const donutCtx = document.getElementById('donutChart').getContext('2d');
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
</script>
</body>
</html>
