<?php

namespace App\Actions\Diagnostics\Pipes\Checks;

use App\Contracts\DiagnosticPipe;
use App\Models\Configs;
use LycheeVerify\Verify;

/**
 * Check whether or not it is possible to update this installation.
 */
class SupporterCheck implements DiagnosticPipe
{
	private Verify $verify;

	/**
	 * @param Verify $verify
	 */
	public function __construct(
		Verify $verify,
	) {
		$this->verify = $verify;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(array &$data, \Closure $next): array
	{
		if (Configs::getValueAsBool('disable_se_call_for_actions')) {
			return $next($data);
		}

		if (!$this->verify->is_supporter()) {
			// @codeCoverageIgnoreStart
			$data[] = 'Info: Have you considered supporting Lychee? :)';
			// @codeCoverageIgnoreEnd
		}

		return $next($data);
	}
}