<?php declare(strict_types=1);

namespace PhpParser;

use PHPUnit\Framework\TestCase;
use function file_get_contents;

class WeirdTokenIssueTest extends TestCase
{
	public function testIssue(): void
	{
		$factory = new ParserFactory();
		$parser = $factory->createForNewestSupportedVersion();
		$parser->parse(file_get_contents(__DIR__ . '/ignore-line-1.php'));

		$tokens = $parser->getTokens();
		$lastToken = $tokens[count($tokens) - 1];
		var_dump(ord($lastToken->text));
		var_dump(bin2hex($lastToken->text));
		var_dump(strlen($lastToken->text));
	}
}
