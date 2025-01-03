<?php

namespace App\Contracts\Http\Requests;

use App\Models\Photo;
use Illuminate\Support\Collection;

interface HasPhotos
{
	/**
	 * @return Collection<int,Photo>
	 */
	public function photos(): Collection;
}
