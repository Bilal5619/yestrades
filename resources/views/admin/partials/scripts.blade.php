<script>
  'use strict';

  const baseUrl = "{{ asset('/') }}";
</script>

{{-- core js files --}}
<script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

{{-- jQuery ui --}}
<script type="text/javascript" src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.ui.touch-punch.min.js') }}"></script>

{{-- jQuery time-picker --}}
<script type="text/javascript" src="{{ asset('assets/js/jquery.timepicker.min.js') }}"></script>

{{-- jQuery scrollbar --}}
<script type="text/javascript" src="{{ asset('assets/js/jquery.scrollbar.min.js') }}"></script>

{{-- bootstrap notify --}}
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-notify.min.js') }}"></script>

{{-- sweet alert --}}
<script type="text/javascript" src="{{ asset('assets/js/sweet-alert.min.js') }}"></script>

{{-- bootstrap tags input --}}
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-tagsinput.min.js') }}"></script>

{{-- bootstrap date-picker --}}
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>

{{-- tinymce editor --}}
<script src="{{ asset('assets/js/tinymce/js/tinymce/tinymce.min.js') }}"></script>

{{-- js color --}}
<script type="text/javascript" src="{{ asset('assets/js/jscolor.min.js') }}"></script>

{{-- fontawesome icon picker js --}}
<script type="text/javascript" src="{{ asset('assets/js/fontawesome-iconpicker.min.js') }}"></script>

{{-- datatables js --}}
<script type="text/javascript" src="{{ asset('assets/js/datatables-1.10.23.min.js') }}"></script>

{{-- datatables bootstrap js --}}
<script type="text/javascript" src="{{ asset('assets/js/datatables.bootstrap4.min.js') }}"></script>

{{-- dropzone js --}}
<script type="text/javascript" src="{{ asset('assets/js/dropzone.min.js') }}"></script>

{{-- atlantis js --}}
<script type="text/javascript" src="{{ asset('assets/js/atlantis.js') }}"></script>

{{-- fonts and icons script --}}
<script type="text/javascript" src="{{ asset('assets/js/webfont.min.js') }}"></script>

@if (session()->has('success'))
  <script>
    'use strict';
    var content = {};

    content.message = '{{ session('success') }}';
    content.title = 'Success';
    content.icon = 'fa fa-bell';

    $.notify(content, {
      type: 'success',
      placement: {
        from: 'top',
        align: 'right'
      },
      showProgressbar: true,
      time: 1000,
      delay: 4000
    });
  </script>
@endif

@if (session()->has('warning'))
  <script>
    'use strict';
    var content = {};

    content.message = '{{ session('warning') }}';
    content.title = 'Warning!';
    content.icon = 'fa fa-bell';

    $.notify(content, {
      type: 'warning',
      placement: {
        from: 'top',
        align: 'right'
      },
      showProgressbar: true,
      time: 1000,
      delay: 4000
    });
  </script>
@endif

<script>
  'use strict';
  const account_status = 1;
  const secret_login = 1;
  let time_format = "{{ $settings->time_format }}";
</script>

{{-- select2 js --}}
<script type="text/javascript" src="{{ asset('assets/js/select2.min.js') }}"></script>

{{-- admin-main js --}}
<script type="text/javascript" src="{{ asset('assets/js/admin-main.js') }}"></script>

