<?php

/**
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2017-2018 Tobias Reich
 * Copyright (c) 2018-2025 LycheeOrg.
 */

/**
 * We don't care for unhandled exceptions in tests.
 * It is the nature of a test to throw an exception.
 * Without this suppression we had 100+ Linter warning in this file which
 * don't help anything.
 *
 * @noinspection PhpDocMissingThrowsInspection
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Tests\Feature_v2\Photo;

use Tests\Feature_v2\Base\BaseApiWithDataTest;

class PhotoMoveTest extends BaseApiWithDataTest
{
	public function testMovePhotoUnauthorizedForbidden(): void
	{
		$response = $this->postJson('Photo::move', []);
		$this->assertUnprocessable($response);

		$response = $this->postJson('Photo::move', [
			'from_id' => $this->album1->id,
			'photo_ids' => [$this->photo1->id],
			'album_id' => $this->album2->id,
		]);
		$this->assertUnauthorized($response);

		$response = $this->actingAs($this->userNoUpload)->postJson('Photo::move', [
			'from_id' => $this->album1->id,
			'photo_ids' => [$this->photo1->id],
			'album_id' => $this->album2->id,
		]);
		$this->assertForbidden($response);
	}

	public function testMovePhotoAuthorizedOwner(): void
	{
		$response = $this->actingAs($this->userMayUpload1)->postJson('Photo::move', []);
		$this->assertUnprocessable($response);

		$response = $this->actingAs($this->userMayUpload1)->postJson('Photo::move', [
			'from_id' => $this->album1->id,
			'photo_ids' => [$this->photo1->id],
			'album_id' => $this->album2->id,
		]);
		$this->assertForbidden($response);

		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => $this->album1->id]);
		$this->assertOk($response);
		$response->assertJsonCount(2, 'resource.photos');

		$response = $this->actingAs($this->userMayUpload1)->postJson('Photo::move', [
			'from_id' => $this->album1->id,
			'photo_ids' => [$this->photo1->id],
			'album_id' => $this->subAlbum1->id,
		]);
		$this->assertNoContent($response);

		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => $this->album1->id]);
		$this->assertOk($response);
		$response->assertJsonCount(1, 'resource.photos');

		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => $this->subAlbum1->id]);
		$this->assertOk($response);
		$response->assertJson([
			'config' => [],
			'resource' => [
				'photos' => [
					[
						'id' => $this->photo1->id,
					],
				],
			],
		]);
	}

	public function testMovePhotoAuthorizedOwnerUnosorted(): void
	{
		$this->clearCachedSmartAlbums();
		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => 'unsorted']);
		$this->assertOk($response);
		$response->assertJsonCount(1, 'resource.photos');
		$response->assertDontSee($this->photo1->id);

		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => $this->album1->id]);
		$this->assertOk($response);
		$response->assertJsonCount(2, 'resource.photos');
		$response->assertSee($this->photo1->id);

		$response = $this->actingAs($this->userMayUpload1)->postJson('Photo::move', [
			'from_id' => $this->album1->id,
			'photo_ids' => [$this->photo1->id],
			'album_id' => null,
		]);
		$this->assertNoContent($response);

		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => $this->album1->id]);
		$this->assertOk($response);
		$response->assertJsonCount(1, 'resource.photos');
		$response->assertDontSee($this->photo1->id);

		$this->clearCachedSmartAlbums();
		$response = $this->actingAs($this->userMayUpload1)->getJsonWithData('Album', ['album_id' => 'unsorted']);
		$this->assertOk($response);
		$response->assertJsonCount(2, 'resource.photos');
		$response->assertSee($this->photo1->id);

		$response = $this->actingAs($this->userMayUpload1)->postJson('Photo::move', [
			'from_id' => 'unsorted',
			'photo_ids' => [$this->photo1->id],
			'album_id' => $this->album1->id,
		]);
		$this->assertNoContent($response);
	}
}