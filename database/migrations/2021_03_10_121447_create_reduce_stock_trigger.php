<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateReduceStockTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $create_trigger_query =
            "CREATE TRIGGER reduce_stock_movie BEFORE INSERT ON sales
            FOR EACH ROW
            BEGIN
               UPDATE movies set stock = stock - new.quantity
                WHERE id = new.movie_id;
            END;
            ";
        DB::unprepared($create_trigger_query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `reduce_stock_movie`');
    }
}
