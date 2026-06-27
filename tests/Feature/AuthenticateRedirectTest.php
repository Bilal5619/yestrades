<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticateRedirectTest extends TestCase
{
  public function test_guest_frontend_route_redirects_to_user_login(): void
  {
    Route::middleware('web')
      ->get('/_test/frontend-auth', function () {
        return 'ok';
      })
      ->middleware('auth:web')
      ->name('frontend.testing.auth');

    $response = $this->get('/_test/frontend-auth');

    $response->assertRedirect(route('user.login'));
  }
}
