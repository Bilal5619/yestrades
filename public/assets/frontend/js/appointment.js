"use strict";
var bookingStepper;
var currentServiceData = {}; // Store service information globally
const csrfToken = $('meta[name="csrf-token"]').attr("content");
$("body").on("click", ".bookNowBtn", function (e) {
  e.preventDefault();
  e.stopPropagation();

  $(".request-loader-time").addClass("show");
  var service_id =
    $(this).data("service-id") || $(this).attr("data-service-id");

  if (!service_id) {
    console.error("No service id found on service cart button.");
    $(".request-loader-time").removeClass("show");
    return;
  }

  // Capture service information from the card
  var $serviceCard = $(this).closest(".product-default, .product-details");
  var rawPriceText = $serviceCard.find(".new-price").text().trim() || "0";
  var numericPrice = parseFloat(rawPriceText.replace(/[^0-9.]/g, "")) || 0;
  currentServiceData = {
    id: service_id,
    name:
      $serviceCard.find(".product-title a, h6 a").text().trim() || "Service",
    price: rawPriceText || "N/A",
    price_value: numericPrice,
    category: $serviceCard.find(".tag").text().trim() || "",
  };

  //service
  $.ajax({
    type: "get",
    url: baseURL + "/services/services-staff-content/" + service_id,
    data: {
      service_id: service_id,
    },
    success: function (response) {
      $("#makeBooking").modal("show");
      $(".request-loader-time").removeClass("show");
      $("#bookInfoShow").html(response);

      var modalPrice = parseFloat($("#service_price_value").val());
      if (!isNaN(modalPrice)) {
        currentServiceData.price_value = modalPrice;
        if (!currentServiceData.price || currentServiceData.price === "N/A") {
          currentServiceData.price = modalPrice.toFixed(2);
        }
      }

      // Do NOT auto-select any payment - let user choose
      // Payment selection happens on Step 4 (Payment page)

      // Service ID is now set in the form from PHP
      // Store it in currentServiceData as well
      currentServiceData.service_id = $("#service_id").val();

      //  Bootstrap Stepper
      bookingStepper = new Stepper(document.querySelector("#booking-stepper"), {
        linear: true,
        animation: true,
      });

      // Load and render questions immediately after stepper is initialized
      // Wait a tiny bit to ensure all scripts in the modal content are executed
      setTimeout(function () {
        if (typeof loadQuestions === "function") {
          try {
            loadQuestions(service_id);
          } catch (e) {
            console.error("❌ Error calling loadQuestions():", e);
          }
        } else {
          console.error("❌ loadQuestions function NOT found!");
        }

        try {
          if (typeof bookingStepper !== "undefined") {
            bookingStepper.to(1);
          }
        } catch (e) {
          console.error("Error moving stepper to step 1:", e);
        }
      }, 100);

      // Bind Complete Payment button from payment page
      $(document)
        .off("click", "#payment_submit_btn")
        .on("click", "#payment_submit_btn", function (e) {
          e.preventDefault();
          e.stopPropagation();

          var gateway = $("#gateway").val();

          if (!gateway || gateway == "") {
            $("#err_gateway").text("Please select a payment method");
            return false;
          }

          $("#err_gateway").text("");
          $(".request-loader-time").addClass("show");
          $("#payment-form").submit();
          return false;
        });

      // Initialize calendar directly for general availability
      var serviceId = service_id; // Use the service ID from the button click
      initializeGeneralCalendar(serviceId);

      /*-----init stripe payment method-----*/
      // set your stripe public key
      var stripe = Stripe(stripe_key);
      // Create a Stripe Element for the card field
      var elements = stripe.elements();
      var cardElement = elements.create("card", {
        style: {
          base: {
            iconColor: "#454545",
            color: "#454545",
            fontWeight: "500",
            lineHeight: "50px",
            fontSmoothing: "antialiased",
            backgroundColor: "#f2f2f2",
            ":-webkit-autofill": {
              color: "#454545",
            },
            "::placeholder": {
              color: "#454545",
            },
          },
        },
      });
      if ($("#stripe-element").length) {
        cardElement.mount("#stripe-element");
      }

      // add focus styling for stripe element
      if (cardElement) {
        cardElement.on("focus", function () {
          $("#stripe-element").addClass("focus");
        });
        cardElement.on("blur", function () {
          $("#stripe-element").removeClass("focus");
        });
      }

      window.initializePaypalCardFields = function () {
        if (window.paypalCardInitialized) return;
        window.paypalCardInitialized = true;

        paypal
          .CardFields({
            createOrder: function () {
              return fetch("/create-paypal-order", {
                method: "post",
                headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                body: JSON.stringify({
                  amount: $("#total_price").val(),
                }),
              })
                .then((res) => res.json())
                .then((data) => data.id);
            },

            onApprove: function (data) {
              return fetch("/capture-paypal-order", {
                method: "post",
                headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                body: JSON.stringify({
                  orderID: data.orderID,
                }),
              })
                .then((res) => res.json())
                .then((response) => {
                  window.location.href = response.redirect_url;
                });
            },

            onError: function (err) {
              console.error("PayPal Error:", err);
              if (window.AppNotify) {
                AppNotify.error("Payment failed");
              } else {
                alert("Payment failed");
              }
            },
          })
          .render("#card-container");
      };

      // Helper to toggle gateway specific UI
      window.handleGatewayChange = function (value) {
        $("#err_currency").html("");
        $("#stripe-errors").html("");
        $("#err_gateway").html("");

        $("#stripe-element").addClass("d-none");
        $("#paypal-card-element").addClass("d-none");
        $("#card-payment-info").hide();
        $("#authorizenet-element").addClass("d-none");
        $("#authorizenet-element input").attr("disabled", "disabled");
        $(".offline-gateway-info").addClass("d-none");

        if (!value) return;

        const dataType = parseInt(value, 10);

        if (isNaN(dataType)) {
          if (value === "stripe") {
            $("#stripe-element").removeClass("d-none");
            $("#card-payment-info").show();
          } else if (value === "paypal_card") {
            $("#paypal-card-element").removeClass("d-none");
            $("#card-payment-info").show();
            setTimeout(() => { initializePaypalCardFields(); }, 200);
          } else if (value === "authorize.net") {
            $("#authorizenet-element").removeClass("d-none");
            $("#authorizenet-element input").removeAttr("disabled");
          }
        } else {
          $("#offline-gateway-" + value).removeClass("d-none");
        }
      };

      function fillBillingHiddenFields(data) {
        var payload = data && data.billing ? data.billing : data || {};
        $("#billing_name").val(payload.name || "");
        $("#billing_phone").val(payload.phone || "");
        $("#billing_email").val(payload.email || "");
        $("#billing_address").val(payload.address || "");
        $("#billing_zip_code").val(payload.zip_code || "");
        $("#billing_country").val(payload.country || "");
      }

      function copySiteDetailsToBilling() {
        $("#billing_name_secondary").val($("#name").val() || "");
        $("#billing_phone_secondary").val($("#phone").val() || "");
        $("#billing_email_secondary").val($("#email").val() || "");
        $("#billing_address_secondary").val($("#address").val() || "");
        $("#billing_zip_secondary").val($("#zip_code").val() || "");
        $("#billing_country_secondary").val($("#country").val() || "");
      }

      function fillAlternateFields(data) {
        var payload = data || {};
        $("#billing_name_alt").val(payload.name || "");
        $("#billing_phone_alt").val(payload.phone || "");
        $("#billing_email_alt").val(payload.email || "");
        $("#billing_address_alt").val(payload.address || "");
        $("#billing_zip_alt").val(payload.zip_code || "");
        $("#billing_country_alt").val(payload.country || "");

        $("#billing_name_secondary").val(payload.name || "");
        $("#billing_phone_secondary").val(payload.phone || "");
        $("#billing_email_secondary").val(payload.email || "");
        $("#billing_address_secondary").val(payload.address || "");
        $("#billing_zip_secondary").val(payload.zip_code || "");
        $("#billing_country_secondary").val(payload.country || "");
      }

      function copyAlternateBillingToSecondary() {
        $("#billing_name_secondary").val($("#billing_name_alt").val() || "");
        $("#billing_phone_secondary").val($("#billing_phone_alt").val() || "");
        $("#billing_email_secondary").val($("#billing_email_alt").val() || "");
        $("#billing_address_secondary").val($("#billing_address_alt").val() || "");
        $("#billing_zip_secondary").val($("#billing_zip_alt").val() || "");
        $("#billing_country_secondary").val($("#billing_country_alt").val() || "");
      }

      window.updateBillingSummaryCard = function () {
        var statusEl = $("#summary-billing-status");
        if (!statusEl.length) {
          return;
        }

        var detailsWrapper = $(
          "#summary-billing-card .billing-details-wrapper",
        );
        var isSame =
          $("#billing_same_input").val() === "1" ||
          $("#billing_same_input").val() === 1;

        if (isSame) {
          statusEl.text(statusEl.data("text-same"));
          detailsWrapper.addClass("d-none");
        } else {
          statusEl.text(statusEl.data("text-different"));
          detailsWrapper.removeClass("d-none");

          $("#summary-billing-name").text(
            $("#billing_name_secondary").val() || "-",
          );
          $("#summary-billing-email").text(
            $("#billing_email_secondary").val() || "-",
          );
          $("#summary-billing-phone").text(
            $("#billing_phone_secondary").val() || "-",
          );

          var addressParts = [];
          if ($("#billing_address_secondary").val())
            addressParts.push($("#billing_address_secondary").val());
          if ($("#billing_zip_secondary").val())
            addressParts.push($("#billing_zip_secondary").val());
          if ($("#billing_country_secondary").val())
            addressParts.push($("#billing_country_secondary").val());
          $("#summary-billing-address").text(
            addressParts.length ? addressParts.join(", ") : "-",
          );
        }
      };

      function toggleAlternateBillingFields(isDifferent) {
        if (isDifferent) {
          $("#alternate_billing_fields").removeClass("d-none");
          $("#billing_same").val(0);
          $("#billing_same_input").val(0);
          $("#billing_same_checkbox").prop("checked", true);
          copyAlternateBillingToSecondary();
        } else {
          $("#alternate_billing_fields").addClass("d-none");
          $("#billing_same").val(1);
          $("#billing_same_input").val(1);
          $("#billing_same_checkbox").prop("checked", false);
          copySiteDetailsToBilling();
        }
      }

      function clearAlternateBillingErrors() {
        [
          "billing_name_alt",
          "billing_phone_alt",
          "billing_email_alt",
          "billing_address_alt",
        ].forEach(function (field) {
          $("#err_" + field).html("");
        });
      }

      window.applyBillingResponse = function (response) {
        if (!response) {
          return;
        }

        // Normalise main contact inputs (if server adjusted formatting)
        ["name", "phone", "email", "address", "zip_code", "country"].forEach(
          function (field) {
            if (typeof response[field] !== "undefined") {
              $("#" + field).val(response[field]);
            }
          },
        );

        fillBillingHiddenFields(response);
        toggleAlternateBillingFields(String(response.billing_same) !== "1");

        if (String(response.billing_same) === "1") {
          copySiteDetailsToBilling();
        } else {
          fillAlternateFields(response.billing || response.alternate || {});
        }

        updateBillingSummaryCard();
      };

      // Init billing toggle events
      $("body").on("change", "#billing_same_checkbox", function () {
        var isDifferent = $(this).is(":checked");
        toggleAlternateBillingFields(isDifferent);
        if (!isDifferent) {
          $(
            "#billing_name_alt, #billing_phone_alt, #billing_email_alt, #billing_address_alt, #billing_zip_alt, #billing_country_alt",
          ).val("");
          clearAlternateBillingErrors();
        }
        updateBillingSummaryCard();
      });

      // If the checkbox exists on load, ensure the UI matches its state
      if ($("#billing_same_checkbox").length) {
        toggleAlternateBillingFields(
          $("#billing_same_checkbox").is(":checked"),
        );
        updateBillingSummaryCard();
      }

      $("body").on("input change", "#name, #phone, #email, #address, #zip_code, #country", function () {
        if ($("#billing_same_input").val() === "1" || $("#billing_same_input").val() === 1) {
          copySiteDetailsToBilling();
          fillBillingHiddenFields({
            name: $("#name").val(),
            phone: $("#phone").val(),
            email: $("#email").val(),
            address: $("#address").val(),
            zip_code: $("#zip_code").val(),
            country: $("#country").val(),
          });
          updateBillingSummaryCard();
        }
      });

      $("body").on("input change", "#billing_name_alt, #billing_phone_alt, #billing_email_alt, #billing_address_alt, #billing_zip_alt, #billing_country_alt", function () {
        if ($("#billing_same_input").val() === "0" || $("#billing_same_input").val() === 0) {
          copyAlternateBillingToSecondary();
          fillBillingHiddenFields({
            name: $("#billing_name_alt").val(),
            phone: $("#billing_phone_alt").val(),
            email: $("#billing_email_alt").val(),
            address: $("#billing_address_alt").val(),
            zip_code: $("#billing_zip_alt").val(),
            country: $("#billing_country_alt").val(),
          });
          updateBillingSummaryCard();
        }
      });

      //submit payment form
      $("#payment-form").submit(function (event) {
        event.preventDefault();
        $(".request-loader-time").addClass("show");
        $("#stripe-errors").text("");
        $("#err_gateway").text("");

        var selectedGateway = $("#gateway").val();

        if (selectedGateway == "stripe") {
          stripe.createToken(cardElement).then(function (result) {
            if (result.error) {
              // Display errors to the customer
              var errorElement = document.getElementById("stripe-errors");
              errorElement.textContent = result.error.message;
              $(".request-loader-time").removeClass("show");
              return; // Prevent further execution
            } else {
              stripeTokenHandler(result.token);
            }
          });
          return; // Don't proceed with AJAX for Stripe yet
        } else if (selectedGateway == "paypal_card") {
          return;
        } else if (selectedGateway == "authorize.net") {
          sendPaymentDataToAnet();
          return;
        }

        submitPaymentFormAjax(event);
      });

      function submitPaymentFormAjax(event) {
        var form = document.getElementById("payment-form");
        var fd = new FormData(form);
        var url = $("#payment-form").attr("action");
        var method = $("#payment-form").attr("method");

        $.ajax({
          url: url,
          method: method,
          data: fd,
          contentType: false,
          processData: false,
          headers: {
            Accept: "application/json",
          },
          success: function (data) {
            $("#featuredBtn").addClass("disabled");
            $("#summary_proceed_payment").addClass("disabled");
            if (data.redirectURL) {
              window.location.href = data.redirectURL;
            } else {
              $("#razorPayForm").html(data);
              $(".request-loader-time").removeClass("show");
            }

            $(".em").each(function () {
              $(this).html("");
            });
            $(".request-loader-time").removeClass("show");
          },
          error: function (error) {
            $("#featuredBtn").removeClass("disabled");
            $("#summary_proceed_payment").removeClass("disabled");
            $(".em").each(function () {
              $(this).html("");
            });

            if (error.status === 422 && error.responseJSON.errors) {
              // Display errors returned by the server
              for (let field in error.responseJSON.errors) {
                document.getElementById("err_" + field).innerHTML =
                  error.responseJSON.errors[field][0];
              }
            } else if (error.status === 422 && error.responseJSON.error) {
              $("#stripe-errors").text(error.responseJSON.error);
              $("#err_gateway").text(error.responseJSON.error);
            } else if (error.status === 500) {
              // Server error
              var errorMsg = "Payment processing error. Please try again.";
              if (error.responseJSON && error.responseJSON.message) {
                errorMsg = error.responseJSON.message;
              }
              $("#err_gateway").text(errorMsg);
              $("#stripe-errors").text(errorMsg);
            } else {
              if (error.responseJSON && error.responseJSON.error) {
                $("#stripe-errors").text(error.responseJSON.error);
                $("#err_gateway").text(error.responseJSON.error);
              } else {
                $("#err_gateway").text("An error occurred. Please try again.");
                $("#stripe-errors").text("An error occurred. Please try again.");
              }
            }
            $(event.target).prop("disabled", false);
            $(".request-loader-time").removeClass("show");
          },
        });
      }

      // Support legacy select-based gateway switching (other pages)
      $("body").on("change", 'select[name="gateway"]', function () {
        const value = $(this).val();
        handleGatewayChange(value);
      });

      // Send the token to your server
      function stripeTokenHandler(token) {
        document.getElementById("stripeToken").value = token.id;
        submitPaymentFormAjax({ target: document.getElementById("payment-form") });
      }
      //Send the authorize.net token to your server
      function sendPaymentDataToAnet() {
        // Set up authorisation to access the gateway.
        var authData = {};
        authData.clientKey = authorize_public_key;
        authData.apiLoginID = authorize_login_key;

        var cardData = {};
        cardData.cardNumber = document.getElementById("anetCardNumber").value;
        cardData.month = document.getElementById("anetExpMonth").value;
        cardData.year = document.getElementById("anetExpYear").value;
        cardData.cardCode = document.getElementById("anetCardCode").value;

        // Now send the card data to the gateway for tokenisation.
        // The responseHandler function will handle the response.
        var secureData = {};
        secureData.authData = authData;
        secureData.cardData = cardData;
        Accept.dispatchData(secureData, responseHandler);
      }

      function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
          var i = 0;
          let errorLists = ``;
          while (i < response.messages.message.length) {
            errorLists += `<li class="text-danger">${response.messages.message[i].text}</li>`;
            i = i + 1;
          }
          $(".request-loader-time").removeClass("show");
          $("#authorizeNetErrors").show();
          $("#authorizeNetErrors").html(errorLists);
        } else {
          paymentFormUpdate(response.opaqueData);
        }
      }

      function paymentFormUpdate(opaqueData) {
        var form = document.getElementById("payment-form");
        document.getElementById("opaqueDataDescriptor").value =
          opaqueData.dataDescriptor;
        document.getElementById("opaqueDataValue").value = opaqueData.dataValue;
        form.submit();
      }
    },
    error: function (xhr, status, error) {
      $(".request-loader-time").removeClass("show");
      $("#bookInfoShow").html(`
  <div class="not-available">
    <div class="icon">
      <i class="fa fa-info"></i>
    </div>
    <h6 class="text-center text-danger">
      ${bookingUnableText}
    </h6>
  </div>
`);

      toastr.error(xhr.responseJSON.error);
    },
  });

  // search staff
  $("body").on("keyup", "#searchVale", function (e) {
    $(".request-loader-time").addClass("show");
    e.preventDefault();
    let searchVal = $("#searchVale").val();
    $.ajax({
      method: "get",
      url: baseURL + "/services/staff/search/" + service_id,
      data: { searchVal: searchVal },
      success: function (res) {
        $(".request-loader-time").removeClass("show");
        $(".staff-slider").html(res);
        // Staff slider
        var staffSlider = new Swiper(".staff-slider", {
          spaceBetween: 24,
          speed: 1000,
          loop: false,
          autoplay: {
            delay: 3000,
          },
          slidesPerView: 1,
          pagination: false,

          pagination: {
            el: "#staff-slider-pagination",
            clickable: true,
          },

          breakpoints: {
            320: {
              slidesPerView: 1,
            },
            576: {
              slidesPerView: 2,
            },
            992: {
              slidesPerView: 3,
            },
          },
        });
      },
    });
  });
});

