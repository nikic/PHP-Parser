<?php

namespace PhpParser;

class Foo
{

	public function doFoo(): void
	{
		echo []; // @phpstan-ignore-line

		// @phpstan-ignore-next-line
		echo [];

		echo []; /* @phpstan-ignore-line */

		/* @phpstan-ignore-next-line */
		echo [];

		echo []; /** @phpstan-ignore-line */

		/** @phpstan-ignore-next-line */
		echo [];

		$this->multiCall(
			1,
			2, // @phpstan-ignore-next-line
			3
		);

		/**
		 * @phpstan-ignore-next-line
		 */
		echo [];

		/** @phpstan-ignore-next-line why we do this */
		echo [];

		$this->multiCall(
			1,
			2, // @phpstan-ignore-next-line why we do this
			3
		);

		/**
		 * @phpstan-ignore-next-line
		 * hahaha
		 */
		echo [];

		$this->multiCall(
			1,
			2, /* @phpstan-ignore-next-line */
			3
		);

		$this->multiCall(
			1,
			2, // @phpstan-ignore-next-line
			3 // something something
		);

		/** @phpstan-ignore-next-line */
		echo [];

		/** @phpstan-ignore-next-line */
		echo [];
	}

}
