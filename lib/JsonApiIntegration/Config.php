<?php

namespace Argonauts\JsonApiIntegration;

/**
 * Diese Klasse enthält lediglich Konstanten, die in diesem Namespace
 * benötigt werden.
 */
class Config
{
    /** Config file name w/o extension */
    const NAME = 'json-api-integration';

    /** Config key for schema list */
    const SCHEMAS = 'schemas';

    /** Config key for URL prefix that will be added to all document links which have $treatAsHref flag set to false */
    const JSON_URL_PREFIX = 'urlPrefix';
}
