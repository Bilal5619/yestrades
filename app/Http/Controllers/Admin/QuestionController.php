<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionCondition;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;

class QuestionController extends Controller
{
    protected function validateQuestion(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,select,multi,number,quantity',
            'target_type' => 'required|in:service,package',
            'service_id' => 'nullable|required_if:target_type,service|exists:services,id',
            'service_package_id' => 'nullable|required_if:target_type,package|exists:service_packages,id',
            'quantity_unit_price' => 'nullable|required_if:type,quantity|numeric|min:0',
            'quantity_min' => 'nullable|integer|min:0',
            'quantity_max' => 'nullable|integer|min:0',
            'quantity_default' => 'nullable|integer|min:0',
            'options.*' => 'nullable|string|max:255',
            'option_prices.*' => 'nullable|numeric|min:0',
            'option_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    protected function optionImageDirectory(): string
    {
        return public_path('assets/img/questions/');
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
            $image = UploadFile::store(public_path('assets/img/questions/'), $request->file('image'));
        }

        return [
            'title' => $request->input('title'),
            'summary_label' => $request->filled('summary_label') ? $request->input('summary_label') : null,
            'is_required' => $request->boolean('is_required'),
            'image' => $image,
            'type' => $type,
            'service_id' => $request->input('target_type') === 'service' ? $request->input('service_id') : null,
            'service_package_id' => $request->input('target_type') === 'package' ? $request->input('service_package_id') : null,
            'quantity_unit_price' => $type === 'quantity' && $request->filled('quantity_unit_price')
                ? $request->input('quantity_unit_price')
                : null,
            'quantity_min' => $type === 'quantity' ? $quantityMin : null,
            'quantity_max' => $type === 'quantity' ? $quantityMax : null,
            'quantity_default' => $type === 'quantity' ? $quantityDefault : null,
        ];
    }

    protected function syncQuestionOptions(Question $question, Request $request): void
    {
        if (!in_array($question->type, ['select', 'multi'], true)) {
            $question->options()->get()->each(function (QuestionOption $option) {
                $this->deleteOptionImage($option->option_image);
                $option->delete();
            });
            return;
        }

        if (!$request->has('options') || !is_array($request->options)) {
            $question->options()->get()->each(function (QuestionOption $option) {
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

            $payload = [
                'question_id' => $question->id,
                'option_text' => $opt,
                'price' => $request->option_prices[$key] ?? null,
                'option_image' => $optionImage,
            ];

            if ($option) {
                $option->update($payload);
                $keptOptionIds[] = $option->id;
            } else {
                $createdOption = QuestionOption::create($payload);
                $keptOptionIds[] = $createdOption->id;
            }
        }

        $existingOptions
            ->reject(function (QuestionOption $option) use ($keptOptionIds) {
                return in_array($option->id, $keptOptionIds, true);
            })
            ->each(function (QuestionOption $option) {
                $this->deleteOptionImage($option->option_image);
                $option->delete();
            });
    }

    protected function conditionOperatorFor(Request $request, int $key): ?string
    {
        if ($key === 0) {
            return null;
        }

        $operators = $request->input('condition_operators', []);

        return $operators[$key] ?? $operators[$key - 1] ?? null;
    }

    public function index(Request $request)
    {
        $services = ServiceContent::select('service_id', 'name')->get()->unique('service_id');
        $packages = ServicePackage::orderBy('title')->get(['id', 'title']);

        $questionsQuery = Question::query();
        if ($request->filled('service_id')) {
            $questionsQuery->where('service_id', $request->service_id);
        }
        if ($request->filled('service_package_id')) {
            $questionsQuery->where('service_package_id', $request->service_package_id);
        }
        $questions = $questionsQuery->get();

        $options = QuestionOption::all()->groupBy('question_id');
        $selectedService = $request->service_id;
        $selectedPackage = $request->service_package_id;

        return view('admin.questions-services.index', compact('questions', 'options', 'services', 'packages', 'selectedService', 'selectedPackage'));
    }

    public function create()
    {
        $services = ServiceContent::select('service_id', 'name')->get()->unique('service_id');
        $packages = ServicePackage::orderBy('title')->get(['id', 'title']);
        return view('admin.questions-services.create', compact('services', 'packages'));
    }

    public function show($id)
    {
        $question = Question::with('options')->findOrFail($id);
        $options = QuestionOption::all()->groupBy('question_id');
        return view('admin.questions-services.show', compact('question', 'options'));
    }

    public function store(Request $request)
    {
        $this->validateQuestion($request);

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_min') &&
            $request->filled('quantity_max') &&
            (int) $request->input('quantity_max') < (int) $request->input('quantity_min')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_max' => 'Maximum quantity must be greater than or equal to minimum quantity.']);
        }

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_default') &&
            $request->filled('quantity_min') &&
            (int) $request->input('quantity_default') < (int) $request->input('quantity_min')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_default' => 'Default quantity must be greater than or equal to minimum quantity.']);
        }

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_default') &&
            $request->filled('quantity_max') &&
            (int) $request->input('quantity_default') > (int) $request->input('quantity_max')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_default' => 'Default quantity must be less than or equal to maximum quantity.']);
        }

