<?php namespace SimpleUPS\Track\SmallPackage;

/**
 * A status type of an activity
 * @since 1.0
 */
class StatusType extends \SimpleUPS\Model
{

    private
        $CODE_IN_TRANSIT = 'L',
        $CODE_DELIVERED = 'D',
        $CODE_EXCEPTION = 'X',
        $CODE_PICKUP = 'P',
        $CODE_MANIFEST_PICKUP = 'M';

    private
        /* @var string $code */
        $code,

        /* @var string $description */
        $description;

    /**
     * @internal
     *
     * @param string $code
     *
     * @return Status
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isInTransit()
    {
        return $this->getCode() == $this->CODE_IN_TRANSIT;
    }

    /**
     * @return bool
     */
    public function isDelivered()
    {
        return $this->getCode() == $this->CODE_DELIVERED;
    }

    /**
     * @return bool
     */
    public function isException()
    {
        return $this->getCode() == $this->CODE_EXCEPTION;
    }

    /**
     * @return bool
     */
    public function isPickup()
    {
        return $this->getCode() == $this->CODE_PICKUP;
    }

    /**
     * @return bool
     */
    public function isManifestPickup()
    {
        return $this->getCode() == $this->CODE_MANIFEST_PICKUP;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return StatusType
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $statusType = new StatusType();
        $statusType->setIsResponse();

        if (isset($xml->Code)) {
            $statusType->setCode((string)$xml->Code);
        }

        if (isset($xml->Description)) {
            $statusType->setDescription((string)$xml->Description);
        }

        return $statusType;
    }
}