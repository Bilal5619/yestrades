<?php

namespace App\Mail\Careers;

use App\Models\Careers\CareerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateStatusMail extends Mailable
{
  use Queueable, SerializesModels;

  public CareerApplication $application;
  public string $mailSubject;
  public string $mailBody;

  public function __construct(CareerApplication $application, string $mailSubject, string $mailBody)
  {
    $this->application = $application;
    $this->mailSubject = $mailSubject;
    $this->mailBody = $mailBody;
  }

  public function build()
  {
    return $this->subject($this->mailSubject)->view('emails.careers.candidate-status');
  }
}
