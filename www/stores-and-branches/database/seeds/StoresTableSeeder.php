<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder {
	public function run()
	{
		foreach (range('A', 'Z') as $char) {
			DB::table('stores')->insert([
				'name' => $char,
				'status' => 'active',
				'has_branch' => false,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);
		}
	}
}