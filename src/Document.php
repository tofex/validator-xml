<?php

namespace Tofex\Validator\Xml;

use DOMDocument;

/**
 * @author      Stefan Jaroschek
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Document
    extends AbstractValidator
{
    /**
     * Returns true if and only if $value is valid against the specified schema
     *
     * @param DOMDocument $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        return $this->validateDocument($value);
    }
}
