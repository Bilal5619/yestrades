<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeCompanyJobsAndCreateClients extends Migration
{
    public function up()
    {
        Schema::create('company_clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('client_type')->default('company');

            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();

            $table->string('payment_terms')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('company_client_properties', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');

            $table->string('property_name')->nullable();

            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->text('access_notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')
                ->references('id')
                ->on('company_clients')
                ->cascadeOnDelete();
        });

        Schema::table('employee_jobs', function (Blueprint $table) {

            $table->string('job_reference')
                ->nullable()
                ->unique()
                ->after('id');

            $table->unsignedBigInteger('client_id')
                ->nullable()
                ->after('employee_id');

            $table->unsignedBigInteger('client_property_id')
                ->nullable()
                ->after('client_id');

            $table->string('customer_email')
                ->nullable()
                ->after('customer_phone');

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            $table->boolean('payment_required')
                ->default(false);

            $table->decimal('agreed_amount', 10, 2)
                ->nullable();

            $table->string('payment_method')
                ->nullable();

            $table->string('payment_status')
                ->default('not_required');

            $table->string('invoice_number')
                ->nullable();

            $table->timestamp('invoice_sent_at')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Certificate
            |--------------------------------------------------------------------------
            */

            $table->boolean('certificate_required')
                ->default(false);

            $table->string('certificate_type')
                ->nullable();

            $table->string('certificate_status')
                ->default('not_required');

            /*
            |--------------------------------------------------------------------------
            | Completion
            |--------------------------------------------------------------------------
            */

            $table->text('work_summary')
                ->nullable();

            $table->text('completion_notes')
                ->nullable();

            $table->string('admin_status')
                ->default('action_required');

            $table->timestamp('closed_at')
                ->nullable();

            $table->foreign('client_id')
                ->references('id')
                ->on('company_clients')
                ->nullOnDelete();

            $table->foreign('client_property_id')
                ->references('id')
                ->on('company_client_properties')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['client_property_id']);

            $table->dropColumn([
                'job_reference',
                'client_id',
                'client_property_id',
                'customer_email',
                'payment_required',
                'agreed_amount',
                'payment_method',
                'payment_status',
                'invoice_number',
                'invoice_sent_at',
                'paid_at',
                'certificate_required',
                'certificate_type',
                'certificate_status',
                'work_summary',
                'completion_notes',
                'admin_status',
                'closed_at',
            ]);
        });

        Schema::dropIfExists('company_client_properties');
        Schema::dropIfExists('company_clients');
    }
}