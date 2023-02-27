Zing Matcher
================

This library provides the following:
1. Polyfills
   - [mb_levenshtein](#mb_levenshtein) - Multibyte compatible implementation of the Levenshtein distance algorithm
   - [mb_similar_text](#mb_similar_text) - Multibyte compatible implementation of the similar_text algorithm
2. [Matcher](#matcher) - Gets the best match for a string from a string array
   - [Comparer](#comparer) - The comparison strategy the matcher should use
   - [Tie breaking](#tie-breaking) - The strategy for handling ties for best match
   - [Options](#options) - Other matching options

---

Usage
-----

mb_levenshtein
--------------

The core `levenshtein` function operates on each byte of a string, therefore it will return
confusing results when it encounters multibyte characters.

`mb_levenshtein` is an implementation of the Levenshtein distance algorithm that supports
multibyte characters. This implementation is a port of the core C function into PHP
with multibyte character support added. It has the same arguments as the core function.

https://www.php.net/manual/en/function.levenshtein.php

https://github.com/php/php-src/blob/php-8.1.10/ext/standard/levenshtein.c

```php
echo levenshtein('notre', 'nitre');
// output: 1

echo levenshtein('notre', 'nôtre');
// output 2

echo mb_levenshtein('notre', 'nôtre');
// output 1
```

Matcher
-------

The matcher will pick the best matching string from
an array and return a result object that contains the matched string from
the haystack and a similarity score to the needle. Please note that the
similarity scores are not normalized in any way and their values are
determined by the comparison algorithm used.

```php
use Zing\Matcher\Matcher;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinComparer;

$haystack = [
    'fox',
    'cat',
    'dog',
];

$matcher = new Matcher(
    new LevensteinComparer()
);

$result = $matcher->match('bat', $haystack);

echo $result->getString();
//output: cat

echo $result->getSimilarityResult()->getScore();
//output 1
```

### Comparer
The matcher requires that an object implementing SimilarityComparerInterface is provided as the first argument.
The comparer is used to compare the needle to each element of the haystack to obtain a SimilarityResult.
The comparer is also used to compare SimilarityResults to determine which is better.
For example with Levenshtein a lower number is better, but with SimilarText a higher percentage is better.

This library includes the following comparers:

`LevensteinComparer` - uses the Levenshtein distance algorithm.

https://www.php.net/manual/en/function.levenshtein.php

`SimlilarTextComparer` - uses PHP's similar_text function.

https://www.php.net/manual/en/function.similar-text.php


### Tie Breaking
By default, if a result is considered an exact match, it will be returned immediately. Tie breaking is only necessary when there
is no exact match and multiple elements in the haystack tie with the best similarity. This behavior can be changed
by enabling the `ALLOW_EXACT_MATCH_TIES` flag.

The default tie breaking rule is to return the element in the haystack that appeared earliest.

```php
$haystack = [
    'cat',
    'fat',
    'rat',
];

$matcher = new Matcher(
    new LevensteinComparer()
);

$result = $matcher->match('bat', $haystack);

echo $result->getString();
//output: cat
```

The tiebreaker is an invokable class that implements the TieBreakerInterface. It receives the needle and an array
of the Results that tied for best match. This library comes with other tiebreakers
that can be used. You can create your own by implementing the TieBreakerInterface and returning
the winner of the tie.

```php
use Zing\Matcher\TieBreaker\LastMatch;
use Zing\Matcher\TieBreaker\ThrowException;

$haystack = [
    'cat',
    'fat',
    'rat',
];

$matcher = new Matcher(
    new LevensteinComparer(),
    [],
    new LastMatch()
);

$result = $matcher->match('bat', $haystack);

echo $result->getString();
//output: rat

$matcher = new Matcher(
    new LevensteinComparer(),
    [],
    new ThrowException()
);
//This will throw a TieException which will include the tied results.
$result = $matcher->match('bat', $haystack);
```


### Matcher Options

Enable case-insensitive support by adding the `StrToLowerPreprocessor` preprocessor. This option tells the matcher to convert the needle and haystack
to lower case before passing it to the comparer. The unmodified strings will be passed to the tiebreakers and returned in the results.

```php
$haystack = [
    'fox',
    'cat',
    'dog',
];

$matcher = new Matcher(
    new LevensteinComparer(),
    [new StrToLowerPreprocessor()]
);

$result = $matcher->match('CAT', $haystack);

echo $result->getString();
//output: cat

echo $result->getSimilarityResult()->getScore();
//output 0
//0 means it was considered to be an exact match
```

Use the comparer's multibyte implementation by adding the `MULTIBYTE` flag to the comparer's options parameter.

```php
$haystack = [
    'notre',
    'nôtre',
];

$matcher = new Matcher(
    new LevensteinComparer(SimilarityComparerInterface::MULTIBYTE)
);

$result = $matcher->match('nôtre', $haystack);

echo $result->getString();
//output: nôtre

echo $result->getSimilarityResult()->getScore();
//output 0
```

You can combine the `CASE_INSENSITIVE` and `MULTIBYTE` flags too.

```php
$haystack = [
    'notre',
    'nôtre',
];

$matcher = new Matcher(
    new LevensteinComparer(SimilarityComparerInterface::MULTIBYTE),
    [new StrToLowerPreprocessor()]
);


$result = $matcher->match('NÔTRE', $haystack);

echo $result->getString();
//output: nôtre

echo $result->getSimilarityResult()->getScore();
//output 0
```




