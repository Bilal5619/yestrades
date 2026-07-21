@extends('frontend.layout')

@php
  $siteInfo = $basicInfo ?? null;
  $businessEmail = $siteInfo->email_address ?? '[ADD BUSINESS EMAIL]';
  $businessPhone = $siteInfo->contact_number ?? '[ADD BUSINESS PHONE]';
  $businessAddress = $siteInfo->address ?? '[ADD BUSINESS ADDRESS IF AVAILABLE]';
@endphp

@section('pageHeading')
  {{ __('Privacy Policy') }}
@endsection

@section('metaDescription')
  {{ __('Privacy Policy for yestrades, explaining how personal data is collected, used, stored and protected.') }}
@endsection

@section('content')
  <style>
    @include('frontend.partials.legal-page-styles')
  </style>

  <section class="lg-hero">
    <div class="lg-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="lg-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ __('Privacy Policy') }}</span>
      </nav>
      <h1>{{ __('Privacy Policy') }}</h1>
      <p>{{ __('How we collect, use, store and protect your personal data.') }}</p>
    </div>
  </section>

  <section class="lg-content-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <article class="legal-page-content" data-aos="fade-up">
            <span class="lg-updated-chip"><i class="fal fa-clock"></i>{{ __('Last updated:') }} 24 June 2026</span>

            <p>yestrades ("we", "our", "us") respects your privacy and is committed to protecting your personal data. This Privacy Policy explains how we collect, use, store and protect your information when you use our website, services, booking forms, vendor platform or contact us.</p>
            <p><strong>Website:</strong> <a href="https://yestrades.co.uk">https://yestrades.co.uk</a></p>

            <h2>1. Who we are</h2>
            <p>yestrades is an online service booking platform for UK customers looking for services such as plumbing, gas, electrical, cleaning, landlord certificates and related property services.</p>
            <p>For privacy questions, please contact us:</p>
            <ul>
              <li>Email: {{ $businessEmail }}</li>
              <li>Phone: {{ $businessPhone }}</li>
              <li>Address: {{ $businessAddress }}</li>
            </ul>

            <h2>2. Information we collect</h2>
            <p>We may collect the following information:</p>
            <h3>Customer information:</h3>
            <ul>
              <li>Name</li>
              <li>Email address</li>
              <li>Phone number</li>
              <li>Property address or service address</li>
              <li>Postcode</li>
              <li>Booking details</li>
              <li>Service requirements</li>
              <li>Payment status and transaction reference</li>
              <li>Messages or support requests</li>
            </ul>
            <h3>Vendor information:</h3>
            <ul>
              <li>Name</li>
              <li>Business name</li>
              <li>Email address</li>
              <li>Phone number</li>
              <li>Address or postcode coverage area</li>
              <li>Business type</li>
              <li>Service categories</li>
              <li>Verification documents where required, such as insurance, ID, right to work or certificates</li>
              <li>Job, invoice and payment-related information</li>
            </ul>
            <h3>Website information:</h3>
            <ul>
              <li>IP address</li>
              <li>Device and browser information</li>
              <li>Pages visited</li>
              <li>Cookies and analytics data</li>
              <li>Form submissions</li>
            </ul>

            <h2>3. How we collect information</h2>
            <p>We collect information when you:</p>
            <ul>
              <li>Use our website</li>
              <li>Create an account</li>
              <li>Make a booking</li>
              <li>Submit a contact form</li>
              <li>Register as a vendor</li>
              <li>Upload documents</li>
              <li>Contact us by phone, email, WhatsApp or website forms</li>
              <li>Use payment or booking features on our website</li>
            </ul>

            <h2>4. Why we use your information</h2>
            <p>We use your information to:</p>
            <ul>
              <li>Provide and manage bookings</li>
              <li>Contact customers about services</li>
              <li>Match customers with suitable vendors</li>
              <li>Process payments and refunds</li>
              <li>Send booking confirmations and service updates</li>
              <li>Manage vendor accounts and verification</li>
              <li>Issue invoices, certificates or job documents</li>
              <li>Improve website performance and customer experience</li>
              <li>Prevent fraud, misuse or unauthorised access</li>
              <li>Comply with legal, tax, accounting and regulatory requirements</li>
            </ul>

            <h2>5. Lawful basis for using your data</h2>
            <p>We process personal data under the following lawful bases:</p>
            <ul>
              <li>Contract: to provide services, bookings and vendor arrangements</li>
              <li>Legal obligation: to keep required business, tax, accounting and compliance records</li>
              <li>Legitimate interests: to operate, improve and protect our business and website</li>
              <li>Consent: where you choose to receive marketing or accept optional cookies</li>
            </ul>

            <h2>6. Payments</h2>
            <p>Payments may be handled through third-party payment providers. We do not store full card details on our website. Payment providers may process your payment information according to their own privacy policies and security standards.</p>

            <h2>7. Sharing your information</h2>
            <p>We may share your information with:</p>
            <ul>
              <li>Service providers or vendors who need details to complete the booking</li>
              <li>Payment processors</li>
              <li>Website hosting and IT service providers</li>
              <li>Email, SMS or WhatsApp communication providers</li>
              <li>Professional advisers such as accountants, lawyers or insurers</li>
              <li>Authorities or regulators where required by law</li>
            </ul>
            <p>We do not sell your personal data.</p>

            <h2>8. Vendor verification documents</h2>
            <p>If you register as a vendor, we may ask for documents such as insurance, identity, right to work, certificates or business information. These are used for verification, safety, compliance and platform approval purposes.</p>

            <h2>9. Cookies and analytics</h2>
            <p>Our website may use cookies or similar technologies to:</p>
            <ul>
              <li>Keep the website working properly</li>
              <li>Remember user preferences</li>
              <li>Improve website performance</li>
              <li>Understand how visitors use the website</li>
              <li>Support security and fraud prevention</li>
            </ul>
            <p>You can control cookies through your browser settings. If we use non-essential cookies, we will request consent where required.</p>

            <h2>10. How long we keep your data</h2>
            <p>We keep personal data only for as long as necessary for the purpose it was collected.</p>
            <p>Booking, invoice and payment records may be kept for legal, tax and accounting purposes. Contact form data may be kept for as long as needed to respond to your enquiry. Vendor verification records may be kept while the vendor account is active and for a reasonable period afterwards where required for compliance or dispute handling.</p>

            <h2>11. Data security</h2>
            <p>We take reasonable steps to protect personal data from loss, misuse, unauthorised access, disclosure or alteration. However, no website or online service can be guaranteed to be completely secure.</p>

            <h2>12. Your rights</h2>
            <p>Under UK data protection law, you may have rights including:</p>
            <ul>
              <li>The right to access your personal data</li>
              <li>The right to correct inaccurate data</li>
              <li>The right to request deletion of your data</li>
              <li>The right to restrict processing</li>
              <li>The right to object to certain processing</li>
              <li>The right to data portability</li>
              <li>The right to withdraw consent where processing is based on consent</li>
            </ul>
            <p>To exercise your rights, contact us using the details above.</p>

            <h2>13. Marketing</h2>
            <p>We may send marketing messages only where allowed by law or where you have consented. You can unsubscribe or ask us to stop marketing messages at any time.</p>

            <h2>14. Third-party links</h2>
            <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of those websites.</p>

            <h2>15. Children's privacy</h2>
            <p>Our services are not intended for children. We do not knowingly collect personal data from children.</p>

            <h2>16. Complaints</h2>
            <p>If you are unhappy with how we use your personal data, please contact us first so we can try to resolve the issue.</p>
            <p>You also have the right to complain to the Information Commissioner's Office, the UK data protection regulator.</p>
            <p><strong>ICO website:</strong> <a href="https://ico.org.uk" target="_blank" rel="noopener">https://ico.org.uk</a></p>

            <h2>17. Changes to this policy</h2>
            <p>We may update this Privacy Policy from time to time. The latest version will always be available on this page.</p>
          </article>
        </div>
      </div>
    </div>
  </section>
  <!-- /lg-content-section -->
@endsection
