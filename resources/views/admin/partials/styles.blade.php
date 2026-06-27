{{-- fontawesome css --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

{{-- fontawesome icon picker css --}}
<link rel="stylesheet" href="{{ asset('assets/css/fontawesome-iconpicker.min.css') }}">

{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

{{-- bootstrap tags-input css --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}">

{{-- jQuery-ui css --}}
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">

{{-- jQuery-timepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">

{{-- bootstrap-datepicker css --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}">

{{-- dropzone css --}}
<link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}">

{{-- atlantis css --}}
<link rel="stylesheet" href="{{ asset('assets/css/atlantis.css') }}">

{{-- select2 css --}}
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

{{-- admin-main css --}}
<link rel="stylesheet" href="{{ asset('assets/css/admin-main.css') }}">

<style>
  .logo-header .logo .admin-header-logo {
    width: auto;
    max-width: 130px;
    max-height: 44px;
    object-fit: contain;
  }

  .main-header .avatar-sm,
  .sidebar .avatar-sm {
    overflow: hidden;
  }

  .main-header .avatar-img,
  .sidebar .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .admin-auto-refresh-indicator {
    position: sticky;
    top: 8px;
    z-index: 20;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    float: right;
    margin: 10px 24px -4px 0;
    padding: 7px 11px;
    color: #4f5665;
    background: #ffffff;
    border: 1px solid #e3e7ee;
    border-radius: 999px;
    box-shadow: 0 8px 22px rgba(15, 18, 28, 0.08);
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
  }

  .admin-auto-refresh-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
  }

  .admin-auto-refresh-indicator.is-loading .admin-auto-refresh-dot {
    background: #1572e8;
    animation: adminRefreshPulse 0.9s ease-in-out infinite;
  }

  .admin-auto-refresh-indicator.is-paused .admin-auto-refresh-dot {
    background: #f0ad4e;
  }

  .admin-auto-refresh-indicator.is-error .admin-auto-refresh-dot {
    background: #dc3545;
  }

  .admin-auto-refresh-time {
    color: #7a8190;
    font-weight: 600;
  }

  @keyframes adminRefreshPulse {
    0%, 100% {
      transform: scale(0.8);
      opacity: 0.55;
    }
    50% {
      transform: scale(1.25);
      opacity: 1;
    }
  }
</style>
