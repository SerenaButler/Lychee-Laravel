<?php

/**
 * We don't care for unhandled exceptions in tests.
 * It is the nature of a test to throw an exception.
 * Without this suppression we had 100+ Linter warning in this file which
 * don't help anything.
 *
 * @noinspection PhpDocMissingThrowsInspection
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Tests\Unit\Redirections;

use App\Http\Redirections\ToAdminSetter;
use Tests\AbstractTestCase;

class ToAdminSetterTest extends AbstractTestCase
{
	public function testRedirection(): void
	{
		$response = ToAdminSetter::go();
		$this->assertEquals(307, $response->getStatusCode());
	}
}