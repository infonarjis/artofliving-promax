<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->decimal('basic_salary', 10, 2)->default(0)->after('gender');

            $table->string('blood_group', 10)->nullable()->after('marital_status');
            $table->text('address')->nullable()->after('blood_group');

            $table->unsignedBigInteger('country_id')->default(0)->after('address');
            $table->unsignedBigInteger('state_id')->default(0)->after('country_id');
            $table->unsignedBigInteger('city')->default(0)->after('state_id');

            $table->string('telephone_no')->nullable()->after('city');

            $table->string('identity_document')->nullable()->after('telephone_no');
            $table->string('identity_number')->nullable()->after('identity_document');
            $table->string('id_number')->nullable()->after('identity_number');

            $table->string('employee_type')->nullable()->after('id_number');

            $table->date('joining_date')->nullable()->after('employee_type');

            $table->string('designation')->nullable()->after('joining_date');
            $table->string('department')->nullable()->after('designation');

            $table->string('pan_no')->nullable()->after('department');
            $table->string('nationality')->nullable()->after('pan_no');

            $table->string('bank_name')->nullable()->after('nationality');
            $table->string('bank_account_no')->nullable()->after('bank_name');
            $table->string('bank_ifsc_code')->nullable()->after('bank_account_no');

            $table->string('pf_account_no')->nullable()->after('bank_ifsc_code');
            $table->string('uan_no')->nullable()->after('pf_account_no');

            $table->string('commission_applicable', 10)
                ->default('No')
                ->after('uan_no');

            $table->double('commission_value')
                ->default(0)
                ->after('commission_applicable');

            $table->double('total_sick_leave')
                ->default(0)
                ->after('commission_value');

            $table->double('monthly_applied_sick_leave')
                ->default(0)
                ->after('total_sick_leave');

            $table->double('total_paid_leave')
                ->default(0)
                ->after('monthly_applied_sick_leave');

            $table->double('monthly_applied_paid_leave')
                ->default(0)
                ->after('total_paid_leave');

            $table->double('total_sick_leave_used')
                ->default(0)
                ->after('monthly_applied_paid_leave');

            $table->double('total_paid_leave_used')
                ->default(0)
                ->after('total_sick_leave_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'basic_salary',
                'blood_group',
                'address',
                'country_id',
                'state_id',
                'city',
                'telephone_no',
                'identity_document',
                'identity_number',
                'id_number',
                'employee_type',
                'joining_date',
                'designation',
                'department',
                'pan_no',
                'nationality',
                'bank_name',
                'bank_account_no',
                'bank_ifsc_code',
                'pf_account_no',
                'uan_no',
                'commission_applicable',
                'commission_value',
                'total_sick_leave',
                'monthly_applied_sick_leave',
                'total_paid_leave',
                'monthly_applied_paid_leave',
                'total_sick_leave_used',
                'total_paid_leave_used',
            ]);
        });
    }
};
