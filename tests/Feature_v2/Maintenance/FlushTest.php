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

namespace Tests\Feature_v2\Maintenance;

use Tests\Feature_v2\Base\BaseApiWithDataTest;

class FlushTest extends BaseApiWithDataTest
{
	public function testGuest(): void
	{
		$response = $this->postJson('Maintenance::flushCache');
		$this->assertUnauthorized($response);
	}

	public function testUser(): void
	{
		$response = $this->actingAs($this->userLocked)->postJson('Maintenance::flushCache');
		$this->assertForbidden($response);
	}

	public function testAdmin(): void
	{
		$response = $this->actingAs($this->admin)->postJson('Maintenance::flushCache');
		$this->assertNoContent($response);
	}
}