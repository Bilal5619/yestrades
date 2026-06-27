@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Add Service Question</h4>
  </div>

  <div class="card">

    <div class="card-body">

      <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- TARGET -->
        <div class="form-group">
          <label>Question Applies To</label>
          <select name="target_type" class="form-control" id="target_type">
            <option value="service" {{ old('target_type', 'service') == 'service' ? 'selected' : '' }}>Single Service</option>
            <option value="package" {{ old('target_type') == 'package' ? 'selected' : '' }}>Package</option>
          </select>
        </div>

        <div class="form-group" id="service_target_group">
          <label>Service</label>
          <select name="service_id" class="form-control">
            <option value="">-- Select a Service --</option>
            @foreach($services as $svc)
              <option value="{{ $svc->service_id }}" {{ old('service_id') == $svc->service_id ? 'selected' : '' }}>{{ $svc->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group" id="package_target_group" style="display:none;">
          <label>Package</label>
          <select name="service_package_id" class="form-control">
            <option value="">-- Select a Package --</option>
            @foreach($packages as $package)
              <option value="{{ $package->id }}" {{ old('service_package_id') == $package->id ? 'selected' : '' }}>{{ $package->title }}</option>
            @endforeach
          </select>
        </div>

        <!-- QUESTION TITLE -->
        <div class="form-group">
          <label>Question Title</label>
          <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <!-- SUMMARY LABEL -->
        <div class="form-group">
          <label>Summary Label <small class="text-muted">(optional — shown in booking summary instead of question title)</small></label>
          <input type="text" name="summary_label" class="form-control" value="{{ old('summary_label') }}" placeholder="e.g. Bedrooms">
        </div>

        <!-- IS REQUIRED -->
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_required">Required <small class="text-muted">(customer must answer before proceeding)</small></label>
          </div>
        </div>

        <!-- IMAGE -->
        <div class="form-group">
          <label>Question Image <small class="text-muted">(optional — displayed as thumbnail in booking modal)</small></label>
          <input type="file" name="image" class="form-control-file" accept="image/jpeg,image/png,image/webp">
          @error('image')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- TYPE -->
        <div class="form-group">
          <label>Field Type</label>
          <select name="type" class="form-control" id="question_type">
            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select</option>
            <option value="multi" {{ old('type') == 'multi' ? 'selected' : '' }}>Multi Select</option>
            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
            <option value="quantity" {{ old('type') == 'quantity' ? 'selected' : '' }}>Quantity Selector</option>
          </select>
        </div>

        <!-- OPTIONS -->
        <div class="form-group" id="question_options_group">
          <label>Options</label>

          <div id="optionBox">
            <div class="option-row border rounded p-2 mb-2" data-option-index="0">
              <div class="d-flex align-items-start">
                <input type="hidden" name="option_ids[0]" value="">
                <input type="text" name="options[0]" class="form-control mr-2" placeholder="Option 1" value="{{ old('options.0') }}">
                <input type="number" name="option_prices[0]" class="form-control mr-2" placeholder="Price" step="0.01"
                  value="{{ old('option_prices.0') }}"
                  style="max-width: 150px;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                  X
                </button>
              </div>
              <div class="mt-2">
                <label class="mb-1 small text-muted">Option Image (optional)</label>
                <input type="file" name="option_images[0]" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                @error('option_images.0')<small class="text-danger">{{ $message }}</small>@enderror
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-info btn-sm" onclick="addOption()">
            + Add Option
          </button>
        </div>

        <div class="form-group border rounded p-3" id="quantity_settings_group" style="display: none;">
          <label class="d-block mb-3">Quantity Settings</label>
          <div class="row">
            <div class="col-md-3">
              <label>Price Per Unit</label>
              <input type="number" name="quantity_unit_price" class="form-control" placeholder="0.00" step="0.01"
                min="0" value="{{ old('quantity_unit_price') }}">
            </div>
            <div class="col-md-3">
              <label>Minimum Quantity (Optional)</label>
              <input type="number" name="quantity_min" class="form-control" placeholder="Leave blank for default"
                min="0" step="1" value="{{ old('quantity_min') }}">
            </div>
            <div class="col-md-3">
              <label>Maximum Quantity (Optional)</label>
              <input type="number" name="quantity_max" class="form-control" placeholder="Leave blank for no limit"
                min="0" step="1" value="{{ old('quantity_max') }}">
            </div>
            <div class="col-md-3">
              <label>Default Value (Optional)</label>
              <input type="number" name="quantity_default" class="form-control" placeholder="Leave blank for auto"
                min="0" step="1" value="{{ old('quantity_default') }}">
            </div>
          </div>
          <small class="form-text text-muted mt-2">
            Customers will be able to type any whole number quantity. The default value is treated as included, and pricing starts only when they go above it.
          </small>
        </div>

        <!-- CONDITIONAL LOGIC -->
        <div class="form-group">
          <label>Conditional Logic (Optional)</label>
          <div class="border p-3 rounded">
            <div class="form-check mb-2">
              <input type="checkbox" class="form-check-input" id="enable_condition" onchange="toggleCondition()">
              <label class="form-check-label" for="enable_condition">Show this question only if another question has a
                specific answer</label>
            </div>

            <div id="condition_fields" style="display: none;">
              <div class="row">
                <div class="col-md-4">
                  <label>Depends on Question</label>
                  <select name="depends_on_question_id" id="depends_on_question_id" class="form-control"
                    onchange="loadQuestionOptions()">
                    <option value="">-- Select Question --</option>
                    <!-- Will be populated via AJAX when service is selected -->
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Condition Type</label>
                  <select name="condition_type" class="form-control">
                    <option value="equals">Equals</option>
                    <option value="not_equals">Not Equals</option>
                    <option value="contains">Contains</option>
                    <option value="not_contains">Not Contains</option>
                  </select>
                </div>
                <div class="col-md-5">
                  <label>Expected Answer</label>
                  <select name="depends_on_option_id" id="depends_on_option_id" class="form-control">
                    <option value="">-- Select Answer --</option>
                    <!-- Will be populated when question is selected -->
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button class="btn btn-success">Save Question</button>

        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
          Back
        </a>

      </form>

    </div>
  </div>

  <script>
    function syncQuestionTypeUI() {
      const type = document.getElementById('question_type').value;
      const optionsGroup = document.getElementById('question_options_group');
      const quantityGroup = document.getElementById('quantity_settings_group');
      const shouldShowOptions = type === 'select' || type === 'multi';
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
              <div class="d-flex align-items-start">
                <input type="hidden" name="option_ids[${index}]" value="">
                <input type="text" name="options[${index}]" class="form-control mr-2" placeholder="Option">
                <input type="number" name="option_prices[${index}]" class="form-control mr-2" placeholder="Price" step="0.01" style="max-width: 150px;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">X</button>
              </div>
              <div class="mt-2">
                <label class="mb-1 small text-muted">Option Image (optional)</label>
                <input type="file" name="option_images[${index}]" class="form-control-file" accept="image/jpeg,image/png,image/webp">
              </div>
          `;

      box.appendChild(div);
    }

    function removeOption(btn) {
      btn.closest('.option-row').remove();
    }

    function toggleCondition() {
      const checkbox = document.getElementById('enable_condition');
      const fields = document.getElementById('condition_fields');
      fields.style.display = checkbox.checked ? 'block' : 'none';

      if (!checkbox.checked) {
        document.getElementById('depends_on_question_id').value = '';
        document.getElementById('depends_on_option_id').value = '';
      }
    }

    function loadServiceQuestions() {
      const targetType = document.getElementById('target_type').value;
      const targetId = targetType === 'package'
        ? document.querySelector('select[name="service_package_id"]').value
        : document.querySelector('select[name="service_id"]').value;
      const questionSelect = document.getElementById('depends_on_question_id');

      if (!targetId) {
        questionSelect.innerHTML = '<option value="">-- Select Question --</option>';
        return;
      }

      fetch(`/admin/service-managment/questions/${targetType === 'package' ? 'package' : 'service'}/${targetId}`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
        .then(response => response.json())
        .then(data => {
          questionSelect.innerHTML = '<option value="">-- Select Question --</option>';
          data.forEach(question => {
            questionSelect.innerHTML += `<option value="${question.id}">${question.title}</option>`;
          });
        })
        .catch(error => console.error('Error loading questions:', error));
    }

    function loadQuestionOptions() {
      const questionId = document.getElementById('depends_on_question_id').value;
      const optionSelect = document.getElementById('depends_on_option_id');

      if (!questionId) {
        optionSelect.innerHTML = '<option value="">-- Select Answer --</option>';
        return;
      }

      fetch(`/admin/service-managment/questions/options/${questionId}`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
        .then(response => response.json())
        .then(data => {
          optionSelect.innerHTML = '<option value="">-- Select Answer --</option>';
          data.forEach(option => {
            optionSelect.innerHTML += `<option value="${option.id}">${option.option_text}</option>`;
          });
        })
        .catch(error => console.error('Error loading options:', error));
    }

    function syncTargetTypeUI() {
      const isPackage = document.getElementById('target_type').value === 'package';
      document.getElementById('service_target_group').style.display = isPackage ? 'none' : 'block';
      document.getElementById('package_target_group').style.display = isPackage ? 'block' : 'none';
      loadServiceQuestions();
    }

    // Load questions when service is selected
    document.getElementById('target_type').addEventListener('change', syncTargetTypeUI);
    document.querySelector('select[name="service_id"]').addEventListener('change', loadServiceQuestions);
    document.querySelector('select[name="service_package_id"]').addEventListener('change', loadServiceQuestions);
    document.getElementById('question_type').addEventListener('change', syncQuestionTypeUI);
    syncTargetTypeUI();
    syncQuestionTypeUI();
  </script>

@endsection
