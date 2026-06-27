@extends('admin.layout')
@includeIf('admin.partials.rtl-style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Slides (Advertisements)') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Home Page') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Slides (Advertisements)') }}</a></li>
    </ul>
  </div>

  @php
    $limit    = (int) ($settings->ad_slider_max_images ?? 10);
    $existing = $images->count();
    $allowed  = max(0, $limit - $existing);
  @endphp

  {{-- ── Alerts ─────────────────────────────────────────── --}}
  @if (session('limit_success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('limit_success') }}
    </div>
  @endif
  @if (session('limit_error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('limit_error') }}
    </div>
  @endif
  @if (session('upload_success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('upload_success') }}
    </div>
  @endif
  @if (session('upload_error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('upload_error') }}
    </div>
  @endif
  @if (session('delete_success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('delete_success') }}
    </div>
  @endif
  @if (session('delete_info'))
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {{ session('delete_info') }}
    </div>
  @endif

  <div class="row">

    {{-- ── Max limit setting ──────────────────────────────── --}}
    <div class="col-md-12 mb-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Image Limit Setting') }}</div>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.home_page.ad_slider.update_limit') }}" method="POST">
            @csrf
            <div class="row align-items-end">
              <div class="col-md-4">
                <div class="form-group mb-0">
                  <label>{{ __('Maximum number of slide images') }}</label>
                  <input type="number" name="ad_slider_max_images" class="form-control"
                    value="{{ $limit }}" min="1" max="50">
                  @error('ad_slider_max_images')
                    <p class="text-danger mt-1 mb-0">{{ $message }}</p>
                  @enderror
                  <small class="text-muted">
                    Currently {{ $existing }} image(s) uploaded. To reduce the limit below {{ $existing }}, delete some images first.
                  </small>
                </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fa fa-save"></i> {{ __('Save Limit') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- ── Existing images (delete by marking) ───────────── --}}
    <div class="col-md-12 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title">
            {{ __('Current Slides') }}
            <span class="badge badge-secondary ml-2">{{ $existing }} / {{ $limit }}</span>
          </div>
          @if ($existing > 0)
            <button type="button" id="saveDeleteBtn" class="btn btn-danger btn-sm" style="display:none;"
              onclick="document.getElementById('bulkDeleteForm').submit();">
              <i class="fa fa-trash"></i>
              <span id="saveDeleteLabel">{{ __('Delete Selected') }}</span>
            </button>
          @endif
        </div>
        <div class="card-body">
          @if ($existing > 0)
            <p class="text-muted mb-3">
              Click the <strong>&times;</strong> on any image to mark it for deletion, then click <strong>Delete Selected</strong>.
            </p>
            <form id="bulkDeleteForm" action="{{ route('admin.home_page.ad_slider.bulk_delete') }}" method="POST">
              @csrf
              <div class="row" id="existingImagesGrid">
                @foreach ($images as $img)
                  <div class="col-md-2 col-sm-3 col-4 mb-3 slide-item" data-id="{{ $img->id }}">
                    <div class="slide-card" style="position:relative;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .2s;">
                      <img src="{{ asset('assets/img/hero/' . $img->image) }}" alt="slide"
                        style="width:100%;height:110px;object-fit:cover;display:block;">
                      {{-- X button --}}
                      <button type="button" class="slide-delete-toggle"
                        style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,.55);color:#fff;border:none;border-radius:50%;width:24px;height:24px;font-size:14px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        &times;
                      </button>
                      {{-- Red overlay when marked --}}
                      <div class="slide-marked-overlay"
                        style="display:none;position:absolute;inset:0;background:rgba(192,57,43,.5);display:none;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-size:28px;font-weight:700;">&times;</span>
                      </div>
                      {{-- Hidden checkbox --}}
                      <input type="checkbox" name="delete_ids[]" value="{{ $img->id }}"
                        class="slide-checkbox" style="display:none;">
                    </div>
                  </div>
                @endforeach
              </div>
            </form>
          @else
            <p class="text-muted">{{ __('No advertisement slide images uploaded yet.') }}</p>
          @endif
        </div>
      </div>
    </div>

    {{-- ── Upload new images ──────────────────────────────── --}}
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Upload New Slides') }}</div>
        </div>
        <div class="card-body">
          @if ($allowed > 0)
            <form id="adSliderUploadForm" action="{{ route('admin.home_page.ad_slider.store') }}"
              method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" id="adFileInput" name="images[]" multiple
                accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">

              <button type="button" id="adPickBtn" class="btn btn-outline-primary btn-sm mb-3">
                <i class="fa fa-images"></i> {{ __('Select Images') }}
                <small class="text-muted ml-1">(up to {{ $allowed }} more)</small>
              </button>

              <div id="adUploadError" class="alert alert-danger mb-2" style="display:none;"></div>

              <div id="adPreviewGrid" class="row mb-3" style="display:none;"></div>

              <div id="adUploadActions" style="display:none;">
                <button type="submit" id="adUploadBtn" class="btn btn-success btn-sm">
                  <i class="fa fa-upload"></i> {{ __('Upload Selected') }}
                  <span id="adUploadCount"></span>
                </button>
                <button type="button" id="adClearBtn" class="btn btn-secondary btn-sm ml-2">
                  {{ __('Clear') }}
                </button>
              </div>
            </form>
          @else
            <div class="alert alert-warning mb-0">
              {{ __('Maximum of') }} {{ $limit }} {{ __('images reached. Delete some images or increase the limit to upload more.') }}
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
@endsection

@push('scripts')
<script>
(function () {
  // ── Delete-marking logic ─────────────────────────────
  var markedCount = 0;
  var saveBtn     = document.getElementById('saveDeleteBtn');

  document.querySelectorAll('.slide-delete-toggle').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var card     = btn.closest('.slide-card');
      var overlay  = card.querySelector('.slide-marked-overlay');
      var checkbox = card.querySelector('.slide-checkbox');

      if (checkbox.checked) {
        // Unmark
        checkbox.checked        = false;
        overlay.style.display   = 'none';
        card.style.borderColor  = 'transparent';
        markedCount--;
      } else {
        // Mark
        checkbox.checked        = true;
        overlay.style.display   = 'flex';
        card.style.borderColor  = '#c0392b';
        markedCount++;
      }

      if (saveBtn) {
        saveBtn.style.display = markedCount > 0 ? '' : 'none';
        document.getElementById('saveDeleteLabel').textContent =
          'Delete Selected (' + markedCount + ')';
      }
    });
  });

  // ── Upload preview logic ────────────────────────────
  var ALLOWED       = {{ $allowed }};
  var LIMIT         = {{ $limit }};
  var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
  var MAX_SIZE      = 4 * 1024 * 1024;

  var fileInput  = document.getElementById('adFileInput');
  var pickBtn    = document.getElementById('adPickBtn');
  var grid       = document.getElementById('adPreviewGrid');
  var actions    = document.getElementById('adUploadActions');
  var errBox     = document.getElementById('adUploadError');
  var countLabel = document.getElementById('adUploadCount');
  var clearBtn   = document.getElementById('adClearBtn');
  var form       = document.getElementById('adSliderUploadForm');

  if (!fileInput) return; // at-limit: upload form not rendered

  pickBtn.addEventListener('click', function () { fileInput.click(); });

  fileInput.addEventListener('change', function () {
    var files = Array.from(fileInput.files);
    errBox.style.display = 'none';
    errBox.innerHTML     = '';
    grid.innerHTML       = '';

    if (!files.length) { reset(); return; }

    var errors = [];
    if (files.length > ALLOWED) {
      errors.push('You can only add ' + ALLOWED + ' more image(s) (limit is ' + LIMIT + ').');
    }
    files.forEach(function (f) {
      if (ALLOWED_TYPES.indexOf(f.type) === -1) {
        errors.push('"' + f.name + '" is not an allowed image type (JPG, PNG, WEBP, GIF).');
      }
      if (f.size > MAX_SIZE) {
        errors.push('"' + f.name + '" exceeds the 4 MB size limit.');
      }
    });

    if (errors.length) {
      errBox.innerHTML     = errors.map(function (e) { return '<div>' + e + '</div>'; }).join('');
      errBox.style.display = '';
      reset();
      return;
    }

    files.forEach(function (f) {
      var reader = new FileReader();
      reader.onload = function (ev) {
        var col = document.createElement('div');
        col.className = 'col-md-2 col-sm-3 col-4 mb-3';
        col.innerHTML =
          '<div style="border-radius:6px;overflow:hidden;border:1px solid #ddd;">' +
            '<img src="' + ev.target.result + '" style="width:100%;height:110px;object-fit:cover;display:block;" alt="preview">' +
            '<div style="padding:4px 6px;font-size:11px;word-break:break-all;color:#666;">' + f.name + '</div>' +
          '</div>';
        grid.appendChild(col);
      };
      reader.readAsDataURL(f);
    });

    grid.style.display    = '';
    actions.style.display = '';
    countLabel.textContent = '(' + files.length + ')';
  });

  clearBtn.addEventListener('click', function () {
    fileInput.value = '';
    reset();
  });

  form.addEventListener('submit', function () {
    document.getElementById('adUploadBtn').disabled    = true;
    document.getElementById('adUploadBtn').innerHTML   = '<i class="fa fa-spinner fa-spin"></i> Uploading…';
  });

  function reset() {
    grid.style.display    = 'none';
    actions.style.display = 'none';
    grid.innerHTML        = '';
    countLabel.textContent = '';
  }
})();
</script>
@endpush