$("body").on("click", ".login_prev", function () {
  $(".auth-info").removeClass("d-none");
  $("#billing-form").addClass("d-none");
});

//remove error message after click prev button from payment page
$("body").on("click", "#payment_prev", function () {
  $(".em").each(function () {
    $(this).html("");
  });
});

/*=====================login customer=================*/
$(document).on("keydown", function (event) {
  if (event.which == 13 && $("#login-form input:focus").length > 0) {
    event.preventDefault();
    $(".request-loader-time").addClass("show");
    customerLogin(event);
  }
});

function customerLogin() {
  var data = $("#login-form").serialize();
  const csrfToken = $('meta[name="csrf-token"]').attr("content");
  $.ajax({
    type: "post",
    url: baseURL + "/services/login",
    data: data,
    dataType: "json",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },
    success: function (response) {
      $(".request-loader-time").removeClass("show");
      if (response.success) {
        $(".auth-info").addClass("d-none");

        //billing data
        Object.keys(response.billingData).forEach((key) => {
          $(`#${key}`).val(response.billingData[key]);
        });

        $("#billing-form").removeClass("d-none");
        $("#billing_prev").removeClass("login_prev").addClass("back_to_time");
        $("#backtoAuth").addClass("d-none");
        toastr["success"](response.success);

        // Check and bind appropriate click event based on class
        if ($("#billing_prev").hasClass("back_to_time")) {
          $("body")
            .off("click", ".back_to_time")
            .on("click", ".back_to_time", function () {
              bookingStepper.previous();
            });
        }
      } else {
        $("#err_username").addClass("d-none");
        $("#err_password").addClass("d-none");
        toastr["error"](response.error);
      }
    },
    error: function (error) {
      $(".request-loader-time").removeClass("show");
      $(".em").each(function () {
        $(this).html("");
      });

      for (let x in error.responseJSON.errors) {
        document.getElementById("err_" + x).innerHTML =
          error.responseJSON.errors[x][0];
      }
    },
  });
}

