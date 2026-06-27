<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Services\ServiceReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifiedReviewController extends Controller
{
  public function show($token)
  {
    $review = ServiceReview::with(['booking.serviceContent', 'service.content'])
      ->where('token', $token)
      ->first();

    if (!$review) {
      return view('frontend.verified-review.message', [
        'title' => 'Review link invalid',
        'message' => 'This review link is invalid or has expired.',
      ]);
    }

    if ($review->reviewed_at) {
      return view('frontend.verified-review.message', [
        'title' => 'Review already submitted',
        'message' => 'This booking has already been reviewed. Thank you.',
      ]);
    }

    if (!$review->booking || $review->booking->order_status !== 'completed') {
      return view('frontend.verified-review.message', [
        'title' => 'Review not available',
        'message' => 'Reviews can only be submitted after the booking is completed.',
      ]);
    }

    return view('frontend.verified-review.form', compact('review'));
  }

  public function store(Request $request, $token)
  {
    $review = ServiceReview::with('booking')
      ->where('token', $token)
      ->first();

    if (!$review || !$review->booking || $review->booking->order_status !== 'completed') {
      return redirect()->route('frontend.verified_review.show', $token);
    }

    if ($review->reviewed_at) {
      return redirect()->route('frontend.verified_review.show', $token);
    }

    $validated = $request->validate([
      'rating' => ['required', 'integer', 'between:1,5'],
      'comment' => ['required', 'string', 'max:2000'],
      'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);

    $image = null;
    if ($request->hasFile('image')) {
      $image = UploadFile::store(public_path('assets/img/service-reviews/'), $request->file('image'));
    }

    $review->update([
      'rating' => $validated['rating'],
      'comment' => $validated['comment'],
      'image' => $image,
      'status' => 'pending',
      'reviewed_at' => now(),
    ]);

    Session::flash('success', 'Thank you. Your review has been submitted and is waiting for approval.');

    return redirect()->route('frontend.verified_review.show', $token);
  }
}
