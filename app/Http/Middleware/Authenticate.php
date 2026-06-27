<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  protected function redirectTo($request)
  {
    if ($request->expectsJson()) {
      return null;
    }

    if ($request->routeIs('admin.*') || $request->is('admin') || $request->is('admin/*')) {
      return route('admin.login');
    }

    if ($request->routeIs('vendor.*') || $request->is('vendor') || $request->is('vendor/*')) {
      return route('vendor.login');
    }

    if ($request->routeIs('staff.*') || $request->is('staff') || $request->is('staff/*')) {
      return route('staff.login');
    }

    if (
      $request->routeIs('user.*') ||
      $request->routeIs('frontend.*') ||
      $request->is('user') ||
      $request->is('user/*') ||
      $request->is('services') ||
      $request->is('services/*')
    ) {
      return route('user.login');
    }

    return route('user.login');
  }
}
