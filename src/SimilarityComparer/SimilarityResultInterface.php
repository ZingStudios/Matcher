<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer;

interface SimilarityResultInterface
{
    public function getScore(): int;

    public function isExactMatch(): bool;
}