/*=====================Urgency Surcharge Helper=================*/

function getBookingTimezone() {
  return $("#booking_timezone_value").val() || "UTC";
}

function getTimeZoneParts(date, timeZone) {
  var parts = new Intl.DateTimeFormat("en-GB", {
    timeZone: timeZone,
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
    hourCycle: "h23",
  }).formatToParts(date);

  return parts.reduce(function (carry, part) {
    if (part.type !== "literal") {
      carry[part.type] = parseInt(part.value, 10);
    }
    return carry;
  }, {});
}

function makeDateInTimeZone(dateValue, timeValue, timeZone) {
  var dateParts = dateValue.split("-");
  var timeParts = timeValue.split(":");
  var year = parseInt(dateParts[0], 10);
  var month = parseInt(dateParts[1], 10);
  var day = parseInt(dateParts[2], 10);
  var hour = parseInt(timeParts[0], 10);
  var minute = parseInt(timeParts[1], 10);
  var utcGuess = Date.UTC(year, month - 1, day, hour, minute, 0);
  var zoneParts = getTimeZoneParts(new Date(utcGuess), timeZone);
  var zoneGuess = Date.UTC(
    zoneParts.year,
    zoneParts.month - 1,
    zoneParts.day,
    zoneParts.hour,
    zoneParts.minute,
    zoneParts.second || 0
  );

  return new Date(utcGuess - (zoneGuess - utcGuess));
}

