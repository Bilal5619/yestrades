
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="robots" content="noindex,nofollow">
  <title>Verifying Payment | Yes Trades</title>

  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      background: #f6f8fb;
      color: #253248;
    }

    .payment-box {
      width: 90%;
      max-width: 430px;
      padding: 35px 25px;
      background: white;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 5px 25px rgba(0,0,0,.06);
    }

    h2 {
      margin-bottom: 12px;
    }

    p {
      line-height: 1.6;
      color: #5b6575;
    }

    .spinner {
      width: 34px;
      height: 34px;
      margin: 20px auto;
      border: 4px solid #e7ebf0;
      border-top-color: #1765cc;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .hidden {
      display: none;
    }
  </style>
</head>

<body>
  <main class="payment-box">
    <h2 id="payment-title">Verifying your payment</h2>

    <div id="payment-spinner" class="spinner"></div>

    <p id="payment-message" role="status" aria-live="polite">
      Please do not close this page.
      We are confirming your payment securely.
    </p>

    <p id="payment-error" class="hidden" role="alert"></p>
  </main>

  <script>
    (async function () {
      const paymentIntentId = @json($paymentIntentId);
      const completeUrl = @json(route('frontend.service_booking.stripe.complete'));
      const csrfToken = @json(csrf_token());

      const title = document.getElementById('payment-title');
      const spinner = document.getElementById('payment-spinner');
      const message = document.getElementById('payment-message');
      const errorBox = document.getElementById('payment-error');

      function showFailure() {
        spinner.classList.add('hidden');
        title.textContent = 'Payment verification needs attention';

        message.textContent =
          'We could not confirm your booking automatically. ' +
          'Please contact Yes Trades before attempting another payment.';

        errorBox.classList.remove('hidden');
        errorBox.textContent =
          'Your bank may already have processed the payment. ' +
          'Do not pay again until its status has been checked.';
      }

      try {
        const response = await fetch(completeUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            payment_intent_id: paymentIntentId
          })
        });

        const result = await response.json();

        if (
          response.ok &&
          result.redirectURL &&
          result.redirectURL.startsWith(window.location.origin + '/')
        ) {
          window.location.assign(result.redirectURL);
          return;
        }

        showFailure();

      } catch (error) {
        showFailure();
      }
    })();
  </script>
</body>
</html>
