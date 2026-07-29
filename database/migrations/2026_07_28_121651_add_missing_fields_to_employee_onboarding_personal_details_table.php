<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table(
            'employee_onboarding_personal_details',
            function (Blueprint $table) {
                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'full_name'
                )) {
                    $table->string('full_name')
                        ->after('employee_onboarding_id');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'date_of_birth'
                )) {
                    $table->date('date_of_birth')
                        ->nullable()
                        ->after('full_name');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'personal_email'
                )) {
                    $table->string('personal_email')
                        ->nullable()
                        ->after('date_of_birth');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'phone'
                )) {
                    $table->string('phone', 50)
                        ->nullable()
                        ->after('personal_email');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'home_address'
                )) {
                    $table->text('home_address')
                        ->nullable()
                        ->after('phone');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'emergency_contact_name'
                )) {
                    $table->string('emergency_contact_name')
                        ->nullable()
                        ->after('home_address');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'emergency_contact_relationship'
                )) {
                    $table->string(
                        'emergency_contact_relationship'
                    )
                        ->nullable()
                        ->after('emergency_contact_name');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'emergency_contact_phone'
                )) {
                    $table->string(
                        'emergency_contact_phone',
                        50
                    )
                        ->nullable()
                        ->after(
                            'emergency_contact_relationship'
                        );
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'national_insurance_number'
                )) {
                    $table->string(
                        'national_insurance_number',
                        50
                    )
                        ->nullable()
                        ->after('emergency_contact_phone');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'bank_name'
                )) {
                    $table->string('bank_name')
                        ->nullable()
                        ->after('national_insurance_number');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'account_holder_name'
                )) {
                    $table->string('account_holder_name')
                        ->nullable()
                        ->after('bank_name');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'sort_code'
                )) {
                    $table->string('sort_code', 20)
                        ->nullable()
                        ->after('account_holder_name');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'account_number'
                )) {
                    $table->string('account_number', 30)
                        ->nullable()
                        ->after('sort_code');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'details_verified'
                )) {
                    $table->boolean('details_verified')
                        ->default(false)
                        ->after('account_number');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'verified_by'
                )) {
                    $table->unsignedBigInteger('verified_by')
                        ->nullable()
                        ->after('details_verified');

                    $table->foreign(
                        'verified_by',
                        'eopd_verified_by_fk'
                    )
                        ->references('id')
                        ->on('company_users')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'verified_at'
                )) {
                    $table->timestamp('verified_at')
                        ->nullable()
                        ->after('verified_by');
                }

                if (!Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'notes'
                )) {
                    $table->text('notes')
                        ->nullable()
                        ->after('verified_at');
                }
            }
        );
    }

    public function down()
    {
        Schema::table(
            'employee_onboarding_personal_details',
            function (Blueprint $table) {
                if (Schema::hasColumn(
                    'employee_onboarding_personal_details',
                    'verified_by'
                )) {
                    $table->dropForeign(
                        'eopd_verified_by_fk'
                    );
                }

                $columns = [
                    'full_name',
                    'date_of_birth',
                    'personal_email',
                    'phone',
                    'home_address',
                    'emergency_contact_name',
                    'emergency_contact_relationship',
                    'emergency_contact_phone',
                    'national_insurance_number',
                    'bank_name',
                    'account_holder_name',
                    'sort_code',
                    'account_number',
                    'details_verified',
                    'verified_by',
                    'verified_at',
                    'notes',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn(
                        'employee_onboarding_personal_details',
                        $column
                    )) {
                        $table->dropColumn($column);
                    }
                }
            }
        );
    }
};