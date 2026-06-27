(function (window, document) {
  'use strict';

  const config = {
    success: {
      title: 'Success',
      icon: 'check-circle'
    },
    error: {
      title: 'Something went wrong',
      icon: 'times-circle'
    },
    warning: {
      title: 'Please check',
      icon: 'exclamation-triangle'
    },
    info: {
      title: 'Notice',
      icon: 'info-circle'
    }
  };

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function getToastStack() {
    let stack = document.querySelector('[data-site-toast-stack]');

    if (!stack) {
      stack = document.createElement('div');
      stack.className = 'site-toast-stack';
      stack.setAttribute('data-site-toast-stack', '');
      document.body.appendChild(stack);
    }

    return stack;
  }

  function removeToast(toast) {
    if (!toast || toast.classList.contains('is-leaving')) {
      return;
    }

    toast.classList.add('is-leaving');
    window.setTimeout(function () {
      toast.remove();
    }, 220);
  }

  function notify(type, message, options) {
    const toastType = config[type] ? type : 'info';
    const settings = Object.assign({}, config[toastType], options || {});
    const stack = getToastStack();
    const toast = document.createElement('div');
    const title = settings.title === false ? '' : escapeHtml(settings.title || config[toastType].title);
    const icon = escapeHtml(settings.icon || config[toastType].icon);
    const timeout = Number(settings.timeout || settings.timeOut || 5000);

    toast.className = 'site-toast site-toast-' + toastType;
    toast.setAttribute('role', toastType === 'error' || toastType === 'warning' ? 'alert' : 'status');
    toast.setAttribute('aria-live', toastType === 'error' || toastType === 'warning' ? 'assertive' : 'polite');
    toast.innerHTML =
      '<span class="site-toast-icon"><i class="fal fa-' + icon + '"></i></span>' +
      '<div>' +
        (title ? '<h3 class="site-toast-title">' + title + '</h3>' : '') +
        '<p class="site-toast-message">' + escapeHtml(message) + '</p>' +
      '</div>' +
      '<button type="button" class="site-toast-close" aria-label="Close notification">&times;</button>';

    stack.prepend(toast);

    const closeButton = toast.querySelector('.site-toast-close');
    closeButton.addEventListener('click', function () {
      removeToast(toast);
    });

    window.requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });

    if (timeout > 0) {
      window.setTimeout(function () {
        removeToast(toast);
      }, timeout);
    }

    return toast;
  }

  function confirmDialog(options) {
    const settings = Object.assign({
      title: 'Are you sure?',
      message: 'Please confirm this action.',
      confirmText: 'Continue',
      cancelText: 'Cancel',
      icon: 'trash-alt'
    }, options || {});

    return new Promise(function (resolve) {
      const overlay = document.createElement('div');
      let resolved = false;

      function close(result) {
        if (resolved) {
          return;
        }

        resolved = true;
        overlay.classList.remove('is-visible');
        window.setTimeout(function () {
          overlay.remove();
          resolve(result);
        }, 180);
      }

      overlay.className = 'site-confirm-overlay';
      overlay.setAttribute('role', 'presentation');
      overlay.innerHTML =
        '<div class="site-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="site-confirm-title" aria-describedby="site-confirm-message">' +
          '<div class="site-confirm-head">' +
            '<span class="site-confirm-icon"><i class="fal fa-' + escapeHtml(settings.icon) + '"></i></span>' +
            '<div>' +
              '<h2 class="site-confirm-title" id="site-confirm-title">' + escapeHtml(settings.title) + '</h2>' +
              '<p class="site-confirm-message" id="site-confirm-message">' + escapeHtml(settings.message) + '</p>' +
            '</div>' +
          '</div>' +
          '<div class="site-confirm-actions">' +
            '<button type="button" class="site-confirm-btn" data-site-confirm-cancel>' + escapeHtml(settings.cancelText) + '</button>' +
            '<button type="button" class="site-confirm-btn site-confirm-btn-primary" data-site-confirm-ok>' + escapeHtml(settings.confirmText) + '</button>' +
          '</div>' +
        '</div>';

      document.body.appendChild(overlay);

      const cancelButton = overlay.querySelector('[data-site-confirm-cancel]');
      const confirmButton = overlay.querySelector('[data-site-confirm-ok]');

      cancelButton.addEventListener('click', function () {
        close(false);
      });
      confirmButton.addEventListener('click', function () {
        close(true);
      });
      overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
          close(false);
        }
      });
      document.addEventListener('keydown', function onKeydown(event) {
        if (!overlay.isConnected) {
          document.removeEventListener('keydown', onKeydown);
          return;
        }

        if (event.key === 'Escape') {
          close(false);
        }
      });

      window.requestAnimationFrame(function () {
        overlay.classList.add('is-visible');
        confirmButton.focus();
      });
    });
  }

  function bindConfirmForms() {
    document.addEventListener('submit', function (event) {
      const form = event.target;

      if (!form || !form.matches('[data-custom-confirm]') || form.dataset.customConfirmAccepted === '1') {
        return;
      }

      event.preventDefault();

      confirmDialog({
        title: form.dataset.confirmTitle || 'Confirm action',
        message: form.dataset.confirmMessage || form.dataset.customConfirm,
        confirmText: form.dataset.confirmButton || 'Continue',
        cancelText: form.dataset.cancelButton || 'Cancel',
        icon: form.dataset.confirmIcon || 'trash-alt'
      }).then(function (confirmed) {
        if (confirmed) {
          form.dataset.customConfirmAccepted = '1';
          form.submit();
        }
      });
    });
  }

  const api = {
    notify: notify,
    success: function (message, title, options) {
      return notify('success', message, Object.assign({ title: title || config.success.title }, options || {}));
    },
    error: function (message, title, options) {
      return notify('error', message, Object.assign({ title: title || config.error.title }, options || {}));
    },
    warning: function (message, title, options) {
      return notify('warning', message, Object.assign({ title: title || config.warning.title }, options || {}));
    },
    info: function (message, title, options) {
      return notify('info', message, Object.assign({ title: title || config.info.title }, options || {}));
    },
    confirm: confirmDialog
  };

  window.AppNotify = api;
  window.toastr = Object.assign(window.toastr || {}, {
    success: api.success,
    error: api.error,
    warning: api.warning,
    info: api.info,
    clear: function () {
      document.querySelectorAll('.site-toast').forEach(removeToast);
    },
    remove: function () {
      document.querySelectorAll('.site-toast').forEach(function (toast) {
        toast.remove();
      });
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindConfirmForms);
  } else {
    bindConfirmForms();
  }
})(window, document);
