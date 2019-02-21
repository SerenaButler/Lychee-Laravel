<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{


	public function scopeMenu($query)
	{
		return $query->where('in_menu', true)->where('enabled',true)->orderBy('order','ASC');
	}

}
