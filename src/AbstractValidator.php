<?php

namespace Tofex\Validator\Xml;

use DOMDocument;
use Laminas\Validator\Exception\InvalidArgumentException;
use LibXMLError;
use Traversable;

/**
 * @author      Stefan Jaroschek
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class AbstractValidator
    extends \Laminas\Validator\AbstractValidator
{
    const INVALID = 'xmlInvalid';

    /** @var array */
    protected $_messageTemplates;

    /** @var array */
    protected $_messageVariables = array(
        'error' => 'error'
    );

    /** @var string */
    protected $error = '';

    /** @var string */
    private $schema = '';

    /**
     * @param string            $schema
     * @param array|Traversable $options
     */
    public function __construct($schema, $options = null)
    {
        parent::__construct($options);

        $this->_messageTemplates =
            array(static::INVALID => "Xml is not valid because of the following error(s): %error%");

        $this->setSchema($schema);
    }

    /**
     * Returns the schema file path
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Sets the schema file path
     *
     * @param string $schema
     *
     * @return AbstractValidator
     *
     * @throws InvalidArgumentException
     */
    public function setSchema($schema)
    {
        if ( ! is_file($schema)) {
            $message = sprintf("Schema file '%s' does not exist.", $schema);
            throw new InvalidArgumentException($message);
        }

        $this->schema = $schema;

        return $this;
    }

    /**
     * Returns true if and only if $value is valid against the specified schema
     *
     * @param DOMDocument $document
     *
     * @return boolean
     */
    protected function validateDocument(DOMDocument $document)
    {
        $document->formatOutput = true;

        $xml = $document->saveXML();

        $document = new DOMDocument();

        $document->loadXML($xml);

        libxml_use_internal_errors(true);

        $this->setValue($xml);

        if ( ! $document->schemaValidate($this->schema)) {
            $this->error = implode("\n", $this->getLibXmlErrors());

            $this->error(static::INVALID);

            return false;
        }

        return true;
    }

    /**
     * Get all libxml errors
     *
     * @return array
     */
    private function getLibXmlErrors()
    {
        $result = array();

        /** @var libXMLError $error */
        foreach (libxml_get_errors() as $error) {
            $result[] = "Error {$error->code} in {$error->file} (Line:{$error->line}): " . trim($error->message);
        }

        libxml_clear_errors();

        return $result;
    }
}
