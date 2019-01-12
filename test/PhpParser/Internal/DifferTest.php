<?php declare(strict_types=1);

namespace PhpParser\Internal;

class DifferTest extends \PHPUnit\Framework\TestCase
{
    private function formatDiffString(array $diff) {
        $diffStr = '';
        foreach ($diff as $diffElem) {
            switch ($diffElem->type) {
                case DiffElem::TYPE_KEEP:
                    $diffStr .= $diffElem->old;
                    break;
                case DiffElem::TYPE_REMOVE:
                    $diffStr .= '-' . $diffElem->old;
                    break;
                case DiffElem::TYPE_ADD:
                    $diffStr .= '+' . $diffElem->new;
                    break;
                case DiffElem::TYPE_REPLACE:
                    $diffStr .= '/' . $diffElem->old . $diffElem->new;
                    break;
                default:
                    assert(false);
                    break;
            }
        }
        return $diffStr;
    }

    /** @dataProvider provideTestDiff */
    public function testDiff($oldStr, $newStr, $expectedDiffStr) {
        $differ = new Differ(function($a, $b) { return $a === $b; });
        $diff = $differ->diff(str_split($oldStr), str_split($newStr));
        $this->assertSame($expectedDiffStr, $this->formatDiffString($diff));
    }

    public function provideTestDiff(): \Iterator
    {
        yield ['abc', 'abc', 'abc'];
        yield ['abc', 'abcdef', 'abc+d+e+f'];
        yield ['abcdef', 'abc', 'abc-d-e-f'];
        yield ['abcdef', 'abcxyzdef', 'abc+x+y+zdef'];
        yield ['axyzb', 'ab', 'a-x-y-zb'];
        yield ['abcdef', 'abxyef', 'ab-c-d+x+yef'];
        yield ['abcdef', 'cdefab', '-a-bcdef+a+b'];
    }

    /** @dataProvider provideTestDiffWithReplacements */
    public function testDiffWithReplacements($oldStr, $newStr, $expectedDiffStr) {
        $differ = new Differ(function($a, $b) { return $a === $b; });
        $diff = $differ->diffWithReplacements(str_split($oldStr), str_split($newStr));
        $this->assertSame($expectedDiffStr, $this->formatDiffString($diff));
    }

    public function provideTestDiffWithReplacements(): \Iterator
    {
        yield ['abcde', 'axyze', 'a/bx/cy/dze'];
        yield ['abcde', 'xbcdy', '/axbcd/ey'];
        yield ['abcde', 'axye', 'a-b-c-d+x+ye'];
        yield ['abcde', 'axyzue', 'a-b-c-d+x+y+z+ue'];
    }
}