function applyUrgencySurcharge(bookingDate, startTime) {
  var surcharge = 0;
  var message = "";
  var period = "";
  var timezone = getBookingTimezone();

  console.group("[Urgency] applyUrgencySurcharge called");
  console.log("  bookingDate:", bookingDate, "| startTime:", startTime, "| timezone:", timezone);

  if (bookingDate && startTime) {
    var bookingDateTime;
    var calculatedAt = new Date();
    try {
      bookingDateTime = makeDateInTimeZone(bookingDate, startTime, timezone);
    } catch (e) {
      bookingDateTime = new Date(bookingDate + "T" + startTime);
    }
    var hoursUntil = (bookingDateTime - calculatedAt) / (1000 * 60 * 60);
    console.log("  bookingDateTime:", bookingDateTime.toISOString());
    console.log("  calculatedAt:", calculatedAt.toISOString());
    console.log("  hoursUntil:", hoursUntil.toFixed(2));
    $("#urgency_calculated_at_value").val(calculatedAt.toISOString());
    $("#urgency_booking_starts_at_value").val(bookingDateTime.toISOString());

    if (hoursUntil >= 0 && hoursUntil <= 4) {
      surcharge = 150;
      message = "An urgent booking fee of £150 applies — your appointment is within 4 hours.";
      period = "Within 4 hours";
    } else if (hoursUntil >= 0 && hoursUntil <= 24) {
      surcharge = 100;
      message = "An urgent booking fee of £100 applies — your appointment is within 24 hours.";
      period = "Within 24 hours";
    } else if (hoursUntil >= 0 && hoursUntil <= 48) {
      surcharge = 50;
      message = "An urgent booking fee of £50 applies — your appointment is within 48 hours.";
      period = "Within 48 hours";
    }
  } else {
    console.log("  Skipping calculation — no date or no time selected");
    $("#urgency_calculated_at_value").val("");
    $("#urgency_booking_starts_at_value").val("");
  }

  console.log("  surcharge result: £" + surcharge, "| period:", period || "(none)");

  $("#urgency_surcharge_value").val(surcharge);
  $("#urgency_surcharge_period_value").val(period);

  // Keep the step-3 review summary row in sync
  if (surcharge > 0) {
    if ($("#summary-urgency-row").length === 0) {
      $("#summary-postal-row").after(
        '<div class="summary-row" id="summary-urgency-row">' +
          '<span class="summary-label">Urgent Booking Fee:<small class="text-muted d-block" id="summary-urgency-label"></small></span>' +
          '<span class="summary-value text-warning" id="summary-urgency-price"></span>' +
        '</div>'
      );
    }
    $("#summary-urgency-label").text(period);
    $("#summary-urgency-price").text("+£" + surcharge.toFixed(2));
    console.log("  #summary-urgency-row: INSERTED/UPDATED");
  } else {
    $("#summary-urgency-row").remove();
    console.log("  #summary-urgency-row: REMOVED");
  }

  // Recompute and push the new total so summary total stays correct
  var _base = parseFloat($("#base_service_price").val()) || 0;
  var _qTotal = parseFloat($("#question_price_total_value").val()) || 0;
  var _postal = parseFloat($("#postal_price_value").val()) || 0;
  var _newTotal = _base + _qTotal + _postal + surcharge;
  console.log("  total recomputed: base=" + _base + " + questions=" + _qTotal + " + postal=" + _postal + " + surcharge=" + surcharge + " = " + _newTotal);
  $("#total_price").val(_newTotal.toFixed(2));
  $("#service_price_value").val(_newTotal.toFixed(2));
  $("#summary-total-amount").text(($("#currency_symbol_value").val() || "£") + _newTotal.toFixed(2));

  console.groupEnd();

  var $notice = $("#urgency-surcharge-notice");
  if (surcharge > 0) {
    $("#urgency-surcharge-text").text(message);
    $notice.show();
  } else {
    $notice.hide();
  }

  if (typeof updateQuestionAnswers === "function") {
    updateQuestionAnswers();
  }
}

