<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Exception;

use Throwable;
use Zing\Matcher\MatchResultInterface;

class TieException extends RuntimeException
{
    /**
     * @var MatchResultInterface[]
     */
    private array $tiedResults;

    /**
     * @param MatchResultInterface[] $tiedResults
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $tiedResults,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->tiedResults = $tiedResults;
    }

    /**
     * @return MatchResultInterface[]
     */
    public function getTiedResults(): array
    {
        return $this->tiedResults;
    }
}
