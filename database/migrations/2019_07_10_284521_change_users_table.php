

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tel')->nullable()->after('email');
            $table->index('tel');
            $table->string('address')->nullable()->after('tel');
            $table->bigInteger('role_id')->unsigned()->after('address')->default('1'); // Зарегистрированный пользователь id из /config/admin.php user_roles config('admin.user_roles')[1]
            $table->foreign('role_id')->references('id')->on('roles');
            $table->string('status', 32)->default(config('admin.user_statuses')[0])->after('password');
            $table->text('note')->nullable()->after('status');
            $table->string('ip', 32)->nullable()->after('note');
            $table->string('img')->default(config('admin.imgUserDefault'))->after('ip');
            $table->enum('accept', ['0', '1'])->default('0')->after('ip');
            //$table->string('name', 50)->change(); // Если нужно изменить поле, для этого необходимо установить зависимость composer require doctrine/dbal
            //$table->renameColumn('from', 'to'); // Если нужно переименовать колонку
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tel'); // Удалить колонку
            $table->dropIndex('tel'); // Удалить индекс
            $table->dropColumn('address');
            $table->dropColumn('role_id');
            $table->dropForeign('role_id'); // Удалить связующий ключ
            $table->dropColumn('status');
            $table->dropColumn('note');
            $table->dropColumn('ip');
            $table->dropColumn('img');
            $table->dropColumn('accept');
            //$table->string('name')->change(); // Если поле было изменено, то его откатить к первоначальному
        });
    }
}
