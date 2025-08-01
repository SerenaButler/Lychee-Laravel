<?php

/**
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2017-2018 Tobias Reich
 * Copyright (c) 2018-2025 LycheeOrg.
 */

namespace App\Actions\User;

use App\Exceptions\ConflictingPropertyException;
use App\Exceptions\InvalidPropertyException;
use App\Exceptions\ModelDBException;
use App\Models\Configs;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create
{
	/**
	 * @throws InvalidPropertyException
	 * @throws ModelDBException
	 */
	public function do(
		string $username,
		string $password,
		?string $email = null,
		bool $may_upload = false,
		bool $may_edit_own_settings = false,
		bool $may_administrate = false,
		?int $quota_kb = null,
		?string $note = null,
	): User {
		if (User::query()->where('username', '=', $username)->count() !== 0) {
			throw new ConflictingPropertyException('Username already exists');
		}
		if ($quota_kb === 0) {
			$default = Configs::getValueAsInt('default_user_quota');
			$quota_kb = $default === 0 ? null : $default;
		}
		$user = new User();
		$user->may_upload = $may_upload;
		$user->may_edit_own_settings = $may_edit_own_settings;
		$user->may_administrate = $may_administrate;
		$user->username = $username;
		$user->email = $email;
		$user->password = Hash::make($password);
		$user->quota_kb = $quota_kb;
		$user->note = $note;
		$user->save();

		return $user;
	}
}
