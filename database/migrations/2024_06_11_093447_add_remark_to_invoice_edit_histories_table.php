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
        Schema::table('invoice_edit_histories', function (Blueprint $table) {
            //
            $table->string("remark")->after('total_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_edit_histories', function (Blueprint $table) {
            //
            Schema::dropColumns('invoice_edit_histories','remark');
        });
    }
};
