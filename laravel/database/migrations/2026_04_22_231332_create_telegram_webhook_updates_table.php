<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_webhook_updates', function (Blueprint $table) {
            $table->id();
            $table->string('update_key')->unique();
            $table->unsignedBigInteger('update_id')->nullable()->index();
            $table->string('payload_hash', 64);
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_webhook_updates');
    }
};
