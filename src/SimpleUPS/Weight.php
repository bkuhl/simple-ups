<?php namespace SimpleUPS;

/**
 * The weight of a package
 * @since 1.0
 */
class Weight extends Model
{
    private
        /* @var string $code */
        $code = 'LBS',

        /* @var string $description */
        $description,

        /* @var float $weight */
        $weight;

    /**
     * @internal
     *
     * @param string $code
     *
     * @return Weight
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
     * @return Weight
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
     * @param float $weight
     *
     * @return Weight
     */
    public function setWeight($weight)
    {
        $this->weight = (float)$weight;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Determine if the unit of measurement is LBS
     * @return bool
     */
    public function isPounds()
    {
        return $this->getCode() == 'LBS';
    }

    /**
     * Determine if the unit of measurement is KGS
     * @return bool
     */
    public function isKilograms()
    {
        return $this->getCode() == 'KGS';
    }

    /**
     * @internal
     *
     * @param \DomDocument $dom
     *
     * @return \DOMElement
     */
    public function toXml(\DomDocument $dom)
    {
        $packageWeight = $dom->createElement('PackageWeight');

        if ($this->getCode() != null) {
            $packageWeight->appendChild($unitOfMeasurement = $dom->createElement('UnitOfMeasurement'));
            $unitOfMeasurement->appendChild($dom->createElement('Code', $this->getCode()));
        }

        if ($this->getWeight() != null) {
            $packageWeight->appendChild($dom->createElement('Weight', $this->getWeight()));
        }

        return $packageWeight;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Weight
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shipmentWeight = new Weight();
        $shipmentWeight->setIsResponse();
        $shipmentWeight
            ->setCode($xml->UnitOfMeasurement->Code)
            ->setDescription($xml->UnitOfMeasurement->Description)
            ->setWeight($xml->Weight);

        return $shipmentWeight;
    }
}