<!-- Footer-area start -->
@if ($secInfo->footer_section_status == 1)
  @php
    $brandName = $websiteInfo->website_title ?? 'yestrades';
    $whatsappNumber = !empty($basicInfo->whatsapp_number) ? preg_replace('/\D/', '', $basicInfo->whatsapp_number) : null;
  @endphp

  <footer class="footer-area eleven-footer">
    <div class="go-top" aria-label="{{ __('Back to top') }}"><i class="fal fa-long-arrow-up"></i></div>

    <div class="footer-signal-wrap">
      <div class="container">
        <div class="footer-signal">
          <div class="footer-signal-blob footer-signal-blob-1"></div>
          <div class="footer-signal-blob footer-signal-blob-2"></div>
          <div class="footer-signal-dots"></div>

          <div class="footer-signal-copy">
            <span class="footer-signal-eyebrow"><span class="footer-signal-dot"></span>{{ __('Ready when you are') }}</span>
            <h2>{{ __('Booked, confirmed and') }} <span>{{ __('sorted — today.') }}</span></h2>
            <p>{{ __('Tell us the job, pick a time and we\'ll match you with a vetted local engineer. No call-outs, no guesswork.') }}</p>
            <div class="footer-signal-actions">
              <a href="{{ route('frontend.services') }}" class="footer-primary-btn">
                {{ __('Book a service now') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              @if ($whatsappNumber)
                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="footer-secondary-btn">
                  <i class="fab fa-whatsapp"></i> {{ __('Chat on WhatsApp') }}
                </a>
              @endif
            </div>
          </div>

          <div class="footer-signal-points">
            <div class="footer-signal-point">
              <span class="footer-signal-icon"><i class="fal fa-bolt"></i></span>
              <div>
                <strong>{{ __('60-second booking') }}</strong>
                <span class="footer-signal-point-label">{{ __('Online, no phone calls needed') }}</span>
              </div>
            </div>
            <div class="footer-signal-point">
              <span class="footer-signal-icon"><i class="fal fa-shield-check"></i></span>
              <div>
                <strong>{{ __('Vetted engineers') }}</strong>
                <span class="footer-signal-point-label">{{ __('Gas Safe & NICEIC registered') }}</span>
              </div>
            </div>
            <div class="footer-signal-point">
              <span class="footer-signal-icon"><i class="fal fa-tag"></i></span>
              <div>
                <strong>{{ __('Fixed pricing') }}</strong>
                <span class="footer-signal-point-label">{{ __('No hidden fees, ever') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-main">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand-column">
            <div class="footer-logo">
              <a href="{{ route('index') }}" target="_self" title="{{ $brandName }}" class="footer-wordmark">
                @if (!empty($websiteInfo->logo))
                  <img src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="{{ $brandName }}" class="footer-logo-img">
                @else
                  <span class="footer-wordmark-text">{{ $brandName }}</span>
                @endif
              </a>
            </div>
            <p class="footer-intro">
              {{ __('yestrades is the UK\'s easiest way to book vetted service engineers - plumbing, gas, electrical, cleaning and landlord certificates.') }}
            </p>
            <div class="footer-badges" aria-label="{{ __('Accreditations') }}">
              <span><i class="fal fa-check-circle"></i> {{ __('GAS SAFE') }}</span>
              <span>{{ __('NICEIC') }}</span>
              <span>{{ __('DBS') }}</span>
            </div>
            @if (count($socialMediaInfos) > 0)
              <div class="social-link footer-social">
                @php
                  $hasTikTokLink = false;
                @endphp
                @foreach ($socialMediaInfos as $socialMediaInfo)
                  @php
                    $iconParts = explode('-', $socialMediaInfo->icon);
                    $iconName = isset($iconParts[1]) ? ucfirst($iconParts[1]) : __('Social link');
                    $isTikTokLink = stripos((string) $socialMediaInfo->icon, 'tiktok') !== false || stripos((string) $socialMediaInfo->url, 'tiktok') !== false;
                    $hasTikTokLink = $hasTikTokLink || $isTikTokLink;
                  @endphp
                  <a href="{{ $socialMediaInfo->url }}" target="_blank" rel="noopener" title="{{ ucfirst($iconName) }}">
                    @if ($isTikTokLink)
                      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path
                          d="M15.4 3c.3 2.1 1.5 3.6 3.6 4.1v3.2c-1.3 0-2.5-.4-3.6-1.1v5.7c0 3.5-2.2 5.9-5.4 5.9-2.9 0-5.1-2-5.1-4.7 0-2.9 2.3-5 5.2-5 .4 0 .8 0 1.1.1v3.3c-.3-.1-.6-.1-.9-.1-1.2 0-2 .7-2 1.7 0 1 .8 1.6 1.8 1.6 1.2 0 1.9-.7 1.9-2.2V3h3.4Z" />
                      </svg>
                    @else
                      <i class="{{ $socialMediaInfo->icon }}"></i>
                    @endif
                  </a>
                @endforeach
                @if (!$hasTikTokLink)
                  <a href="https://www.tiktok.com/@11done" target="_blank" rel="noopener" title="{{ __('TikTok') }}"
                    aria-label="{{ __('TikTok') }}" class="footer-tiktok-link">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path
                        d="M15.4 3c.3 2.1 1.5 3.6 3.6 4.1v3.2c-1.3 0-2.5-.4-3.6-1.1v5.7c0 3.5-2.2 5.9-5.4 5.9-2.9 0-5.1-2-5.1-4.7 0-2.9 2.3-5 5.2-5 .4 0 .8 0 1.1.1v3.3c-.3-.1-.6-.1-.9-.1-1.2 0-2 .7-2 1.7 0 1 .8 1.6 1.8 1.6 1.2 0 1.9-.7 1.9-2.2V3h3.4Z" />
                    </svg>
                  </a>
                @endif
              </div>
            @endif
          </div>

          <div class="footer-link-column">
            <h3>{{ __('Services') }}</h3>
            <ul>
              <li><a href="{{ route('frontend.services') }}">{{ __('Plumbing') }}</a></li>
              <li><a href="{{ route('frontend.services') }}">{{ __('Gas') }}</a></li>
              <li><a href="{{ route('frontend.services') }}">{{ __('Electrical') }}</a></li>
              <li><a href="{{ route('frontend.services') }}">{{ __('Cleaning') }}</a></li>
              <li><a href="{{ route('frontend.services') }}">{{ __('Certificates') }}</a></li>
            </ul>
          </div>

          <div class="footer-link-column">
            <h3>{{ __('For Customers') }}</h3>
            <ul>
              <li><a href="{{ route('index') }}#how-it-works">{{ __('How it works') }}</a></li>
              <li><a href="{{ route('frontend.pricing') }}">{{ __('Pricing') }}</a></li>
              <li><a href="{{ route('faq') }}">{{ __('FAQs') }}</a></li>
              <li><a href="{{ route('contact') }}">{{ __('Help') }}</a></li>
            </ul>
          </div>

          <div class="footer-link-column">
            <h3>{{ __('For Pros') }}</h3>
            <ul>
              {{-- Vendor signup link hidden for now (client request) — restore this <li> to bring it back --}}
              {{-- <li><a href="{{ route('vendor.signup') }}">{{ __('Become an engineer') }}</a></li> --}}
              <li><a href="{{ route('frontend.pricing') }}">{{ __('Pricing') }}</a></li>
            </ul>
          </div>

          <div class="footer-link-column">
            <h3>{{ __('Company') }}</h3>
            <ul>
              <li><a href="{{ route('about_us') }}">{{ __('About') }}</a></li>
              <li><a href="{{ route('careers.index') }}">{{ __('Careers') }}</a></li>
              <li><a href="{{ route('blog') }}">{{ __('Press') }}</a></li>
              <li><a href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
              <li><a href="{{ route('privacy_policy') }}">{{ __('Privacy') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="copy-right-area">
      <div class="container">
        <div class="copy-right-content">
          <span>
            &copy; {{ date('Y') }} {{ $brandName }} Ltd.
          </span>
          <nav class="footer-legal-links" aria-label="{{ __('Legal links') }}">
            <a href="{{ url('/terms-and-conditions') }}">{{ __('Terms') }}</a>
            <a href="{{ route('privacy_policy') }}">{{ __('Privacy') }}</a>
            <a href="{{ route('cookie_policy') }}">{{ __('Cookies') }}</a>
          </nav>
        </div>
      </div>
    </div>
  </footer>
  <!-- Footer-area end-->
@endif
<!-- Add Review Modal Start -->
@include('frontend.services.booking-modal.modal-page')
<!-- Add Review Modal End -->

