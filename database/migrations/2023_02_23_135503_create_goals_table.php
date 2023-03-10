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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->decimal('totalIncome', 15, 2)->nullable()->default(null);
            $table->decimal('totalExpense', 15, 2)->nullable()->default(null);
            $table->decimal('profit', 15, 2);
            $table->decimal('year',4,0);
            $table->decimal('profit_calculated', 15, 2)->nullable()->default(null);
            $table->string('created_by');
            $table->string('updated_by')->nullable()->default(null);
            $table->string('deleted_by')->nullable()->default(null);
            $table->unsignedBigInteger('admin_id')->nullable()->default(null);
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');

    }
};