<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteVisitor;
use Illuminate\Http\Request;

class WebsiteVisitorController extends Controller
{
  public function index(Request $request)
  {
    $query = WebsiteVisitor::query()
      ->with(['user', 'latestPageView'])
      ->withCount('pageViews')
      ->latest('last_activity_at');

    if ($request->filled('term')) {
      $term = $request->input('term');
      $query->where(function ($builder) use ($term) {
        $builder->where('visitor_uuid', 'like', '%' . $term . '%')
          ->orWhere('ip_address', 'like', '%' . $term . '%')
          ->orWhere('country', 'like', '%' . $term . '%')
          ->orWhere('city', 'like', '%' . $term . '%')
          ->orWhereHas('user', function ($userQuery) use ($term) {
            $userQuery->where('username', 'like', '%' . $term . '%')
              ->orWhere('email', 'like', '%' . $term . '%')
              ->orWhere('first_name', 'like', '%' . $term . '%')
              ->orWhere('last_name', 'like', '%' . $term . '%');
          });
      });
    }

    $visitors = $query->paginate(20)->appends($request->query());

    return view('admin.website-visitors.index', compact('visitors'));
  }

  public function show($id)
  {
    $visitor = WebsiteVisitor::with(['user', 'latestPageView'])->findOrFail($id);
    $pageViews = $visitor->pageViews()
      ->latest('visited_at')
      ->paginate(50);

    return view('admin.website-visitors.show', compact('visitor', 'pageViews'));
  }
}
