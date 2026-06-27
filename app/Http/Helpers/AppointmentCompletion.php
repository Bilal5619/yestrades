<?php

namespace App\Http\Helpers;

use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceReview;
use Illuminate\Support\Str;

class AppointmentCompletion
{
  public static function complete(ServiceBooking $booking, string $actorType, ?int $actorId): void
  {
    $booking->update([
      'order_status' => 'completed',
      'completed_at' => now(),
      'completed_by_type' => $actorType,
      'completed_by_id' => $actorId,
    ]);
  }

  public static function notifyCustomer(ServiceBooking $booking, ?string $certificatePath = null): void
  {
    if (empty($booking->customer_email)) {
      return;
    }

    $serviceName = optional($booking->serviceContent->first())->name ?: 'Service appointment';
    $review = self::reviewInvite($booking);
    $reviewLink = $review && $review->token && empty($review->reviewed_at)
      ? '<p><a href="' . e(route('frontend.verified_review.show', $review->token)) . '">Leave a verified review for this completed job</a></p>'
      : '';
    $details = $booking->user_id
      ? '<p><a href="' . e(route('user.appointment.details', $booking->id)) . '">Review the appointment details and completion images</a></p>'
      : '';

    $mailData = [
      'recipient' => $booking->customer_email,
      'subject' => 'Your service appointment has been completed',
      'body' => '<p>Hello ' . e($booking->customer_name) . ',</p>'
        . '<p>Your appointment for <strong>' . e($serviceName) . '</strong> has been marked as completed.</p>'
        . '<p>The job certificate is attached to this email.</p>'
        . $reviewLink
        . $details
        . '<p>Booking number: <strong>' . e($booking->order_number) . '</strong></p>',
    ];

    if ($certificatePath && file_exists($certificatePath)) {
      $mailData['certificate'] = $certificatePath;
    }

    BasicMailer::sendMail($mailData);
  }

  private static function reviewInvite(ServiceBooking $booking): ?ServiceReview
  {
    if ($booking->order_status !== 'completed') {
      return null;
    }

    $review = ServiceReview::where('booking_id', $booking->id)->first();

    if ($review) {
      return $review;
    }

    return ServiceReview::create([
      'booking_id' => $booking->id,
      'user_id' => $booking->user_id,
      'customer_email' => $booking->customer_email,
      'vendor_id' => $booking->vendor_id,
      'service_id' => $booking->service_id,
      'status' => 'pending',
      'token' => Str::random(64),
    ]);
  }
}
