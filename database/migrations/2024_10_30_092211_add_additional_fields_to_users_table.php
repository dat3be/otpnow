<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_userid')->nullable()->unique()->comment('Telegram User ID');
            $table->string('telegram_username')->nullable()->comment('Telegram Username');
            $table->decimal('balance', 15, 2)->default(0)->comment('Số dư tài khoản');
            $table->string('aff_code')->nullable()->unique()->comment('Mã affiliate');
            $table->decimal('aff_balance', 15, 2)->default(0)->comment('Số dư affiliate');
            $table->string('ref_by')->nullable()->comment('ID của người giới thiệu');
            $table->string('phone_num')->nullable()->unique()->comment('Số điện thoại');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_userid',
                'telegram_username',
                'balance',
                'aff_code',
                'aff_balance',
                'ref_by',
                'phone_num',
            ]);
        });
    }
};
