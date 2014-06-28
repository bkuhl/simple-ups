<?php namespace SimpleUPS\Track\SmallPackage;

/**
 * @since 1.0
 */
class Accessorial extends \SimpleUPS\Model
{

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
     * @return Accessorial
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * The code indicating accessorial for a given UPS World Wide Express Shipment.
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
     * @return Accessorial
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
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Accessorial
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $accessorial = new Accessorial();
        $accessorial->setIsResponse();
        $accessorial
            ->setCode($xml->Code)
            ->setDescription($xml->Description);

        return $accessorial;
    }
}