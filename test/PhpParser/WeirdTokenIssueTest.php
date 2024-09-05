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
		$newContent = '';
		foreach ($tokens as $token) {
			$newContent .= $token->text;
		}

		$this->assertStringEqualsFile(__DIR__ . '/ignore-line-1.php', $newContent);
	}

	public function testUnsetLastToken(): void
	{
		$factory = new ParserFactory();
		$parser = $factory->createForNewestSupportedVersion();
		$parser->parse(file_get_contents(__DIR__ . '/ignore-line-1.php'));

		$tokens = $parser->getTokens();
		unset($tokens[count($tokens) - 1]);
		$newContent = '';
		foreach ($tokens as $token) {
			$newContent .= $token->text;
		}

		$this->assertStringEqualsFile(__DIR__ . '/ignore-line-1.php', $newContent);
	}
}
