<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\Preprocessor;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\Preprocessor\StrToLowerPreprocessor;

/**
 * @covers \Zing\Matcher\Preprocessor\StrToLowerPreprocessor
 */
class StrToLowerPreprocessorTest extends TestCase
{
    /**
     * Verifies that we get 0 for an exact match.
     * @return void
     */
    public function testStrToLowerPreprocesssor()
    {
        //Verify that we produce an exact match result for strings
        //with multibyte characters.
        $this->assertEquals('carröt', (new StrToLowerPreprocessor)('CARRÖT'));
    }
}