<script>
  (function () {
    'use strict';

    const config = window.adminAutoRefreshConfig || {};

    if (!config.enabled) {
      return;
    }

    const refreshInterval = config.interval || 30000;
    const targetSelector = config.targetSelector || '[data-admin-refresh-target]';
    const indicator = document.querySelector(config.indicatorSelector || '[data-admin-auto-refresh-indicator]');
    const indicatorText = indicator ? indicator.querySelector('[data-admin-auto-refresh-text]') : null;
    const indicatorTime = indicator ? indicator.querySelector('[data-admin-auto-refresh-time]') : null;
    let isRefreshing = false;
    let formSnapshots = new WeakMap();

    function formatTime(date) {
      return date.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    }

    function setIndicator(state, text) {
      if (!indicator) {
        return;
      }

      indicator.classList.remove('is-loading', 'is-paused', 'is-error');

      if (state) {
        indicator.classList.add('is-' + state);
      }

      if (indicatorText) {
        indicatorText.textContent = text;
      }
    }

    function markUpdated(date) {
      setIndicator('', 'Auto-refresh on');

      if (indicatorTime) {
        indicatorTime.textContent = 'Last updated: ' + formatTime(date);
      }
    }

    function formSnapshot(form) {
      try {
        const data = new FormData(form);
        data.delete('_token');
        return new URLSearchParams(data).toString();
      } catch (error) {
        return '';
      }
    }

    function rememberFormSnapshots() {
      formSnapshots = new WeakMap();
      document.querySelectorAll(targetSelector + ' form').forEach(function (form) {
        formSnapshots.set(form, formSnapshot(form));
      });
    }

    function hasDirtyForm() {
      return Array.from(document.querySelectorAll(targetSelector + ' form')).some(function (form) {
        return formSnapshots.get(form) !== formSnapshot(form);
      });
    }

    function isUserInteracting() {
      const active = document.activeElement;
      const activeTag = active ? active.tagName : '';
      const inputFocused = active && active !== document.body && (
        ['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(activeTag) ||
        active.isContentEditable
      );
      const modalOpen = document.querySelector('.modal.show, .modal.in');

      return Boolean(inputFocused || modalOpen || hasDirtyForm());
    }

    function replacePageContent(html) {
      const parser = new DOMParser();
      const nextDocument = parser.parseFromString(html, 'text/html');
      const currentTarget = document.querySelector(targetSelector);
      const nextTarget = nextDocument.querySelector(targetSelector);

      if (!currentTarget || !nextTarget) {
        throw new Error('Refresh target not found.');
      }

      const currentPanels = currentTarget.querySelectorAll('[data-admin-refresh-panel]');
      const nextPanels = nextTarget.querySelectorAll('[data-admin-refresh-panel]');

      if (currentPanels.length && nextPanels.length) {
        currentPanels.forEach(function (panel) {
          const panelName = panel.getAttribute('data-admin-refresh-panel');
          const nextPanel = nextTarget.querySelector('[data-admin-refresh-panel="' + panelName + '"]');

          if (nextPanel) {
            panel.innerHTML = nextPanel.innerHTML;
          }
        });
      } else {
        currentTarget.innerHTML = nextTarget.innerHTML;
      }

      document.title = nextDocument.title || document.title;
      rememberFormSnapshots();

      if (window.jQuery) {
        window.jQuery('[data-toggle="tooltip"]').tooltip();
        window.jQuery('[data-toggle="popover"]').popover();

        if (window.jQuery.fn.select2) {
          window.jQuery(targetSelector + ' .select2').select2();
        }

        if (window.jQuery.fn.DataTable && window.jQuery(targetSelector + ' #basic-datatables').length) {
          window.jQuery(targetSelector + ' #basic-datatables').DataTable({
            ordering: false,
            responsive: true
          });
        }
      }
    }

    function refreshAdminContent() {
      if (isRefreshing || document.hidden) {
        return;
      }

      if (isUserInteracting()) {
        setIndicator('paused', 'Auto-refresh paused');
        return;
      }

      isRefreshing = true;
      setIndicator('loading', 'Refreshing...');

      fetch(window.location.href, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        },
        cache: 'no-store'
      })
        .then(function (response) {
          if (response.status === 401 || response.status === 419) {
            window.location.reload();
            return null;
          }

          if (!response.ok) {
            throw new Error('Refresh failed.');
          }

          return response.text();
        })
        .then(function (html) {
          if (!html) {
            return;
          }

          replacePageContent(html);
          markUpdated(new Date());
        })
        .catch(function () {
          setIndicator('error', 'Refresh failed');
        })
        .finally(function () {
          isRefreshing = false;
        });
    }

    rememberFormSnapshots();
    markUpdated(new Date());
    window.setInterval(refreshAdminContent, refreshInterval);
  })();
</script>
