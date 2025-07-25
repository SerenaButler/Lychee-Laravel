<?php

/**
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2017-2018 Tobias Reich
 * Copyright (c) 2018-2025 LycheeOrg.
 */

namespace App\Http\Requests\Album;

use App\Constants\PhotoAlbum as PA;
use App\Contracts\Http\Requests\HasAlbum;
use App\Contracts\Http\Requests\HasCompactBoolean;
use App\Contracts\Http\Requests\HasPhoto;
use App\Contracts\Http\Requests\RequestAttribute;
use App\Contracts\Models\AbstractAlbum;
use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\Traits\HasAlbumTrait;
use App\Http\Requests\Traits\HasCompactBooleanTrait;
use App\Http\Requests\Traits\HasPhotoTrait;
use App\Models\Album;
use App\Models\Photo;
use App\Policies\AlbumPolicy;
use App\Rules\RandomIDRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SetAsHeaderRequest extends BaseApiRequest implements HasAlbum, HasPhoto, HasCompactBoolean
{
	use HasAlbumTrait;
	use HasPhotoTrait;
	use HasCompactBooleanTrait;

	private array $album_ids = [];

	public function authorize(): bool
	{
		$is_photo_in_album = $this->photo !== null && DB::table(PA::PHOTO_ALBUM)
			->where(PA::ALBUM_ID, '=', $this->album->id)
			->where(PA::PHOTO_ID, '=', $this->photo->id)
			->count() > 0;

		return Gate::check(AlbumPolicy::CAN_EDIT, [AbstractAlbum::class, $this->album]) &&
		($this->is_compact || $is_photo_in_album);
	}

	/**
	 * {@inheritDoc}
	 */
	public function rules(): array
	{
		return [
			RequestAttribute::ALBUM_ID_ATTRIBUTE => ['required', new RandomIDRule(false)],
			RequestAttribute::HEADER_ID_ATTRIBUTE => ['required', new RandomIDRule(true)],
			RequestAttribute::IS_COMPACT_ATTRIBUTE => ['required', 'boolean'],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processValidatedValues(array $values, array $files): void
	{
		$album = $this->album_factory->findBaseAlbumOrFail(
			$values[RequestAttribute::ALBUM_ID_ATTRIBUTE]
		);

		if (!$album instanceof Album) {
			throw ValidationException::withMessages([RequestAttribute::ALBUM_ID_ATTRIBUTE => 'album type not supported.']);
		}

		$this->album = $album;
		$this->is_compact = static::toBoolean($values[RequestAttribute::IS_COMPACT_ATTRIBUTE]);

		if ($this->is_compact) {
			return;
		}

		/** @var string $photo_id */
		$photo_id = $values[RequestAttribute::HEADER_ID_ATTRIBUTE];
		$this->photo = Photo::query()->findOrFail($photo_id);
	}
}