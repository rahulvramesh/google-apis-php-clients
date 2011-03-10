<?php

/*
 * This file is part of the Google APIs PHP Clients package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Google\Api\Data\Parser;

use Google\Api\Data\Parser;
use Google\Api\Data\Parser\Exception;
use Google\Api\Data\Parser\Translate\Translation as TranslationParser;

use Google\Api\Data\Translate as TranslateData;

/**
 * Translate parses raw data into a formatted Translate Data object.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Translate implements Parser
{
    /**
     * Parses raw data into a formatted Translate Data object.
     *
     * @param \stdClass $data The data to parse.
     *
     * @return TranslateData
     *
     * @throws Exception When a parse error occurs.
     */
    public function parse(\stdClass $data)
    {
        $formattedData = array();

        if(!(isset($data->data) && $data->data instanceof \stdClass))
        {
            throw new Exception('Missing/invalid response data.');
        }

        if(!(isset($data->data->translations) && is_array($data->data->translations)))
        {
            throw new Exception('Missing/invalid translations data.');
        }

        $formattedData['translations'] = $this->parseTranslations($data->data->translations);

        return new TranslateData($formattedData);
    }

    /**
     * Parses the "translations" part of the data.
     *
     * @param array $translations
     *
     * @return array
     *
     * @throws Exception When a parse error occurs.
     */
    protected function parseTranslations(array $translations)
    {
        $translationObjects = array();
        $translationParser = new TranslationParser();

        foreach($translations as $translation)
        {
            if(!($translation instanceof \stdClass))
            {
                throw new Exception('Invalid translation format.');
            }

            array_push($translationObjects, $translationParser->parse($translation));
        }

        return $translationObjects;
    }
}