        $question = Question::create($this->questionPayload($request, null));
        $this->syncQuestionOptions($question, $request);

        // Handle conditions
        if ($request->has('depends_on_question_ids') && !empty($request->depends_on_question_ids[0])) {
            foreach ($request->depends_on_question_ids as $key => $dependsOnQuestionId) {
                if (!empty($dependsOnQuestionId)) {
                    QuestionCondition::create([
                        'question_id' => $question->id,
                        'depends_on_question_id' => $dependsOnQuestionId,
                        'depends_on_option_id' => $request->depends_on_option_ids[$key] ?? null,
                        'condition_type' => $request->condition_types[$key] ?? 'equals',
                        'condition_value' => null, // Not used in current implementation
                        'operator' => $this->conditionOperatorFor($request, $key)
                    ]);
                }
            }
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully');
    }

    public function edit($id)
    {
        $question = Question::with('options', 'conditions')->findOrFail($id);
        $options = QuestionOption::all()->groupBy('question_id');
        $services = ServiceContent::select('service_id', 'name')->get()->unique('service_id');
        $packages = ServicePackage::orderBy('title')->get(['id', 'title']);
        $allQuestions = Question::query()
            ->where('id', '!=', $id)
            ->when($question->service_package_id, function ($query) use ($question) {
                $query->where('service_package_id', $question->service_package_id);
            }, function ($query) use ($question) {
                $query->where('service_id', $question->service_id);
            })
            ->get();
        return view('admin.questions-services.edit', compact('question', 'options', 'services', 'packages', 'allQuestions'));
    }

    public function update(Request $request, $id)
    {
        $this->validateQuestion($request);

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_min') &&
            $request->filled('quantity_max') &&
            (int) $request->input('quantity_max') < (int) $request->input('quantity_min')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_max' => 'Maximum quantity must be greater than or equal to minimum quantity.']);
        }

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_default') &&
            $request->filled('quantity_min') &&
            (int) $request->input('quantity_default') < (int) $request->input('quantity_min')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_default' => 'Default quantity must be greater than or equal to minimum quantity.']);
        }

        if (
            $request->input('type') === 'quantity' &&
            $request->filled('quantity_default') &&
            $request->filled('quantity_max') &&
            (int) $request->input('quantity_default') > (int) $request->input('quantity_max')
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['quantity_default' => 'Default quantity must be less than or equal to maximum quantity.']);
        }

        $question = Question::findOrFail($id);

        if ($request->hasFile('image') && $question->image) {
            $oldPath = public_path('assets/img/questions/' . $question->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $question->update($this->questionPayload($request, $question->image));
        $this->syncQuestionOptions($question, $request);

        // Handle conditions
        QuestionCondition::where('question_id', $id)->delete(); // Remove old conditions
        if ($request->has('depends_on_question_ids') && !empty($request->depends_on_question_ids[0])) {
            foreach ($request->depends_on_question_ids as $key => $dependsOnQuestionId) {
                if (!empty($dependsOnQuestionId)) {
                    QuestionCondition::create([
                        'question_id' => $question->id,
                        'depends_on_question_id' => $dependsOnQuestionId,
                        'depends_on_option_id' => $request->depends_on_option_ids[$key] ?? null,
                        'condition_type' => $request->condition_types[$key] ?? 'equals',
                        'condition_value' => null, // Not used in current implementation
                        'operator' => $this->conditionOperatorFor($request, $key)
                    ]);
                }
            }
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully!');
    }

    // API methods for conditional logic
    public function getQuestionsByService($serviceId)
    {
        $questions = Question::where('service_id', $serviceId)->get(['id', 'title']);
        return response()->json($questions);
    }

    public function getQuestionsByPackage($packageId)
    {
        $questions = Question::where('service_package_id', $packageId)->get(['id', 'title']);
        return response()->json($questions);
    }

    public function getQuestionOptions($questionId)
    {
        $options = QuestionOption::where('question_id', $questionId)->get(['id', 'option_text']);
        return response()->json($options);
    }

    public function destroy($id)
    {
        // Delete related records first
        QuestionOption::where('question_id', $id)->get()->each(function (QuestionOption $option) {
            $this->deleteOptionImage($option->option_image);
            $option->delete();
        });
        QuestionCondition::where('question_id', $id)->delete();
        QuestionCondition::where('depends_on_question_id', $id)->delete();

        // Delete the question and its image
        $question = Question::findOrFail($id);
        if ($question->image) {
            $oldPath = public_path('assets/img/questions/' . $question->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully!');
    }
}
