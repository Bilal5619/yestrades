<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeAuthController extends Controller
{
  public function activate(Request $request): JsonResponse
  {
    $data = $request->validate([
      'token' => ['required', 'string'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $employee = Employee::query()
      ->where('activation_token', $data['token'])
      ->whereNotNull('activation_token_expires_at')
      ->where('activation_token_expires_at', '>=', now())
      ->first();

    if (!$employee) {
      return response()->json([
        'success' => false,
        'message' => 'This activation link is invalid or has expired.',
      ], 422);
    }

    $employee->activateWithPassword($data['password']);

    $token = $employee->createToken('employee-mobile')->plainTextToken;

    return response()->json([
      'success' => true,
      'message' => 'Account activated successfully.',
      'data' => [
        'token_type' => 'Bearer',
        'access_token' => $token,
        'employee' => $this->employeePayload($employee),
      ],
    ]);
  }

  public function login(Request $request): JsonResponse
  {
    $data = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required', 'string'],
    ]);

    $employee = Employee::query()
      ->where('work_email', $data['email'])
      ->orWhere('personal_email', $data['email'])
      ->first();

    if (!$employee || !$employee->activated_at) {
      return response()->json([
        'success' => false,
        'message' => 'Account not found or not yet activated.',
      ], 422);
    }

    if (!$employee->password || !Hash::check($data['password'], $employee->password)) {
      return response()->json([
        'success' => false,
        'message' => 'The email or password is incorrect.',
      ], 422);
    }

    if (!in_array($employee->employment_status, ['active', 'on_leave', 'notice_period'], true)) {
      return response()->json([
        'success' => false,
        'message' => 'Your account is not currently active. Please contact HR.',
      ], 403);
    }

    $token = $employee->createToken('employee-mobile')->plainTextToken;

    return response()->json([
      'success' => true,
      'message' => 'Login successful.',
      'data' => [
        'token_type' => 'Bearer',
        'access_token' => $token,
        'employee' => $this->employeePayload($employee),
      ],
    ]);
  }

  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'success' => true,
      'message' => 'Logged out successfully.',
    ]);
  }

  public function me(Request $request): JsonResponse
  {
    return response()->json([
      'success' => true,
      'data' => [
        'employee' => $this->employeePayload($request->user()),
      ],
    ]);
  }

  private function employeePayload(Employee $employee): array
  {
    return [
      'id' => $employee->id,
      'employee_number' => $employee->employee_number,
      'staff_id' => $employee->staff_id,
      'is_field_engineer' => $employee->isFieldEngineer(),
      'can_use_manual_attendance' => $employee->canUseManualAttendance(),
      'full_name' => $employee->full_name,
      'first_name' => $employee->first_name,
      'last_name' => $employee->last_name,
      'work_email' => $employee->work_email,
      'personal_email' => $employee->personal_email,
      'phone' => $employee->phone,
      'job_title' => $employee->job_title,
      'department' => $employee->department,
      'employment_type' => $employee->employment_type,
      'employment_status' => $employee->employment_status,
      'work_location' => $employee->work_location,
      'start_date' => optional($employee->start_date)->toDateString(),
      'profile_photo' => $employee->profile_photo,
    ];
  }
}