/*=====================Initialize General Calendar=================*/

function initializeGeneralCalendar(serviceId) {
  $(".request-loader-time").addClass("show");

  $.ajax({
    type: "get",
    url: baseURL + "/services/general-date-time/" + serviceId,
    dataType: "json",
    success: function (response) {
      $(".request-loader-time").removeClass("show");
      let disabledDates = [];
      let disabledWeekdays = [];
      var vendor_id = 13; // Hardcoded vendor ID
      var serviceId = response.serviceId;
      // Use global settings for general availability
      for (var x in response.globalHoliday) {
        let glboalHoliday = response.globalHoliday[x];
        disabledDates.push(glboalHoliday.date);
      }
      for (let x in response.globalWeekend) {
        var value = response.globalWeekend[x];
        disabledWeekdays.push(value.indx);
      }
      if (vendor_id == 0) {
        for (let x in response.adminGlobalWeekend) {
          var value = response.adminGlobalWeekend[x];
          disabledWeekdays.push(value.indx);
        }
      }
      // Always disable Saturday (6) and Sunday (0)
      if (!disabledWeekdays.includes(0)) disabledWeekdays.push(0);
      if (!disabledWeekdays.includes(6)) disabledWeekdays.push(6);

      //convert full date to day
      function convertDayName(dateString) {
        // Parse as local date to avoid UTC timezone shifts (e.g., showing previous day)
        // Expected format: YYYY-MM-DD
        if (
          typeof dateString === "string" &&
          /^\d{4}-\d{2}-\d{2}$/.test(dateString)
        ) {
          var parts = dateString.split("-");
          var year = parseInt(parts[0], 10);
          var monthIndex = parseInt(parts[1], 10) - 1; // 0-based month
          var day = parseInt(parts[2], 10);
          var date = new Date(year, monthIndex, day);
          var dayNames = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
          ];
          var dayName = dayNames[date.getDay()];
          return dayName;
        }
        // Fallback: let Date try to parse
        var d = new Date(dateString);
        var fallbackDayNames = [
          "Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
        ];
        return fallbackDayNames[d.getDay()];
      }

      // Booking Calender
      function onInitBookingCalendar() {
        //weekend days color change
        const weekdays = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        var disabledDayNames = [];
        disabledWeekdays.forEach(function (index) {
          disabledDayNames.push(".pignose-calendar-week-" + weekdays[index]);
        });
        disabledDayNames.forEach(function (selector) {
          $(selector).addClass("weekend");
        });

        //holiday days color change
        disabledDates.forEach(function (date) {
          // Check if any element matches the current date
          var $element = $(
            '.pignose-calendar-unit-disabled[data-date="' + date + '"]',
          );
          if ($element.length > 0) {
            $element.addClass("holiday");
          }
        });

        // Add green highlighting for available dates (days with time slots)
        setTimeout(function () {
          $(".pignose-calendar-unit").each(function () {
            var $this = $(this);
            var dateStr = $this.data("date");
            if (dateStr && !$this.hasClass("pignose-calendar-unit-disabled")) {
              var dayName = convertDayName(dateStr);
              // Check if this day has available slots
              if (
                [
                  "Monday",
                  "Tuesday",
                  "Wednesday",
                  "Thursday",
                  "Friday",
                  "Saturday",
                  "Sunday",
                ].includes(dayName)
              ) {
                $this.addClass("available-date");
                $this.css({
                  "background-color": "#d4edda",
                  border: "2px solid #28a745",
                  "border-radius": "4px",
                });
              }
            }
          });
        }, 100);

        $(".pignose-calendar-top-next, .pignose-calendar-top-prev").on(
          "click",
          function () {
            $("#time_next_step, #max_person_id").addClass("d-none");
            $(".booking-time-slider").empty();
          },
        );
      }
      //initialize calender with staff holiday & vendor membership expiree date
      $(".booking-calendar").pignoseCalendar({
        init: onInitBookingCalendar,
        initialize: false,
        disabledDates: disabledDates,
        disabledWeekdays: disabledWeekdays.map(Number),
        minDate: moment().format("YYYY-MM-DD"),
        select: function (date) {
          let bookingDate = date[0]["_i"];
          var dayName = convertDayName(bookingDate);
          $(".request-loader-time").addClass("show");

          // Reset surcharge, time selection and Next Step when a new date is picked
          applyUrgencySurcharge(bookingDate, "");
          $("#time_next_step").addClass("d-none");
          $("#max_person_id").addClass("d-none");
          $("#service_hour_id").val("");
          $("#start_time").val("");
          $("#end_time").val("");

          // Remove stale time slot listeners before adding fresh ones for this date
          $("body").off("change", "#time_slot_select");
          $("body").off("click", ".time");

          //show general time slots
          $.ajax({
            method: "get",
            url: baseURL + "/services/show-general-hour/" + serviceId,
            data: {
              dayName: dayName,
              bookingDate: bookingDate,
              vendor_id: vendor_id,
              serviceId: serviceId,
            },
            success: function (res) {
              if (res) {
                $(".booking-time-slider").html(res);
                $(".request-loader-time").removeClass("show");
                $(".houre-title-1").addClass("d-none");
              } else {
                console.error("Empty response from server");
              }
            },
            error: function (xhr, status, error) {
              console.error("Error loading time slots:", error);
              console.error("Response:", xhr.responseText);
              $(".request-loader-time").removeClass("show");
            },
          });

          //service hour select - using dropdown
          $("body").on("change", "#time_slot_select", function () {
            let serviceHourId = $(this).val();
            let selectedOption = $(this).find("option:selected");
            let startTime = selectedOption.data("start") || "";
            let endTime = selectedOption.data("end") || "";

            $("#time_next_step").removeClass("d-none");
            $("#max_person_id").removeClass("d-none");
            $("#service_hour_id").val(serviceHourId);
            $("#start_time").val(startTime);
            $("#end_time").val(endTime);

            applyUrgencySurcharge(bookingDate, startTime);
          });

          //service hour select - using clickable buttons (legacy support)
          $("body").on("click", ".time", function () {
            $(".time").removeClass("time_active");
            $(this).addClass("time_active");

            let serviceHourId = $(this).data("id");
            let startTime =
              $(this).data("start") || $(this).data("begin") || "";
            let endTime = $(this).data("end") || "";

            $("#time_next_step").removeClass("d-none");
            $("#max_person_id").removeClass("d-none");
            $("#service_hour_id").val(serviceHourId);
            $("#start_time").val(startTime);
            $("#end_time").val(endTime);

            applyUrgencySurcharge(bookingDate, startTime);
          });

          //this data pass on checkout page
          $("body").on("click", "#time_next_step", function () {
            let max_person = $("#max_person").val();
            $("#booking_date").val(bookingDate);
            $("#max_person").val(max_person);
            // Set default staff ID for general booking
            $("#staff_id").val(11);
          });
        },
      });
    },
    error: function (error) {
      $(".request-loader-time").removeClass("show");
      console.error("Error loading general calendar:", error);
    },
  });
}

