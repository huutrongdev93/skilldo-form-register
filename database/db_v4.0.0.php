<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

return new class () extends Migration {

    public function up(): void
    {
        if(schema()->hasColumn('generate_form_register', 'taxonomy')) {
            schema()->table('generate_form_register', function (Blueprint $table) {
                $table->dropColumn('taxonomy');
                $table->dropColumn('taxonomy_icon');
                $table->dropColumn('taxonomy_config');
            });
        }
        if(!schema()->hasTable('form_register_result')) {
            schema()->create('form_register_result', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 100)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('phone', 50)->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('message')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('status')->default(0);
                $table->string('form_key', 50)->collate('utf8mb4_unicode_ci');
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
    }
};