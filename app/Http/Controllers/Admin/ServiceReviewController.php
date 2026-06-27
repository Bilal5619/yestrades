<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services\ServiceReview;
use Illuminate\Http\Request;

class ServiceReviewController extends Controller
{
  public function index(Request $request)
  {
    $query = ServiceReview::query()
      ->with(['booking.serviceContent', 'service.content', 'vendor.vendor_info', 'user'])
      ->latest();

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('term')) {
      $term = $request->term;
      $query->where(function ($builder) use ($term) {
        $builder->where('customer_email', 'like', '%' . $term . '%')
          ->orWhere('comment', 'like', '%' . $term . '%')
          ->orWhereHas('booking', function ($bookingQuery) use ($term) {
            $bookingQuery->where('order_number', 'like', '%' . $term . '%')
              ->orWhere('customer_name', 'like', '%' . $term . '%')
              ->orWhere('customer_email', 'like', '%' . $term . '%');
          });
      });
    }

    $reviews = $query->paginate(20)->appends($request->query());

    return view('admin.service-reviews.index', compact('reviews'));
  }

  public function approve($id)
  {
    $review = ServiceReview::findOrFail($id);
    $review->update(['status' => 'approved']);
    ServiceReview::refreshRatings($review->service_id, $review->vendor_id);

    return redirect()->back()->with('success', 'Review approved successfully.');
  }

  public function reject($id)
  {
    $review = ServiceReview::findOrFail($id);
    $review->update(['status' => 'rejected']);
    ServiceReview::refreshRatings($review->service_id, $review->vendor_id);

    return redirect()->back()->with('success', 'Review rejected successfully.');
  }

  public function destroy($id)
  {
    $review = ServiceReview::findOrFail($id);
    $serviceId = $review->service_id;
    $vendorId = $review->vendor_id;

    if ($review->image) {
      @unlink(public_path('assets/img/service-reviews/' . $review->image));
    }

    $review->delete();
    ServiceReview::refreshRatings($serviceId, $vendorId);

    return redirect()->back()->with('success', 'Review deleted successfully.');
  }
}
