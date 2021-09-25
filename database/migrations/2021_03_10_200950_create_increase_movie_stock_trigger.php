<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateIncreaseMovieStockTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $create_trigger_query =
            "CREATE TRIGGER increase_stock_movie BEFORE UPDATE ON rents
            FOR EACH ROW
            BEGIN
               UPDATE movies set stock = stock + new.quantity
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
        DB::unprepared("DROP TRIGGER `increase_stock_movie`");
    }
}
