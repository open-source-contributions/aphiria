<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/aphiria/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Net\Http\ContentNegotiation;

use Aphiria\Net\Http\Headers\AcceptLanguageHeaderValue;
use Aphiria\Net\Http\Headers\IHeaderValueWithQualityScore;

/**
 * Defines the language matcher
 */
final class LanguageMatcher
{
    /**
     * Gets the best language match between a list of supported languages and Accept-Language headers
     * This uses "lookup" matching per RFC-4647 section 3.4
     *
     * @param array $supportedLanguages The list of supported languages
     * @param AcceptLanguageHeaderValue[] $languageHeaders The list of language headers to rank
     * @return string|null The best language match if one existed, otherwise null
     * @link https://tools.ietf.org/html/rfc4647#section-3.4
     */
    public function getBestLanguageMatch(array $supportedLanguages, array $languageHeaders): ?string
    {
        usort($languageHeaders, [$this, 'compareAcceptLanguageHeaders']);
        $rankedLanguageHeaders = array_filter($languageHeaders, [$this, 'filterZeroScores']);
        $rankedLanguageHeaderValues = $this->getLanguageValuesFromHeaders($rankedLanguageHeaders);

        foreach ($rankedLanguageHeaderValues as $language) {
            $languageParts = explode('-', $language);

            // Progressively truncate this language tag and try to match a supported language
            do {
                foreach ($supportedLanguages as $supportedLanguage) {
                    if ($language === '*' || implode('-', $languageParts) === $supportedLanguage) {
                        return $supportedLanguage;
                    }
                }

                array_pop($languageParts);
            } while (count($languageParts) > 0);
        }

        return null;
    }

    /**
     * Compares two languages and returns which of them is "lower" than the other
     *
     * @param AcceptLanguageHeaderValue $a The first language header to compare
     * @param AcceptLanguageHeaderValue $b The second language header to compare
     * @return int -1 if $a is lower than $b, 0 if they're even, or 1 if $a is higher than $b
     */
    private function compareAcceptLanguageHeaders(AcceptLanguageHeaderValue $a, AcceptLanguageHeaderValue $b): int
    {
        $aQuality = $a->getQuality();
        $bQuality = $b->getQuality();

        if ($aQuality < $bQuality) {
            return 1;
        }

        if ($aQuality > $bQuality) {
            return -1;
        }

        $aValue = $a->getLanguage();
        $bValue = $b->getLanguage();

        if ($aValue === '*') {
            if ($bValue === '*') {
                return 0;
            }

            return 1;
        }

        if ($bValue === '*') {
            return -1;
        }

        return 0;
    }

    /**
     * Filters out any header values with a zero quality score
     *
     * @param IHeaderValueWithQualityScore $header The value to check
     * @return bool True if we should keep the value, otherwise false
     */
    private function filterZeroScores(IHeaderValueWithQualityScore $header): bool
    {
        return $header->getQuality() > 0;
    }

    /**
     * Gets the language values from a list of headers
     *
     * @param AcceptLanguageHeaderValue[] $headers The list of language headers
     * @return array The list of language values from the headers
     */
    private function getLanguageValuesFromHeaders(array $headers): array
    {
        $languages = [];

        foreach ($headers as $header) {
            $languages[] = $header->getLanguage();
        }

        return $languages;
    }
}