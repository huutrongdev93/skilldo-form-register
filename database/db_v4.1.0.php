<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

return new class () extends Migration {

    public function up(): void
    {
        if(!schema()->hasColumn('generate_form_register', 'send_telegram'))
        {
            schema()->table('generate_form_register', function (Blueprint $table) {
                $table->tinyInteger('send_telegram')->default(0);
            });
        }
    }

    public function down(): void
    {
    }
};