/*==========reset service hour value and houre-title after click previous======*/
$("body").on("click", "#time_prev_step", function () {
  $(".booking-time-slider").html("");
  $(".houre-title-1").removeClass("d-none");
  $("#time_next_step").addClass("d-none");
});

/*=====================assing data on payment page for booking=================*/
$(document).on("keydown", function (event) {
  if (event.which == 13 && $("#billing-form input:focus").length > 0) {
    event.preventDefault();
    $(".request-loader-time").addClass("show");
    submitForm(event);
  }
});

function submitForm(event) {
  let url = $("#billing-form").attr("action");
  let method = $("#billing-form").attr("method");
  $.ajax({
    url: url,
    method: method,
    data: $("#billing-form").serialize(),
    success: function (response) {
      $(".request-loader-time").removeClass("show");
      if (response) {
        applyBillingResponse(response);

        $("#serviceHourId").val(response.service_hour_id);
        $("#bookingDate").val(response.booking_date);
        $("#staffId").val(response.staff_id || 11); // Set default staff ID
        $("#userId").val(response.user_id || "0");
        $("#bmax_person").val(response.max_person);
        populateSummary();
        bookingStepper.next();
      }
    },
    error: function (error) {
      $(".em").each(function () {
        $(this).html("");
      });

      for (let x in error.responseJSON.errors) {
        document.getElementById("err_" + x).innerHTML =
          error.responseJSON.errors[x][0];
      }
      $(".request-loader-time").removeClass("show");
    },
  });
}

/*=====================success msg after complete payment=================*/
function openPaymentSuccessModal() {
  var id =
    bookingInfo && bookingInfo.service_id ? bookingInfo.service_id : 13;

  $.ajax({
    type: "get",
    url: baseURL + "/services/payment-success/" + id,
    success: function (response) {
      $("#bookInfoShow").html(response);
      $("#makeBooking").modal("show");

      var stepperElement = document.querySelector("#booking-stepper");
      if (stepperElement) {
        bookingStepper = new Stepper(stepperElement, {
          linear: false,
          animation: true,
        });

        setTimeout(function () {
          bookingStepper.to(6);
        }, 50);
      }

      $(".con_user").val(id);
    },
    error: function (xhr, status, error) {
      console.error("Error loading payment success modal:", status, error);
    },
  });
}

if (complete == "payment_complete") {
  openPaymentSuccessModal();
}

/*=====================forget session data after close modal=================*/
$("#makeBooking").on("hidden.bs.modal", function (e) {
  $(".request-loader-time").removeClass("show");
  $.ajax({
    type: "post",
    url: baseURL + "/services/session/forget",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },
    success: function (response) {},
  });
});

// Fix modal accessibility issue
$("#makeBooking").on("shown.bs.modal", function (e) {
  $(this).attr("aria-hidden", "false");
});

$("#makeBooking").on("hidden.bs.modal", function (e) {
  $(this).attr("aria-hidden", "true");
});

/**
 * show or hide stripe gateway input fields,
 * also show or hide offline gateway informations according to checked payment gateway
 */
$("body").on("change", 'select[name="gateway"]', function () {
  const value = $(this).val();
  handleGatewayChange(value);
});

/*=====================guest checkout form=================*/
$("body").on("click", "#guest_checkout", function () {
  $(".auth-info").addClass("d-none");
  $("#billing-form").removeClass("d-none");
  $("#billingBtn").addClass("d-none");
});

$(document).ready(function () {
  // Listen for changes in the active class
  $(document).on("DOMSubtreeModified", ".step", function handler() {
    // Unbind the event handler to prevent it from being called again
    $(document).off("DOMSubtreeModified", ".step", handler);
    // $('.step').removeClass('active-prev');
    $(".step.active").each(function () {
      $(this).prevAll(".step").addClass("active-prev");
    });
    // Rebind the event handler for future changes
    $(document).on("DOMSubtreeModified", ".step", handler);
  });
});

/*=====================Initialize General Calendar=================*/

