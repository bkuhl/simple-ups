<?php namespace SimpleUPS\Track;

/**
 * The status of a tracked shipment
 * @since 1.0
 */
class Status extends \SimpleUPS\Model
{

    public static
        $CODE_BILLING_INFO_RECEIVED = 1,
        $CODE_IN_TRANSIT = 2,
        $CODE_EXCEPTION = 3,
        $CODE_DELIVERED_ORIGIN_CFS = 4,
        $CODE_DELIVERED_DESTINATION_CFS = 5,
        $CODE_WAREHOUSING = 6,
        $CODE_OUT_FOR_DELIVERY = 7,
        $CODE_DELIVERED = 11,
        $CODE_NOT_AVAILABLE1 = 111,
        $CODE_NOT_AVAILABLE2 = 222;

    private
        /* @var integer $code */
        $code,

        /* @var string $description */
        $description;

    /**
     * @internal
     *
     * @param integer $code
     *
     * @return Status
     */
    public function setCode($code)
    {
        $this->code = (int)$code;
        return $this;
    }

    /**
     * Get the code for this status
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @internal
     *
     * @param string $description
     *
     * @return Status
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    /**
     * Textual representation of the status
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Status
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $status = new Status();
        $status->setIsResponse();
        $status
            ->setCode($xml->Code)
            ->setDescription($xml->Description);

        return $status;
    }
}