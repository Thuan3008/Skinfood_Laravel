<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AdminHiddenRecords', function (Blueprint $table) {
            $table->string('ResourceTable', 64);
            $table->unsignedInteger('RecordId');
            $table->unsignedInteger('HiddenBy')->nullable();
            $table->dateTime('HiddenAt');
            $table->primary(['ResourceTable', 'RecordId']);
        });
    }
    public function down(): void { Schema::dropIfExists('AdminHiddenRecords'); }
};
