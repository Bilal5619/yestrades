<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;

class LegalPageController extends Controller
{
  public function privacyPolicy()
  {
    return view('frontend.privacy-policy');
  }

  public function cookiePolicy()
  {
    return view('frontend.cookie-policy');
  }
}
