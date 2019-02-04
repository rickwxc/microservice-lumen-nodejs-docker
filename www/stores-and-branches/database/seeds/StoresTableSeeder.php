<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder {
	public function run()
	{
		foreach (range('A', 'Z') as $char) {
			DB::table('stores')->insert([
				'name' => $char,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);
		}
		foreach (range('A', 'D') as $idx => $char) {
			DB::table('stores')->insert([
				'name' => $char.'1',
				'parent_store_id' => $idx + 1,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);
		}

	}
}
