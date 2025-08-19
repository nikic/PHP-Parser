<?php declare(strict_types=1);

namespace PhpParser\Node\Expr\Cast;

require __DIR__ . '/Float_.php';

if (false) {
    /**
     * For classmap-authoritative support.
     *
     * @deprecated use \PhpParser\Node\Expr\Cast\Float_ instead.
     */
    class Double extends Float_ {
        public function getType(): string {
            return 'Expr_Cast_Double';
        }
    }
}