function initializeGeneralCalendar(serviceId) {
  $(".request-loader-time").addClass("show");

  $.ajax({
    type: "get",

    url: baseURL + "/services/general-date-time/" + serviceId,

    dataType: "json",

    success: function (response) {
      $(".request-loader-time").removeClass("show");

      let disabledDates = [];

      let disabledWeekdays = [];

      var vendor_id = 13; // Hardcoded vendor ID

      var serviceId = response.serviceId;

      // Use global settings for general availability

      for (var x in response.globalHoliday) {
        let glboalHoliday = response.globalHoliday[x];

        disabledDates.push(glboalHoliday.date);
      }

      for (let x in response.globalWeekend) {
        var value = response.globalWeekend[x];

        disabledWeekdays.push(value.indx);
      }

      if (vendor_id == 0) {
        for (let x in response.adminGlobalWeekend) {
          var value = response.adminGlobalWeekend[x];

          disabledWeekdays.push(value.indx);
        }
      }

      // Always disable Saturday (6) and Sunday (0)
      if (!disabledWeekdays.includes(0)) disabledWeekdays.push(0);
      if (!disabledWeekdays.includes(6)) disabledWeekdays.push(6);

      //convert full date to day

      function convertDayName(dateString) {
        var dayNames = [
          "Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
        ];
        // Parse as local date to avoid UTC timezone shifts (e.g. UK BST = UTC+1
        // would shift "2026-05-20" back to May 19 if parsed as UTC midnight)
        if (typeof dateString === "string" && /^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
          var parts = dateString.split("-");
          var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
          return dayNames[date.getDay()];
        }
        return dayNames[new Date(dateString).getDay()];
      }

      // Booking Calender

      function onInitBookingCalendar() {
        //weekend days color change

        const weekdays = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];

        var disabledDayNames = [];

        disabledWeekdays.forEach(function (index) {
          disabledDayNames.push(".pignose-calendar-week-" + weekdays[index]);
        });

        disabledDayNames.forEach(function (selector) {
          $(selector).addClass("weekend");
        });

        //holiday days color change

        disabledDates.forEach(function (date) {
          // Check if any element matches the current date

          var $element = $(
            '.pignose-calendar-unit-disabled[data-date="' + date + '"]',
          );

          if ($element.length > 0) {
            $element.addClass("holiday");
          }
        });

        $(".pignose-calendar-top-next, .pignose-calendar-top-prev").on(
          "click",
          function () {
            $("#time_next_step, #max_person_id").addClass("d-none");

            $(".booking-time-slider").empty();
          },
        );
      }

      //initialize calender with staff holiday & vendor membership expiree date

      $(".booking-calendar").pignoseCalendar({
        init: onInitBookingCalendar,

        initialize: false,

        disabledDates: disabledDates,

        disabledWeekdays: disabledWeekdays.map(Number),

        minDate: moment().format("YYYY-MM-DD"),

        select: function (date) {
          let bookingDate = date[0]["_i"];

          var dayName = convertDayName(bookingDate);

          $(".request-loader-time").addClass("show");

          // Reset surcharge, time selection and Next Step when a new date is picked
          applyUrgencySurcharge(bookingDate, "");
          $("#time_next_step").addClass("d-none");
          $("#max_person_id").addClass("d-none");
          $("#service_hour_id").val("");
          $("#start_time").val("");
          $("#end_time").val("");

          // Remove stale time slot listeners before adding fresh ones for this date
          $("body").off("change", "#time_slot_select");
          $("body").off("click", ".time");

          //show general time slots

          $.ajax({
            method: "get",

            url: baseURL + "/services/show-general-hour/" + serviceId,

            data: {
              dayName: dayName,

              bookingDate: bookingDate,

              vendor_id: vendor_id,

              serviceId: serviceId,
            },

            success: function (res) {
              if (res) {
                $(".booking-time-slider").html(res);

                $(".request-loader-time").removeClass("show");

                $(".houre-title-1").addClass("d-none");
              } else {
                console.error("Empty response from server");
              }
            },
            error: function (xhr, status, error) {
              console.error("Error loading time slots:", error);
              console.error("Response:", xhr.responseText);
              $(".request-loader-time").removeClass("show");
            },
          });

          //service hour select - using dropdown
          $("body").on("change", "#time_slot_select", function () {
            let serviceHourId = $(this).val();
            let selectedOption = $(this).find("option:selected");
            let startTime = selectedOption.data("start") || "";
            let endTime = selectedOption.data("end") || "";

            $("#time_next_step").removeClass("d-none");
            $("#max_person_id").removeClass("d-none");
            $("#service_hour_id").val(serviceHourId);
            $("#start_time").val(startTime);
            $("#end_time").val(endTime);

            applyUrgencySurcharge(bookingDate, startTime);
          });

          //service hour select - using clickable buttons (legacy support)
          $("body").on("click", ".time", function () {
            $(".time").removeClass("time_active");

            $(this).addClass("time_active");

            let serviceHourId = $(this).data("id");
            let startTime = $(this).data("start") || $(this).data("begin") || "";
            let endTime = $(this).data("end") || "";

            $("#time_next_step").removeClass("d-none");

            $("#max_person_id").removeClass("d-none");

            $("#service_hour_id").val(serviceHourId);
            $("#start_time").val(startTime);
            $("#end_time").val(endTime);

            applyUrgencySurcharge(bookingDate, startTime);
          });

          //this data pass on checkout page

          $("body").on("click", "#time_next_step", function () {
            let max_person = $("#max_person").val();

            $("#booking_date").val(bookingDate);

            $("#max_person").val(max_person);

            // Set default staff ID for general booking

            $("#staff_id").val(11);
          });
        },
      });
    },

    error: function (error) {
      $(".request-loader-time").removeClass("show");

      console.error("Error loading general calendar:", error);
    },
  });
}

/*==========reset service hour value and houre-title after click previous======*/

$("body").on("click", "#time_prev_step", function () {
  $(".booking-time-slider").html("");

  $(".houre-title-1").removeClass("d-none");

  $("#time_next_step").addClass("d-none");
});

/*=====================assing data on payment page for booking=================*/

$(document).on("keydown", function (event) {
  if (event.which == 13 && $("#billing-form input:focus").length > 0) {
    event.preventDefault();

    $(".request-loader-time").addClass("show");

    submitForm(event);
  }
});

function submitForm(event) {
  let url = $("#billing-form").attr("action");

  let method = $("#billing-form").attr("method");

  $.ajax({
    url: url,

    method: method,

    data: $("#billing-form").serialize(),

    success: function (response) {
      $(".request-loader-time").removeClass("show");

      if (response) {
        const fields = [
          "name",
          "phone",
          "email",
          "address",
          "zip_code",
          "country",
        ];

        fields.forEach((field) => $(`#billing_${field}`).val(response[field]));

        $("#serviceHourId").val(response.service_hour_id);

        $("#bookingDate").val(response.booking_date);

        $("#staffId").val(response.staff_id || 11); // Set default staff ID

        $("#userId").val(response.user_id);

        $("#bmax_person").val(response.max_person);

        bookingStepper.next();
      }
    },

    error: function (error) {
      $(".em").each(function () {
        $(this).html("");
      });

      for (let x in error.responseJSON.errors) {
        document.getElementById("err_" + x).innerHTML =
          error.responseJSON.errors[x][0];
      }

      $(".request-loader-time").removeClass("show");
    },
  });
}

/*=====================success msg after complete payment=================*/

/*=====================forget session data after close modal=================*/

$("#makeBooking").on("hidden.bs.modal", function (e) {
  $(".request-loader-time").removeClass("show");

  $.ajax({
    type: "post",

    url: baseURL + "/services/session/forget",

    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },

    success: function (response) {},
  });
});

/**

 * show or hide stripe gateway input fields,

 * also show or hide offline gateway informations according to checked payment gateway

 */

$("body").on("change", 'select[name="gateway"]', function () {
  const value = $(this).val();

  handleGatewayChange(value);
});

/*=====================guest checkout form=================*/

$("body").on("click", "#guest_checkout", function () {
  $(".auth-info").addClass("d-none");

  $("#billing-form").removeClass("d-none");

  $("#billingBtn").addClass("d-none");
});

$(document).ready(function () {
  // Listen for changes in the active class

  $(document).on("DOMSubtreeModified", ".step", function handler() {
    // Unbind the event handler to prevent it from being called again

    $(document).off("DOMSubtreeModified", ".step", handler);

    // $('.step').removeClass('active-prev');

    $(".step.active").each(function () {
      $(this).prevAll(".step").addClass("active-prev");
    });

    // Rebind the event handler for future changes

    $(document).on("DOMSubtreeModified", ".step", handler);
  });
});

/*===================== Payment Method Step - No longer used =================*/
// Removed proceedToSummary - now using submitFormAndGoToSummary in blade template

