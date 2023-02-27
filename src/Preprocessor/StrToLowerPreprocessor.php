<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Preprocessor;

class StrToLowerPreprocessor implements PreprocessorInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function __invoke(string $string): string
    {
        return mb_strtolower($string);
    }
}
