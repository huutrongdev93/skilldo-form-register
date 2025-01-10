<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

return new class () extends Migration {

    public function up(): void
    {
        if(!schema()->hasTable('generate_form_register')) {
            schema()->create('generate_form_register', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('is_live')->default(1);
                $table->tinyInteger('send_email')->default(0);
                $table->tinyInteger('send_telegram')->default(0);
                $table->tinyInteger('is_redirect')->default(0);
                $table->string('name', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('key', 100);
                $table->text('field')->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('url_redirect', 255);
                $table->text('email_template')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
            });
        }
        if(!schema()->hasTable('form_register_result')) {
            schema()->create('form_register_result', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 100)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('phone', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('message')->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('form_key', 50)->collate('utf8mb4_unicode_ci');
                $table->integer('status')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
                $table->index('id');
                $table->index('form_key');
            });
        }
        if(!schema()->hasTable('form_register_result_metadata')) {
            schema()->create('form_register_result_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('meta_key', 100)->collate('utf8mb4_unicode_ci')->nullable();
                $table->longText('meta_value')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
                $table->index('object_id');
                $table->index('meta_key');
            });
        }
    }

    public function down(): void
    {
        schema()->drop('generate_form_register');
        schema()->drop('form_register_result');
        schema()->drop('form_register_result_metadata');
    }
};