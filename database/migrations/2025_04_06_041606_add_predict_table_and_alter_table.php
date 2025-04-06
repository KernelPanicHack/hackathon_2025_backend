<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('predict_expenses', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->float('money');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->float('cushion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predict_expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('predict_expenses');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cushion');
        });
    }
};
