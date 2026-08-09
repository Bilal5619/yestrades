<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AutoSeoWebhookController;
use App\Http\Controllers\Api\EmployeeAuthController;
use App\Http\Controllers\Api\EmployeeDashboardController;
use App\Http\Controllers\Api\EmployeeMobileController;
use App\Http\Controllers\Api\VendorAuthController;
use App\Http\Controllers\Api\VendorMobileController;
use App\Models\Question;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::prefix('v1/vendor')->group(function () {
  Route::get('/service-categories', [VendorAuthController::class, 'serviceCategories']);
  Route::post('/signup', [VendorAuthController::class, 'signup']);
  Route::post('/login', [VendorAuthController::class, 'login']);

  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [VendorMobileController::class, 'me']);
    Route::post('/logout', [VendorMobileController::class, 'logout']);
    Route::get('/dashboard', [VendorMobileController::class, 'dashboard']);
    Route::post('/profile', [VendorMobileController::class, 'updateProfile']);
    Route::post('/change-password', [VendorMobileController::class, 'changePassword']);
    Route::get('/account-setup', [VendorMobileController::class, 'accountSetup']);
    Route::post('/business-type', [VendorMobileController::class, 'updateBusinessType']);
    Route::post('/registration-numbers', [VendorMobileController::class, 'storeRegistrationNumber']);
    Route::delete('/registration-numbers/{id}', [VendorMobileController::class, 'deleteRegistrationNumber']);
    Route::post('/documents', [VendorMobileController::class, 'storeDocument']);
    Route::post('/submit-verification', [VendorMobileController::class, 'submitVerification']);

    Route::get('/appointments', [VendorMobileController::class, 'appointments']);
    Route::get('/appointments/{id}', [VendorMobileController::class, 'appointment']);
    Route::post('/appointments/{id}/status', [VendorMobileController::class, 'updateAppointmentStatus']);
    Route::post('/appointments/{id}/complete', [VendorMobileController::class, 'completeAppointment']);
    Route::post('/appointments/{id}/invoice', [VendorMobileController::class, 'storeInvoice']);

    Route::get('/staff', [VendorMobileController::class, 'staff']);
    Route::post('/appointments/staff-assign', [VendorMobileController::class, 'assignStaff']);

    Route::get('/available-jobs', [VendorMobileController::class, 'availableJobs']);
    Route::post('/available-jobs/{id}/claim', [VendorMobileController::class, 'claimAvailableJob']);
  });
});

Route::prefix('v1/employee')->group(function () {
  Route::post('/activate', [EmployeeAuthController::class, 'activate']);
  Route::post('/login', [EmployeeAuthController::class, 'login']);

  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [EmployeeAuthController::class, 'me']);
    Route::post('/logout', [EmployeeAuthController::class, 'logout']);
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index']);
Route::post('/push-token', [EmployeeMobileController::class, 'storePushToken']);

    Route::get('/profile', [EmployeeMobileController::class, 'profile']);
    Route::put('/profile', [EmployeeMobileController::class, 'updateProfile']);
    Route::get('/holidays', [EmployeeMobileController::class, 'holidays']);
    Route::post('/holidays', [EmployeeMobileController::class, 'requestHoliday']);
    Route::post('/holidays/{holiday}/cancel', [EmployeeMobileController::class, 'cancelHoliday']);
    Route::get('/training', [EmployeeMobileController::class, 'training']);
    Route::get('/training/{record}/certificate', [EmployeeMobileController::class, 'downloadTrainingCertificate']);
    Route::get('/contract', [EmployeeMobileController::class, 'contract']);
    Route::get('/contracts/{id}/download', [EmployeeMobileController::class, 'downloadContract']);
    Route::get('/payslips', [EmployeeMobileController::class, 'payslips']);
    Route::get('/payslips/{payslip}', [EmployeeMobileController::class, 'payslip']);
    Route::get('/payslips/{payslip}/download', [EmployeeMobileController::class, 'downloadPayslip']);
    Route::get('/announcements', [EmployeeMobileController::class, 'announcements']);
    Route::post('/announcements/{announcement}/read', [EmployeeMobileController::class, 'markAnnouncementRead']);
    Route::get('/notifications', [EmployeeMobileController::class, 'notifications']);
    Route::get('/jobs', [EmployeeMobileController::class, 'jobs']);
    Route::get('/jobs/{job}', [EmployeeMobileController::class, 'job']);
    Route::post('/jobs/{job}/status', [EmployeeMobileController::class, 'updateJobStatus']);
    Route::post('/jobs/{job}/start', [EmployeeMobileController::class, 'startJob']);
    Route::post('/jobs/{job}/finish', [EmployeeMobileController::class, 'finishJob']);
    Route::get('/shifts', [EmployeeMobileController::class, 'shifts']);
    Route::post('/attendance/clock', [EmployeeMobileController::class, 'clock']);
  });
});

Route::get('/autoseo/webhook', [AutoSeoWebhookController::class, 'health']);
Route::post('/autoseo/webhook', [AutoSeoWebhookController::class, 'store']);

// Fetch questions by service_id
Route::get('/questions/by-service/{service_id}', function ($service_id) {
  $questions = Question::where('service_id', $service_id)
    ->with('options')
    ->get();

  return response()->json([
    'success' => true,
    'questions' => $questions,
    'count' => $questions->count()
  ]);
});
