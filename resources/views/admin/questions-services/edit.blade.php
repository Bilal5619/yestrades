@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Edit Question</h4>
  </div>

  <div class="card">

    <div class="card-body">

      <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @php
          $targetType = old('target_type', $question->service_package_id ? 'package' : 'service');
        @endphp

        <!-- TARGET -->
        <div class="form-group">
          <label>Question Applies To</label>
          <select name="target_type" class="form-control" id="target_type">
            <option value="service" {{ $targetType == 'service' ? 'selected' : '' }}>Single Service</option>
            <option value="package" {{ $targetType == 'package' ? 'selected' : '' }}>Package</option>
          </select>
        </div>

        <div class="form-group" id="service_target_group">
          <label>Service</label>
          <select name="service_id" class="form-control">
            <option value="">-- Select a Service --</option>
            @foreach($services as $svc)
              <option value="{{ $svc->service_id }}" {{ old('service_id', $question->service_id) == $svc->service_id ? 'selected' : '' }}>
                {{ $svc->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group" id="package_target_group" style="display:none;">
          <label>Package</label>
          <select name="service_package_id" class="form-control">
            <option value="">-- Select a Package --</option>
            @foreach($packages as $package)
              <option value="{{ $package->id }}" {{ old('service_package_id', $question->service_package_id) == $package->id ? 'selected' : '' }}>
                {{ $package->title }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- TITLE -->
        <div class="form-group">
          <label>Question Title</label>
          <input type="text" name="title" class="form-control" value="{{ old('title', $question->title) }}" required>
        </div>

        <!-- SUMMARY LABEL -->
        <div class="form-group">
          <label>Summary Label <small class="text-muted">(optional — shown in booking summary instead of question title)</small></label>
          <input type="text" name="summary_label" class="form-control" value="{{ old('summary_label', $question->summary_label) }}" placeholder="e.g. Bedrooms">
        </div>

        <!-- IS REQUIRED -->
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_required">Required <small class="text-muted">(customer must answer before proceeding)</small></label>
          </div>
        </div>

        <!-- IMAGE -->
        <div class="form-group">
          <label>Question Image <small class="text-muted">(optional — displayed as thumbnail in booking modal)</small></label>
          @if($question->image)
            <div class="mb-2">
              <img src="{{ asset('assets/img/questions/' . $question->image) }}" alt="Current image" style="height:80px;width:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
              <small class="d-block text-muted mt-1">Current image — upload a new one to replace it</small>
            </div>
          @endif
          <input type="file" name="image" class="form-control-file" accept="image/jpeg,image/png,image/webp">
          @error('image')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- TYPE -->
        <div class="form-group">
          <label>Field Type</label>
          <select name="type" class="form-control" id="question_type">
            <option value="text" {{ old('type', $question->type) == 'text' ? 'selected' : '' }}>Text</option>
            <option value="select" {{ old('type', $question->type) == 'select' ? 'selected' : '' }}>Select</option>
            <option value="multi" {{ old('type', $question->type) == 'multi' ? 'selected' : '' }}>Multi</option>
            <option value="number" {{ old('type', $question->type) == 'number' ? 'selected' : '' }}>Number</option>
            <option value="quantity" {{ old('type', $question->type) == 'quantity' ? 'selected' : '' }}>Quantity Selector</option>
          </select>
        </div>

        <!-- OPTIONS -->
        <div class="form-group" id="question_options_group">
          <label>Options</label>

          <div id="optionBox">

            @forelse($options[$question->id] ?? [] as $index => $opt)
              <div class="option-row border rounded p-2 mb-2" data-option-index="{{ $index }}">
                <div class="d-flex align-items-start">
                  <input type="hidden" name="option_ids[{{ $index }}]" value="{{ $opt->id }}">
                  <input type="text" name="options[{{ $index }}]" value="{{ old('options.' . $index, $opt->option_text) }}" class="form-control mr-2">

                  <input type="number" name="option_prices[{{ $index }}]" value="{{ old('option_prices.' . $index, $opt->price ?? '') }}" class="form-control mr-2"
                    placeholder="Price" step="0.01" style="max-width: 150px;">

                  <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                    X
                  </button>
                </div>
                <div class="mt-2">
                  <label class="mb-1 small text-muted">Option Image (optional)</label>
                  @if($opt->option_image)
                    <div class="mb-2">
                      <img src="{{ asset('assets/img/questions/' . $opt->option_image) }}" alt="Current option image" style="height:56px;width:56px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                      <small class="text-muted ml-2">Upload a new image to replace it</small>
                    </div>
                  @endif
                  <input type="file" name="option_images[{{ $index }}]" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                  @error('option_images.' . $index)<small class="text-danger">{{ $message }}</small>@enderror
                </div>
              </div>
            @empty
              <div class="option-row border rounded p-2 mb-2" data-option-index="0">
                <div class="d-flex align-items-start">
                  <input type="hidden" name="option_ids[0]" value="">
                  <input type="text" name="options[0]" class="form-control mr-2" placeholder="Enter option">
                  <input type="number" name="option_prices[0]" class="form-control mr-2" placeholder="Price" step="0.01"
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
            @endforelse

          </div>

          <button type="button" class="btn btn-info btn-sm mt-2" onclick="addOption()">
            + Add Option
          </button>
        </div>

        <!-- CONDITIONAL LOGIC -->
        <div class="form-group">
          <label class="d-block">Quantity Settings</label>
          <div class="row border rounded p-3 mx-0" id="quantity_settings_group" style="display: none;">
            <div class="col-md-3 px-1">
              <label>Price Per Unit</label>
              <input type="number" name="quantity_unit_price" class="form-control" placeholder="0.00" step="0.01"
                min="0" value="{{ old('quantity_unit_price', $question->quantity_unit_price) }}">
            </div>
            <div class="col-md-3 px-1">
              <label>Minimum Quantity (Optional)</label>
              <input type="number" name="quantity_min" class="form-control" placeholder="Leave blank for default"
                min="0" step="1" value="{{ old('quantity_min', $question->quantity_min) }}">
            </div>
            <div class="col-md-3 px-1">
              <label>Maximum Quantity (Optional)</label>
              <input type="number" name="quantity_max" class="form-control" placeholder="Leave blank for no limit"
                min="0" step="1" value="{{ old('quantity_max', $question->quantity_max) }}">
            </div>
            <div class="col-md-3 px-1">
              <label>Default Value (Optional)</label>
              <input type="number" name="quantity_default" class="form-control" placeholder="Leave blank for auto"
                min="0" step="1" value="{{ old('quantity_default', $question->quantity_default) }}">
            </div>
            <small class="form-text text-muted mt-2 px-1">
              Customers will be able to type any whole number quantity. The default value is treated as included, and pricing starts only when they go above it.
            </small>
          </div>
        </div>

        <div class="form-group">
          <label>Conditional Logic (Optional)</label>
          <div class="border p-3 rounded">
            <div class="form-check mb-2">
              <input type="checkbox" class="form-check-input" id="enable_condition" onchange="toggleCondition()" {{ $question->conditions->count() > 0 ? 'checked' : '' }}>
              <label class="form-check-label" for="enable_condition">Show this question only if conditions are met</label>
            </div>

            <div id="condition_fields"
              style="{{ $question->conditions->count() > 0 ? 'display: block;' : 'display: none;' }}">
              <div id="conditions_container">
                @if($question->conditions->count() > 0)
                  @foreach($question->conditions as $index => $condition)
                    <div class="condition-row mb-3 border-bottom pb-3">
                      <div class="row">
                        <div class="col-md-2">
                          <label>Operator</label>
                          <select name="condition_operators[]" class="form-control" {{ $index == 0 ? 'disabled' : '' }}>
                            @if($index == 0)
                              <option value="">-- First Condition --</option>
                            @else
                              <option value="AND" {{ $condition->operator == 'AND' ? 'selected' : '' }}>AND</option>
                              <option value="OR" {{ $condition->operator == 'OR' ? 'selected' : '' }}>OR</option>
                            @endif
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label>Depends on Question</label>
                          <select name="depends_on_question_ids[]" class="form-control depends_on_question"
                            onchange="loadQuestionOptions(this)">
                            <option value="">-- Select Question --</option>
                            @foreach($allQuestions as $q)
                              <option value="{{ $q->id }}" {{ $condition->depends_on_question_id == $q->id ? 'selected' : '' }}>
                                {{ $q->title }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label>Condition Type</label>
                          <select name="condition_types[]" class="form-control">
                            <option value="equals" {{ $condition->condition_type == 'equals' ? 'selected' : '' }}>Equals
                            </option>
                            <option value="not_equals" {{ $condition->condition_type == 'not_equals' ? 'selected' : '' }}>Not
                              Equals</option>
                            <option value="contains" {{ $condition->condition_type == 'contains' ? 'selected' : '' }}>Contains
                            </option>
                            <option value="not_contains" {{ $condition->condition_type == 'not_contains' ? 'selected' : '' }}>
                              Not Contains</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label>Expected Answer</label>
                          @php
                            $optionList = optional($condition->dependsOnQuestion)->options ?? collect();
                            $selectedOption = $condition->dependsOnOption;
                          @endphp
                          <select name="depends_on_option_ids[]" class="form-control depends_on_option">
                            <option value="">-- Select Answer --</option>
                            @foreach($optionList as $opt)
                              <option value="{{ $opt->id }}" {{ $condition->depends_on_option_id == $opt->id ? 'selected' : '' }}>
                                {{ $opt->option_text }}
                              </option>
                            @endforeach
                            @if($selectedOption && $optionList->where('id', $selectedOption->id)->isEmpty())
                              <option value="{{ $selectedOption->id }}" selected>
                                {{ $selectedOption->option_text }}
                              </option>
                            @endif
                          </select>
                        </div>
                        <div class="col-md-1">
                          <label>&nbsp;</label>
                          <button type="button" class="btn btn-danger btn-sm" onclick="removeCondition(this)" {{ $question->conditions->count() == 1 ? 'style="display: none;"' : '' }}>×</button>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="condition-row mb-3 border-bottom pb-3">
                    <div class="row">
                      <div class="col-md-2">
                        <label>Operator</label>
                        <select name="condition_operators[]" class="form-control" disabled>
                          <option value="">-- First Condition --</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Depends on Question</label>
                        <select name="depends_on_question_ids[]" class="form-control depends_on_question"
                          onchange="loadQuestionOptions(this)">
                          <option value="">-- Select Question --</option>
                          @foreach($allQuestions as $q)
                            <option value="{{ $q->id }}">{{ $q->title }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label>Condition Type</label>
                        <select name="condition_types[]" class="form-control">
                          <option value="equals">Equals</option>
                          <option value="not_equals">Not Equals</option>
                          <option value="contains">Contains</option>
                          <option value="not_contains">Not Contains</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label>Expected Answer</label>
                        <select name="depends_on_option_ids[]" class="form-control depends_on_option">
                          <option value="">-- Select Answer --</option>
                        </select>
                      </div>
                      <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeCondition(this)"
                          style="display: none;">×</button>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
              <button type="button" class="btn btn-info btn-sm" onclick="addCondition()">
                + Add Another Condition
              </button>
            </div>
          </div>
        </div>

        <button class="btn btn-success">Update</button>

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
      quantityGroup.style.display = shouldShowQuantity ? 'flex' : 'none';
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
        // Clear all condition fields
        document.querySelectorAll('.depends_on_question').forEach(select => select.value = '');
        document.querySelectorAll('.depends_on_option').forEach(select => select.value = '');
      }
    }

    function loadServiceQuestions() {
      const targetType = document.getElementById('target_type').value;
      const targetId = targetType === 'package'
        ? document.querySelector('select[name="service_package_id"]').value
        : document.querySelector('select[name="service_id"]').value;
      const questionSelects = document.querySelectorAll('.depends_on_question');

      if (!targetId) {
        questionSelects.forEach(select => {
          select.innerHTML = '<option value="">-- Select Question --</option>';
        });
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
          questionSelects.forEach(select => {
            select.innerHTML = '<option value="">-- Select Question --</option>';
            data.forEach(question => {
              select.innerHTML += `<option value="${question.id}">${question.title}</option>`;
            });
          });
        })
        .catch(error => console.error('Error loading questions:', error));
    }

    function loadQuestionOptions(element) {
      const questionId = element.value;
      const conditionRow = element.closest('.condition-row');
      const optionSelect = conditionRow.querySelector('.depends_on_option');

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

    function addCondition() {
      const container = document.getElementById('conditions_container');
      const conditionRows = container.querySelectorAll('.condition-row');
      const newRow = conditionRows[0].cloneNode(true);

      // Update the operator select
      const operatorSelect = newRow.querySelector('select[name="condition_operators[]"]');
      operatorSelect.innerHTML = `
            <option value="AND">AND</option>
            <option value="OR">OR</option>
          `;
      operatorSelect.disabled = false;

      // Clear values
      newRow.querySelector('.depends_on_question').value = '';
      newRow.querySelector('.depends_on_option').value = '';
      newRow.querySelector('select[name="condition_types[]"]').value = 'equals';

      // Show remove button
      newRow.querySelector('.btn-danger').style.display = 'block';

      container.appendChild(newRow);
    }

    function removeCondition(button) {
      const conditionRow = button.closest('.condition-row');
      conditionRow.remove();
    }

    function syncTargetTypeUI(refreshQuestions = true) {
      const isPackage = document.getElementById('target_type').value === 'package';
      document.getElementById('service_target_group').style.display = isPackage ? 'none' : 'block';
      document.getElementById('package_target_group').style.display = isPackage ? 'block' : 'none';
      if (refreshQuestions) {
        loadServiceQuestions();
      }
    }

    // Load questions when service is selected
    document.getElementById('target_type').addEventListener('change', syncTargetTypeUI);
    document.querySelector('select[name="service_id"]').addEventListener('change', loadServiceQuestions);
    document.querySelector('select[name="service_package_id"]').addEventListener('change', loadServiceQuestions);
    document.getElementById('question_type').addEventListener('change', syncQuestionTypeUI);
    syncTargetTypeUI(false);
    syncQuestionTypeUI();
  </script>

@endsection
