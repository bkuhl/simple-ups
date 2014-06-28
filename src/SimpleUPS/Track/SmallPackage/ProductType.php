<?php namespace SimpleUPS\Track\SmallPackage;

/**
 * @since 1.0
 */
class ShipmentType extends \SimpleUPS\Model
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
     * @return ShipmentType
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * Code indicating the type of the Product
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
     * @return ShipmentType
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    /**
     * Description of the type of the Product.
     * Valid Value: “World Ease” (when a shipment with single/multiple packages is associated with World Ease movement).
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isWorldEase()
    {
        return $this->getDescription() == "World Ease";
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return ProductType
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $productType = new ProductType();
        $productType->setIsResponse();
        $productType
            ->setCode($xml->Code)
            ->setDescription($xml->Description);

        return $productType;
    }
}