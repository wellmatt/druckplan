<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser = \App\User::create(Array(
                'name' => "APICaller",
                'email' => "api@limburg-live.com",
                'password' => '$2y$10$1i2vYV4dFMW5yV2hhUB.oOzuCpoBlEEodkGix2EJKQwtF5E/U./0O') // AppLL2016
        );

        DB::table('oauth_clients')->insert(
            [
                'id' => 'GXvOWazQ3lA6YSaFji',
                'secret' => 'GXvOWazQ3lA.6/YSaFji',
                'name' => 'NewsAPI'
            ]
        );

    }
}