function populateSummary() {
  var sym = $("#currency_symbol_value").val() || "£";
  var serviceName =
    $("#service_name_value").val() || currentServiceData.name || "Service";
  var baseServicePrice = currentServiceData.price || "N/A";
  var totalPriceValue =
    $("#total_price").val() ||
    $("#service_price_value").val() ||
    baseServicePrice;

  var numericPrice =
    parseFloat(totalPriceValue.toString().replace(/[^0-9.]/g, "")) || 0;
  var servicePrice = sym + numericPrice.toFixed(2);

  var bookingDateRaw =
    $("#booking_date").val() || $("#bookingDate").val() || "-";
  var selectedTimeSlot = $("#time_slot_select option:selected").text() || "-";
  var startTime = $("#start_time").val();
  var endTime = $("#end_time").val();

  var formattedDate = bookingDateRaw;
  if (bookingDateRaw && bookingDateRaw !== "-") {
    var dateObj = new Date(bookingDateRaw);
    if (!isNaN(dateObj.getTime())) {
      var options = { year: "numeric", month: "long", day: "numeric" };
      formattedDate = dateObj.toLocaleDateString("en-US", options);
    }
  }

  var formattedTime = selectedTimeSlot;
  if (
    (!formattedTime ||
      formattedTime.trim() === "-" ||
      formattedTime.trim() === "") &&
    (startTime || endTime)
  ) {
    formattedTime = formatTimeRange(startTime, endTime);
  }

  var bookingDateTime = formattedDate;
  if (formattedTime && formattedTime !== "-") {
    bookingDateTime += " · " + formattedTime;
  }

  var customerName = $("#name").val() || $("#billing_name").val() || "-";
  var customerEmail = $("#email").val() || $("#billing_email").val() || "-";
  var customerPhone = $("#phone").val() || $("#billing_phone").val() || "-";
  var customerAddress =
    $("#address").val() || $("#billing_address").val() || "-";
  var zipCode = $("#zip_code").val() || $("#billing_zip_code").val() || "";
  var country = $("#country").val() || $("#billing_country").val() || "";

  var fullAddress = customerAddress;
  if (zipCode) fullAddress += ", " + zipCode;
  if (country) fullAddress += ", " + country;

  var paymentMethod = "To be selected";
  var gatewayValue = $("#gateway").val();
  if (gatewayValue) {
    var selectedCard = $(
      `.payment-method-card[data-gateway="${gatewayValue}"]`,
    );
    if (selectedCard.length) {
      paymentMethod = selectedCard
        .find(".payment-name, .payment-name-featured")
        .first()
        .text()
        .trim();
    }
  }

  var basePrice = parseFloat($("#base_service_price").val() || "0");
  var postalPrice = parseFloat($("#postal_price_value").val()) || 0;

  var priceBreakdown = [];
  try {
    priceBreakdown = JSON.parse($("#question_price_breakdown_value").val() || "[]");
  } catch (e) {}

  $("#summary-service-name").text(serviceName);
  $("#summary-service-price").text(sym + basePrice.toFixed(2));
  $("#summary-booking-datetime").text(bookingDateTime);
  $("#summary-customer-name").text(customerName);
  $("#summary-customer-email").text(customerEmail);
  $("#summary-customer-phone").text(customerPhone);
  $("#summary-customer-address").text(fullAddress);
  $("#summary-payment-method").text(paymentMethod);

  // Render dynamic add-on rows
  $("#summary-question-breakdown").empty();
  if (priceBreakdown.length > 0) {
    priceBreakdown.forEach(function (item) {
      if (item.price > 0) {
        var valueText = item.answer ? '<small class="text-muted d-block">' + item.answer + "</small>" : "";
        $("#summary-question-breakdown").append(
          '<div class="summary-row">' +
            '<span class="summary-label">' + item.label + ":" + valueText + "</span>" +
            '<span class="summary-value text-success">+' + sym + item.price.toFixed(2) + "</span>" +
          "</div>"
        );
      }
    });
  }

  if (postalPrice > 0) {
    $("#summary-postal-row").show();
    $("#summary-postal-price").text("+" + sym + postalPrice.toFixed(2));
  } else {
    $("#summary-postal-row").hide();
  }

  // Always sync urgency row from the current hidden-field value
  var urgencySurcharge = parseFloat($("#urgency_surcharge_value").val()) || 0;
  console.group("[Urgency] populateSummary — urgency sync");
  console.log("  #urgency_surcharge_value field reads:", $("#urgency_surcharge_value").val(), "→ parsed:", urgencySurcharge);
  if (urgencySurcharge > 0) {
    var urgencyPeriod = $("#urgency_surcharge_period_value").val() || "";
    if ($("#summary-urgency-row").length === 0) {
      $("#summary-postal-row").after(
        '<div class="summary-row" id="summary-urgency-row">' +
          '<span class="summary-label">Urgent Booking Fee:<small class="text-muted d-block" id="summary-urgency-label"></small></span>' +
          '<span class="summary-value text-warning" id="summary-urgency-price"></span>' +
        '</div>'
      );
    }
    $("#summary-urgency-label").text(urgencyPeriod);
    $("#summary-urgency-price").text("+" + sym + urgencySurcharge.toFixed(2));
    console.log("  #summary-urgency-row: INSERTED (surcharge=£" + urgencySurcharge + ")");
  } else {
    $("#summary-urgency-row").remove();
    console.log("  #summary-urgency-row: REMOVED (surcharge=0)");
  }

  // Recompute total to include / exclude surcharge correctly
  var qTotalForSummary = parseFloat($("#question_price_total_value").val()) || 0;
  var postalForSummary = parseFloat($("#postal_price_value").val()) || 0;
  var correctTotal = basePrice + qTotalForSummary + postalForSummary + urgencySurcharge;
  console.log("  total: base=" + basePrice + " + questions=" + qTotalForSummary + " + postal=" + postalForSummary + " + surcharge=" + urgencySurcharge + " = " + correctTotal);
  console.groupEnd();
  $("#summary-total-amount").text(sym + correctTotal.toFixed(2));

  if (typeof updateBillingSummaryCard === "function") {
    updateBillingSummaryCard();
  }
}

function formatTimeRange(startTime, endTime) {
  function formatTime(time) {
    if (!time) return "--";
    var parts = time.split(":");
    if (parts.length < 2) return time;
    var hours = parseInt(parts[0], 10);
    var minutes = parts[1];
    var suffix = hours >= 12 ? "PM" : "AM";
    hours = ((hours + 11) % 12) + 1;
    return hours + ":" + minutes + " " + suffix;
  }

  return formatTime(startTime) + " - " + formatTime(endTime);
}
