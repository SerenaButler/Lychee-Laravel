<?php

namespace App\Legacy\V1\Requests\Traits;

use App\Models\Album;

trait HasParentAlbumTrait
{
	protected ?Album $parentAlbum = null;

	/**
	 * @return Album|null
	 */
	public function parentAlbum(): ?Album
	{
		return $this->parentAlbum;
	}
}