<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE customers MODIFY email VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY phone VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY country VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY city VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY address VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE customers SET email = '' WHERE email IS NULL");
        DB::statement("UPDATE customers SET phone = '' WHERE phone IS NULL");
        DB::statement("UPDATE customers SET country = '' WHERE country IS NULL");
        DB::statement("UPDATE customers SET city = '' WHERE city IS NULL");
        DB::statement("UPDATE customers SET address = '' WHERE address IS NULL");

        DB::statement('ALTER TABLE customers MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY phone VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY country VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY city VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY address VARCHAR(255) NOT NULL');
    }
};
