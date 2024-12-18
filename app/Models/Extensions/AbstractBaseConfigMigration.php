<?php

namespace App\Models\Extensions;

use Illuminate\Database\Migrations\Migration;

abstract class AbstractBaseConfigMigration extends Migration
{
	public const BOOL = '0|1';
	public const POSITIVE = 'positive';
	public const INT = 'int';
	public const STRING = 'string';

	/**
	 * @return array<int,array{key:string,value:string,is_secret:bool,cat:string,type_range:string,description:string}>
	 */
	abstract public function getConfigs(): array;

	/**
	 * Run the migrations.
	 */
	abstract public function up(): void;

	/**
	 * Reverse the migrations.
	 */
	abstract public function down(): void;
}
