<?php namespace SimpleUPS\Track;

/**
 * Reference number for signifying PO #’s, Invoice #’s, etc
 * @since 1.0
 */
class ReferenceNumber extends \SimpleUPS\Model
{
    private
        /* @var integer $code */
        $code,

        /* @var string $value */
        $value;

    /**
     * @internal
     *
     * @param integer $code
     *
     * @return ReferenceNumber
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * Reference number type code, for signifying PO #’s, Invoice #’s, etc
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @internal
     *
     * @param string $value
     *
     * @return ReferenceNumber
     */
    public function setValue($value)
    {
        $this->value = (string)$value;
        return $this;
    }

    /**
     * Customer supplied reference number
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return ReferenceNumber
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $referenceNumber = new ReferenceNumber();
        $referenceNumber->setIsResponse();
        $referenceNumber
            ->setCode($xml->Code)
            ->setValue($xml->Value);

        return $referenceNumber;
    }
}
