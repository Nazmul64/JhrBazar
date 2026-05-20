
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
  const currentTheme = html.getAttribute('data-theme') || 'light';
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  
  html.setAttribute('data-theme', newTheme);
  
  // Update Icon
  const icons = document.querySelectorAll('#darkToggle i, #darkToggleBtn i');
  icons.forEach(icon => {
    if (newTheme === 'dark') {
      icon.classList.remove('bi-moon-stars-fill');
      icon.classList.add('bi-sun-fill');
    } else {
      icon.classList.remove('bi-sun-fill');
      icon.classList.add('bi-moon-stars-fill');
    }
  });
  
  // Save to database
  fetch('{{ route("admin.generalsettings.update-theme") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ theme: newTheme })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) {
      console.error('Failed to save theme preference');
    }
  })
  .catch(err => console.error('Error saving theme:', err));
}
  /* Mobile Sidebar Toggle Visibility removed */

  // Explicitly attach dark mode listeners
  document.addEventListener('DOMContentLoaded', () => {
    const dt1 = document.getElementById('darkToggle');
    const dt2 = document.getElementById('darkToggleBtn');
    if (dt1) dt1.addEventListener('click', toggleDark);
    if (dt2) dt2.addEventListener('click', toggleDark);
  });

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

    {{-- ── Customer Detector AJAX Poller ── --}}
    @if(auth()->check() && auth()->user()->role === 'admin')
    <style>
        #cd-toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 380px;
            width: calc(100% - 40px);
        }
        .cd-toast-alert {
            background: #0b1329 !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            padding: 16px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.35) !important;
            border: 1px solid #1e293b !important;
            font-family: 'Outfit', sans-serif !important;
            position: relative;
            cursor: pointer;
            animation: cdSlideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.3s ease;
        }
        .cd-toast-alert:hover {
            transform: translateY(-2px) scale(1.02);
            border-color: #38bdf8 !important;
            box-shadow: 0 12px 30px rgba(56, 189, 248, 0.15) !important;
        }
        .cd-toast-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }
        .cd-toast-close:hover {
            color: #ffffff;
        }
        @keyframes cdSlideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    <script>
        function playNotificationSound() {
            try {
                let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                let osc = audioCtx.createOscillator();
                let gainNode = audioCtx.createGain();
                
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.08); // A5
                
                gainNode.gain.setValueAtTime(0.12, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
                
                osc.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.35);
            } catch (e) {
                console.log("Audio play failed: ", e);
            }
        }

        $(document).ready(function() {
            // Container setup
            if (!$('#cd-toast-container').length) {
                $('body').append('<div id="cd-toast-container"></div>');
            }

            function pollCustomerDetector() {
                $.ajax({
                    url: "{{ route('admin.customer-detector.poll') }}",
                    method: "GET",
                    dataType: "json",
                    success: function(visits) {
                        if (visits && visits.length > 0) {
                            visits.forEach(function(visit) {
                                playNotificationSound();
                                
                                let timeStr = new Date(visit.visited_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                                let toastId = 'cd-toast-' + visit.id;
                                
                                // Map URL path to beautiful names if possible
                                let pageName = visit.page_visited;
                                if (pageName.includes('checkout') || pageName.toLowerCase() === 'checkout') {
                                    pageName = 'চেকআউট পেজ';
                                } else if (pageName === '/' || pageName.toLowerCase() === 'home' || pageName.toLowerCase() === 'index') {
                                    pageName = 'হোম পেজ';
                                } else if (pageName.toLowerCase() === 'cart') {
                                    pageName = 'কার্ট পেজ';
                                }

                                let toastHtml = `
                                    <div id="${toastId}" class="cd-toast-alert">
                                        <button type="button" class="cd-toast-close">&times;</button>
                                        <div style="font-weight: 700; color: #38bdf8; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <span>🔔</span> Returning Customer Visit Alert!
                                        </div>
                                        <div style="font-size: 13px; color: #e2e8f0; line-height: 1.4; margin-bottom: 8px; font-weight: 500;">
                                            Customer <strong>${visit.customer_name}</strong> (<span style="color: #38bdf8;">${visit.phone_number}</span>) just visited <strong style="color: #f59e0b;">🛒 ${pageName}</strong>!
                                        </div>
                                        <div style="font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 4px; font-weight: 600;">
                                            <span>🕒</span> ${timeStr} (just now)
                                        </div>
                                    </div>
                                `;

                                $('#cd-toast-container').append(toastHtml);

                                // Setup card click navigation
                                $(`#${toastId}`).on('click', function(e) {
                                    if ($(e.target).hasClass('cd-toast-close')) {
                                        e.stopPropagation();
                                        $(`#${toastId}`).remove();
                                        return;
                                    }
                                    window.location.href = "{{ route('admin.customer-detector.index') }}?search=" + encodeURIComponent(visit.phone_number);
                                });

                                // Auto remove toast after 10 seconds
                                setTimeout(function() {
                                    let el = document.getElementById(toastId);
                                    if (el) {
                                        el.style.opacity = '0';
                                        el.style.transform = 'translateX(100px)';
                                        setTimeout(() => el.remove(), 300);
                                    }
                                }, 10000);
                            });
                        }
                    }
                });
            }

            // Poll every 8 seconds
            setInterval(pollCustomerDetector, 8000);
            // Run immediately on page load
            pollCustomerDetector();
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>

