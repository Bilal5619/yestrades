<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Services\ServiceReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
  public function index(Request $request)
  {
    $vendorId = Auth::guard('vendor')->id();

    $query = ServiceReview::query()
      ->with(['booking.serviceContent', 'service.content'])
      ->where('vendor_id', $vendorId)
      ->whereNotNull('reviewed_at')
      ->latest('reviewed_at');

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('term')) {
      $term = $request->term;
      $query->where(function ($builder) use ($term) {
        $builder->where('comment', 'like', '%' . $term . '%')
          ->orWhereHas('booking', function ($bookingQuery) use ($term) {
            $bookingQuery->where('order_number', 'like', '%' . $term . '%')
              ->orWhere('customer_name', 'like', '%' . $term . '%');
          });
      });
    }

    $reviews = $query->paginate(20)->appends($request->query());

    return view('vendors.reviews.index', compact('reviews'));
  }
}
