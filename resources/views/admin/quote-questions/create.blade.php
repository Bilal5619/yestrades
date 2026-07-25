@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Add Quote Question</h4>
  </div>

  <div class="card">

    <div class="card-body">

      <form action="{{ route('admin.quote_questions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
          <label>Service</label>
          <select name="service_id" class="form-control" required>
            <option value="">-- Select a Quote Service --</option>
            @foreach($serviceNames as $svc)
              <option value="{{ $svc->service_id }}" {{ old('service_id') == $svc->service_id ? 'selected' : '' }}>{{ $svc->name }}</option>
            @endforeach
          </select>
          <small class="text-muted">Only services set to "Get a Quote" appear here. Change a service's type from Service Managment &gt; Edit Service.</small>
        </div>

        <div class="form-group">
          <label>Question Title</label>
          <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
          <label>Summary Label <small class="text-muted">(optional &mdash; shown in admin summary instead of question title)</small></label>
          <input type="text" name="summary_label" class="form-control" value="{{ old('summary_label') }}" placeholder="e.g. Property type">
        </div>

        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_required">Required <small class="text-muted">(customer must answer before submitting)</small></label>
          </div>
        </div>

        <div class="form-group">
          <label>Question Image <small class="text-muted">(optional)</small></label>
          <input type="file" name="image" class="form-control-file" accept="image/jpeg,image/png,image/webp">
          @error('image')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
          <label>Field Type</label>
          <select name="type" class="form-control" id="question_type">
            <option value="short_text" {{ old('type', 'short_text') == 'short_text' ? 'selected' : '' }}>Short Text</option>
            <option value="long_text" {{ old('type') == 'long_text' ? 'selected' : '' }}>Long Description</option>
            <option value="single_select" {{ old('type') == 'single_select' ? 'selected' : '' }}>Single Selection</option>
            <option value="multi_select" {{ old('type') == 'multi_select' ? 'selected' : '' }}>Multiple Selection</option>
            <option value="yes_no" {{ old('type') == 'yes_no' ? 'selected' : '' }}>Yes / No</option>
            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
            <option value="quantity" {{ old('type') == 'quantity' ? 'selected' : '' }}>Quantity</option>
            <option value="dropdown" {{ old('type') == 'dropdown' ? 'selected' : '' }}>Dropdown</option>
            <option value="date" {{ old('type') == 'date' ? 'selected' : '' }}>Date</option>
            <option value="time" {{ old('type') == 'time' ? 'selected' : '' }}>Time</option>
            <option value="address" {{ old('type') == 'address' ? 'selected' : '' }}>Address</option>
            <option value="postcode" {{ old('type') == 'postcode' ? 'selected' : '' }}>Postcode</option>
            <option value="photo_upload" {{ old('type') == 'photo_upload' ? 'selected' : '' }}>Photo Upload</option>
            <option value="document_upload" {{ old('type') == 'document_upload' ? 'selected' : '' }}>Document Upload</option>
          </select>
        </div>

        <div class="form-group" id="question_options_group">
          <label>Options</label>

          <div id="optionBox">
            <div class="option-row border rounded p-2 mb-2" data-option-index="0">
              <div class="d-flex align-items-start flex-wrap">
                <input type="hidden" name="option_ids[0]" value="">
                <input type="text" name="options[0]" class="form-control mr-2" placeholder="Option 1" value="{{ old('options.0') }}">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">X</button>
              </div>
              <div class="row mt-2">
                <div class="col-md-6">
                  <label class="mb-1 small text-muted">Option Description (optional)</label>
                  <textarea name="option_descriptions[0]" class="form-control" rows="2" maxlength="2000">{{ old('option_descriptions.0') }}</textarea>
                </div>
                <div class="col-md-6">
                  <label class="mb-1 small text-muted">Option Image (optional)</label>
                  <input type="file" name="option_images[0]" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                </div>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-info btn-sm" onclick="addOption()">+ Add Option</button>
        </div>

        <div class="form-group border rounded p-3" id="quantity_settings_group" style="display: none;">
          <label class="d-block mb-3">Quantity Settings</label>
          <div class="row">
            <div class="col-md-4">
              <label>Minimum Quantity (Optional)</label>
              <input type="number" name="quantity_min" class="form-control" min="0" step="1" value="{{ old('quantity_min') }}">
            </div>
            <div class="col-md-4">
              <label>Maximum Quantity (Optional)</label>
              <input type="number" name="quantity_max" class="form-control" min="0" step="1" value="{{ old('quantity_max') }}">
            </div>
            <div class="col-md-4">
              <label>Default Value (Optional)</label>
              <input type="number" name="quantity_default" class="form-control" min="0" step="1" value="{{ old('quantity_default') }}">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" style="max-width: 150px;">
        </div>

        <button class="btn btn-success">Save Question</button>
        <a href="{{ route('admin.quote_questions.index') }}" class="btn btn-secondary">Back</a>

      </form>

    </div>
  </div>

  <script>
    function syncQuestionTypeUI() {
      const type = document.getElementById('question_type').value;
      const optionsGroup = document.getElementById('question_options_group');
      const quantityGroup = document.getElementById('quantity_settings_group');
      const shouldShowOptions = ['single_select', 'multi_select', 'dropdown'].includes(type);
      const shouldShowQuantity = type === 'quantity';

      optionsGroup.style.display = shouldShowOptions ? 'block' : 'none';
      quantityGroup.style.display = shouldShowQuantity ? 'block' : 'none';
    }

    function addOption() {
      let box = document.getElementById('optionBox');
      let index = box.querySelectorAll('.option-row').length
        ? Math.max(...Array.from(box.querySelectorAll('.option-row')).map(row => parseInt(row.dataset.optionIndex, 10) || 0)) + 1
        : 0;

      let div = document.createElement('div');
      div.className = "option-row border rounded p-2 mb-2";
      div.dataset.optionIndex = index;

      div.innerHTML = `
              <div class="d-flex align-items-start flex-wrap">
                <input type="hidden" name="option_ids[${index}]" value="">
                <input type="text" name="options[${index}]" class="form-control mr-2" placeholder="Option">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">X</button>
              </div>
              <div class="row mt-2">
                <div class="col-md-6">
                  <label class="mb-1 small text-muted">Option Description (optional)</label>
                  <textarea name="option_descriptions[${index}]" class="form-control" rows="2" maxlength="2000"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="mb-1 small text-muted">Option Image (optional)</label>
                  <input type="file" name="option_images[${index}]" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                </div>
              </div>
          `;

      box.appendChild(div);
    }

    function removeOption(btn) {
      btn.closest('.option-row').remove();
    }

    document.getElementById('question_type').addEventListener('change', syncQuestionTypeUI);
    syncQuestionTypeUI();
  </script>

@endsection
