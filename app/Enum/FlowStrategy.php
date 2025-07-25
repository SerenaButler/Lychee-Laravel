<?php

/**
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2017-2018 Tobias Reich
 * Copyright (c) 2018-2025 LycheeOrg.
 */

namespace App\Enum;

enum FlowStrategy: string
{
	case AUTO = 'auto';
	case OPT_IN = 'opt-in';
}
