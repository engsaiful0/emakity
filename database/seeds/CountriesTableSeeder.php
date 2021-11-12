<?php

use Illuminate\Database\Seeder;
use PragmaRX\Countries\Package\Countries;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('countries')->delete();
        $countries = Countries::all()->pluck('name.common','postal');
        $data=[];
        foreach($countries as $key => $value){
            $data[]=array (
                'name' => $value,
                'code' => strtolower($key),
                'active' => 0,
                'currency_id' => 1,
                'created_at' => '2019-10-22 15:50:48',
                'updated_at' => '2019-10-22 15:50:48',
            );
        }
        //dd($data);
        \DB::table('countries')->insert($data);
    }
}
