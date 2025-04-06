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
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn('ref_no');
            $table->string('ref_no');

            $table->dropColumn('cost');
            $table->double('cost');

            $table->dropColumn('remaining_balance');
            $table->double('remaining_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn('ref_no');
            $table->bigInteger('ref_no')->unsigned();

            $table->dropColumn('cost');
            $table->integer('cost');

            $table->dropColumn('remaining_balance');
            $table->integer('remaining_balance');
        });
    }
};
