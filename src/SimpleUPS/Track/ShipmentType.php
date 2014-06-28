<?php namespace SimpleUPS\Track;

/**
 * @internal
 */
class ShipmentType extends \SimpleUPS\Model
{
    const
        TYPE_SMALL_PACKAGE = 1,
        TYPE_FREIGHT = 2,
        TYPE_MAIL_INNOVATION = 3;

    private
        /* @var string $code */
        $code,

        /* @var string $description */
        $description;

    /**
     * @param string $code
     *
     * @return ShipmentType
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
     * @param string $description
     *
     * @return ShipmentType
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
     * @param \SimpleXMLElement $xml
     *
     * @return ShipmentType
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shipmentType = new ShipmentType();
        $shipmentType->setIsResponse();
        $shipmentType
            ->setCode($xml->Code)
            ->setDescription($xml->Description);

        return $shipmentType;
    }
}