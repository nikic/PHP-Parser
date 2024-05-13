<?php declare(strict_types=1);

namespace PhpParser\Internal;

class DifferTest extends \PHPUnit\Framework\TestCase {
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
    public function testDiff($oldStr, $newStr, $expectedDiffStr): void {
        $differ = new Differ(function ($a, $b) {
            return $a === $b;
        });
        $diff = $differ->diff(str_split($oldStr), str_split($newStr));
        $this->assertSame($expectedDiffStr, $this->formatDiffString($diff));
    }

    public static function provideTestDiff() {
        return [
            ['abc', 'abc', 'abc'],
            ['abc', 'abcdef', 'abc+d+e+f'],
            ['abcdef', 'abc', 'abc-d-e-f'],
            ['abcdef', 'abcxyzdef', 'abc+x+y+zdef'],
            ['axyzb', 'ab', 'a-x-y-zb'],
            ['abcdef', 'abxyef', 'ab-c-d+x+yef'],
            ['abcdef', 'cdefab', '-a-bcdef+a+b'],
        ];
    }

    /** @dataProvider provideTestDiffWithReplacements */
    public function testDiffWithReplacements($oldStr, $newStr, $expectedDiffStr): void {
        $differ = new Differ(function ($a, $b) {
            return $a === $b;
        });
        $diff = $differ->diffWithReplacements(str_split($oldStr), str_split($newStr));
        $this->assertSame($expectedDiffStr, $this->formatDiffString($diff));
    }

    public static function provideTestDiffWithReplacements() {
        return [
            ['abcde', 'axyze', 'a/bx/cy/dze'],
            ['abcde', 'xbcdy', '/axbcd/ey'],
            ['abcde', 'axye', 'a-b-c-d+x+ye'],
            ['abcde', 'axyzue', 'a-b-c-d+x+y+z+ue'],
        ];
    }

    public function testNonContiguousIndices(): void {
        $differ = new Differ(function ($a, $b) {
            return $a === $b;
        });
        $diff = $differ->diff([0 => 'a', 2 => 'b'], [0 => 'a', 3 => 'b']);
        $this->assertEquals([
            new DiffElem(DiffElem::TYPE_KEEP, 'a', 'a'),
            new DiffElem(DiffElem::TYPE_KEEP, 'b', 'b'),
        ], $diff);
    }
}
