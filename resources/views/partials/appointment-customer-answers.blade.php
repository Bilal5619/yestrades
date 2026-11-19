@once
  <style>
    .appointment-answer-list {
      display: grid;
      gap: 14px;
    }

    .appointment-answer-card {
      border: 1px solid #e6eaf0;
      border-radius: 8px;
      background: #fff;
      overflow: hidden;
    }
     
    .appointment-answer-header {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding: 14px 16px;
      border-bottom: 1px solid #eef1f5;
      background: #f9fafb;
    }

    .appointment-answer-question {
      display: flex;
      gap: 12px;
      min-width: 0;
    }

    .appointment-answer-number {
      flex: 0 0 32px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #1572e8;
      color: #fff;
      text-align: center;
      line-height: 32px;
      font-weight: 700;
      font-size: 13px;
    }

    .appointment-answer-title {
      margin: 0;
      color: #1f2937;
      font-size: 14px;
      font-weight: 700;
      line-height: 1.35;
      word-break: break-word;
    }

    .appointment-answer-meta {
      margin-top: 5px;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .appointment-answer-pill {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 3px 8px;
      background: #eef2ff;
      color: #374151;
      font-size: 11px;
      font-weight: 600;
      line-height: 1.2;
    }

    .appointment-answer-pill.required {
      background: #fff1f2;
      color: #be123c;
    }

    .appointment-answer-body {
      padding: 14px 16px 16px;
    }

    .appointment-answer-value {
      margin: 0;
      color: #111827;
      font-size: 15px;
      font-weight: 600;
      line-height: 1.45;
      white-space: pre-wrap;
      word-break: break-word;
    }

    .appointment-answer-options {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .appointment-answer-option {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #d8e7ff;
      border-radius: 6px;
      background: #f4f8ff;
      padding: 7px 10px;
      color: #1f2937;
      font-size: 13px;
      font-weight: 600;
    }

    .appointment-answer-price {
      color: #157347;
      font-size: 12px;
      font-weight: 700;
    }

    .appointment-answer-image {
      width: 56px;
      height: 56px;
      border-radius: 6px;
      object-fit: cover;
      border: 1px solid #e5e7eb;
    }

    .appointment-answer-empty {
      color: #6b7280;
      font-size: 13px;
      font-style: italic;
    }

    @media (max-width: 575.98px) {
      .appointment-answer-header {
        flex-direction: column;
      }
    }
  </style>
@endonce

<div class="appointment-answer-list">
  @foreach ($answers as $answer)
    @php
      $question = $answer->question;
      $questionTitle = optional($question)->title ?? __('Question');
      $summaryLabel = optional($question)->summary_label;
      $questionType = optional($question)->type;
      $questionTypeLabel = $questionType ? __(ucfirst(str_replace('_', ' ', $questionType))) : __('Answer');
      $isRequired = (bool) optional($question)->is_required;
      $rawValue = $answer->answer;
      $decodedValue = json_decode($rawValue, true);
      $hasJsonValue = json_last_error() === JSON_ERROR_NONE && is_array($decodedValue);

      // The cart saves select/multi answers as option IDs; show the option text instead.
      // Note: questions.options is a legacy text column that shadows the options()
      // relation, so the relation must be read explicitly.
      $questionOptions = $question
        ? ($question->relationLoaded('options') ? $question->getRelation('options') : $question->options()->get())
        : collect();
      $optionTexts = in_array($questionType, ['select', 'multi'], true) ? $questionOptions->pluck('option_text', 'id') : collect();
      $toOptionText = function ($value) use ($optionTexts) {
        return is_numeric($value) && $optionTexts->has((int) $value) ? $optionTexts->get((int) $value) : $value;
      };
      if ($hasJsonValue) {
        $decodedValue = array_map($toOptionText, $decodedValue);
      }

      $displayValue = $hasJsonValue ? null : $toOptionText($rawValue);
      if ($questionType === 'quantity' && $displayValue !== null && $displayValue !== '') {
        $unitPrice = optional($question)->quantity_unit_price;
        $displayValue = $displayValue . ' ' . ((int) $displayValue === 1 ? __('item') : __('items'));
        if ($unitPrice !== null && $unitPrice !== '') {
          $displayValue .= ' (' . __('Unit price') . ': ' . number_format((float) $unitPrice, 2, '.', ',') . ')';
        }
      }
    @endphp

    <div class="appointment-answer-card">
      <div class="appointment-answer-header">
        <div class="appointment-answer-question">
          <span class="appointment-answer-number">{{ $loop->iteration }}</span>
          <div>
            <h6 class="appointment-answer-title">{{ $summaryLabel ?: $questionTitle }}</h6>
            @if ($summaryLabel && $summaryLabel !== $questionTitle)
              <div class="text-muted small mt-1">{{ $questionTitle }}</div>
            @endif
            <div class="appointment-answer-meta">
              <span class="appointment-answer-pill">{{ $questionTypeLabel }}</span>
              @if ($isRequired)
                <span class="appointment-answer-pill required">{{ __('Required') }}</span>
              @endif
            </div>
          </div>
        </div>
        @if (!empty(optional($question)->image))
          <img class="appointment-answer-image" src="{{ asset('assets/img/questions/' . $question->image) }}" alt="">
        @endif
      </div>
      <div class="appointment-answer-body">
        @if ($hasJsonValue && count($decodedValue) > 0)
          <ul class="appointment-answer-options">
            @foreach ($decodedValue as $item)
              <li class="appointment-answer-option">
                <span>{{ $item }}</span>
              </li>
            @endforeach
          </ul>
        @elseif ($displayValue !== null && $displayValue !== '')
          <p class="appointment-answer-value">{{ $displayValue }}</p>
        @else
          <span class="appointment-answer-empty">{{ __('No answer provided') }}</span>
        @endif
      </div>
    </div>
  @endforeach
</div>
