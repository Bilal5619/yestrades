<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\QuoteQuestion;
use App\Models\QuoteQuestionOption;
use App\Models\Services\ServiceContent;
use App\Models\Services\Services;
use Illuminate\Http\Request;

/**
 * Near-duplicate of Admin\QuestionController, scoped to quote-type services.
 * Quote questions never carry pricing (no options.price, no is_contact_option)
 * and have no conditional-logic support — deliberately simpler than the buy
 * pricing-question system since they only gather information, never a price.
 */
class QuoteQuestionController extends Controller
{
    protected function validateQuestion(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', QuoteQuestion::TYPES),
            'service_id' => 'required|exists:services,id',
            'quantity_min' => 'nullable|integer|min:0',
            'quantity_max' => 'nullable|integer|min:0',
            'quantity_default' => 'nullable|integer|min:0',
            'options.*' => 'nullable|string|max:255',
            'option_descriptions.*' => 'nullable|string|max:2000',
            'option_highlights.*' => 'nullable|string|max:5000',
            'option_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    protected function optionImageDirectory(): string
    {
        return public_path('assets/img/quote-questions/');
    }

    protected function deleteOptionImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $path = $this->optionImageDirectory() . $image;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    protected function questionPayload(Request $request, ?string $existingImage = null): array
    {
        $type = $request->input('type');
        $quantityMin = $request->filled('quantity_min') ? (int) $request->input('quantity_min') : null;
        $quantityMax = $request->filled('quantity_max') ? (int) $request->input('quantity_max') : null;
        $quantityDefault = $request->filled('quantity_default') ? (int) $request->input('quantity_default') : null;

        $image = $existingImage;
        if ($request->hasFile('image')) {
            $image = UploadFile::store(public_path('assets/img/quote-questions/'), $request->file('image'));
        }

        return [
            'title' => $request->input('title'),
            'summary_label' => $request->filled('summary_label') ? $request->input('summary_label') : null,
            'is_required' => $request->boolean('is_required'),
            'image' => $image,
            'type' => $type,
            'service_id' => $request->input('service_id'),
            'quantity_min' => $type === 'quantity' ? $quantityMin : null,
            'quantity_max' => $type === 'quantity' ? $quantityMax : null,
            'quantity_default' => $type === 'quantity' ? $quantityDefault : null,
            'sort_order' => $request->input('sort_order', 0),
        ];
    }

    protected function syncQuestionOptions(QuoteQuestion $question, Request $request): void
    {
        if (!$question->isOptionBased() || !$request->has('options') || !is_array($request->options)) {
            $question->options()->get()->each(function (QuoteQuestionOption $option) {
                $this->deleteOptionImage($option->option_image);
                $option->delete();
            });
            return;
        }

        $existingOptions = $question->options()->get()->keyBy('id');
        $keptOptionIds = [];
        $optionIds = $request->input('option_ids', []);

        foreach ($request->options as $key => $opt) {
            if (empty($opt)) {
                continue;
            }

            $optionId = $optionIds[$key] ?? null;
            $option = $optionId ? $existingOptions->get((int) $optionId) : null;
            $optionImage = $option->option_image ?? null;

            if ($request->hasFile("option_images.$key")) {
                if ($optionImage) {
                    $this->deleteOptionImage($optionImage);
                }
                $optionImage = UploadFile::store($this->optionImageDirectory(), $request->file("option_images.$key"));
            }

            $highlights = collect(preg_split('/\r\n|\r|\n/', (string) $request->input("option_highlights.$key")))
                ->map(fn ($highlight) => trim($highlight))
                ->filter()
                ->values()
                ->all();

            $payload = [
                'quote_question_id' => $question->id,
                'option_text' => $opt,
                'description' => $request->filled("option_descriptions.$key")
                    ? trim((string) $request->input("option_descriptions.$key"))
                    : null,
                'highlights' => $highlights ?: null,
                'option_image' => $optionImage,
            ];

            if ($option) {
                $option->update($payload);
                $keptOptionIds[] = $option->id;
            } else {
                $created = QuoteQuestionOption::create($payload);
                $keptOptionIds[] = $created->id;
            }
        }

        $existingOptions
            ->reject(fn (QuoteQuestionOption $option) => in_array($option->id, $keptOptionIds, true))
            ->each(function (QuoteQuestionOption $option) {
                $this->deleteOptionImage($option->option_image);
                $option->delete();
            });
    }

    public function index(Request $request)
    {
        // Only quote-type services are relevant here.
        $services = Services::where('service_type', 'quote')->get(['id']);
        $serviceNames = ServiceContent::select('service_id', 'name')
            ->whereIn('service_id', $services->pluck('id'))
            ->get()
            ->unique('service_id');

        $questionsQuery = QuoteQuestion::query();
        if ($request->filled('service_id')) {
            $questionsQuery->where('service_id', $request->service_id);
        }
        $questions = $questionsQuery->orderBy('service_id')->orderBy('sort_order')->get();

        $options = QuoteQuestionOption::all()->groupBy('quote_question_id');
        $selectedService = $request->service_id;

        return view('admin.quote-questions.index', compact('questions', 'options', 'serviceNames', 'selectedService'));
    }

    public function create()
    {
        $services = Services::where('service_type', 'quote')->get(['id']);
        $serviceNames = ServiceContent::select('service_id', 'name')
            ->whereIn('service_id', $services->pluck('id'))
            ->get()
            ->unique('service_id');

        return view('admin.quote-questions.create', compact('serviceNames'));
    }

    public function show($id)
    {
        $question = QuoteQuestion::with('options')->findOrFail($id);
        return view('admin.quote-questions.show', compact('question'));
    }

    public function store(Request $request)
    {
        $this->validateQuestion($request);

        $question = QuoteQuestion::create($this->questionPayload($request, null));
        $this->syncQuestionOptions($question, $request);

        return redirect()->route('admin.quote_questions.index', ['service_id' => $question->service_id])
            ->with('success', 'Quote question created successfully');
    }

    public function edit($id)
    {
        $question = QuoteQuestion::with('options')->findOrFail($id);
        $services = Services::where('service_type', 'quote')->get(['id']);
        $serviceNames = ServiceContent::select('service_id', 'name')
            ->whereIn('service_id', $services->pluck('id'))
            ->get()
            ->unique('service_id');

        return view('admin.quote-questions.edit', compact('question', 'serviceNames'));
    }

    public function update(Request $request, $id)
    {
        $this->validateQuestion($request);

        $question = QuoteQuestion::findOrFail($id);

        if ($request->hasFile('image') && $question->image) {
            $oldPath = public_path('assets/img/quote-questions/' . $question->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $question->update($this->questionPayload($request, $question->image));
        $this->syncQuestionOptions($question, $request);

        return redirect()->route('admin.quote_questions.index', ['service_id' => $question->service_id])
            ->with('success', 'Quote question updated successfully!');
    }

    public function destroy($id)
    {
        $question = QuoteQuestion::findOrFail($id);

        $question->options()->get()->each(function (QuoteQuestionOption $option) {
            $this->deleteOptionImage($option->option_image);
            $option->delete();
        });

        if ($question->image) {
            $oldPath = public_path('assets/img/quote-questions/' . $question->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $serviceId = $question->service_id;
        $question->delete();

        return redirect()->route('admin.quote_questions.index', ['service_id' => $serviceId])
            ->with('success', 'Quote question deleted successfully!');
    }

    public function getQuoteQuestionsByService($serviceId)
    {
        $questions = QuoteQuestion::where('service_id', $serviceId)->orderBy('sort_order')->get(['id', 'title']);
        return response()->json($questions);
    }
}
