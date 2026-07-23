<?php

namespace App\Mail\Careers;

use App\Models\Careers\CareerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable
{
  use Queueable, SerializesModels;

  public CareerApplication $application;

  public function __construct(CareerApplication $application)
  {
    $this->application = $application;
  }

  public function build()
  {
    return $this->subject("Your interview has been scheduled - Yes Trades")
      ->view('emails.careers.interview-scheduled');
  }
}
