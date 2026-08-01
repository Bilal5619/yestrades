<?php

namespace App\Mail\Company;

use App\Models\Company\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeActivationMail extends Mailable
{
  use Queueable, SerializesModels;

  public Employee $employee;
  public string $activationUrl;

  public function __construct(Employee $employee, string $activationUrl)
  {
    $this->employee = $employee;
    $this->activationUrl = $activationUrl;
  }

  public function build()
  {
    return $this->subject('Activate your Yes Trades staff account')
      ->view('emails.company.employee-activation');
  }
}
