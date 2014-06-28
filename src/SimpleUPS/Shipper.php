<?php namespace SimpleUPS;

/**
 * Contains information about the shipper
 * @since 1.0
 */
class Shipper extends Model
{
    private
        /* @var string $number */
        $number,

        /* @var InstructionalAddress $address */
        $address;

    /**
     * @internal
     *
     * @param InstructionalAddress $address
     *
     * @return Shipper
     */
    public function setAddress(\SimpleUPS\InstructionalAddress $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get the shipper's address
     * @return InstructionalAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @internal
     *
     * @param string $number
     *
     * @return Shipper
     */
    public function setNumber($number)
    {
        $this->number = (string)$number;
        return $this;
    }

    /**
     * Get the shipper UPS account number
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
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
        $shipper = $dom->createElement('Shipper');

        if ($this->getAddress()->getAddressee() != null) {
            $shipper->appendChild($dom->createElement('Name', $this->getAddress()->getAddressee()));
        }

        if ($this->getNumber() != null) {
            $shipper->appendChild($dom->createElement('ShipperNumber', $this->getNumber()));
        }

        if ($this->getAddress() != null) {
            $shipper->appendChild($this->getAddress()->toXml($dom));
        }

        return $shipper;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Shipper
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shipper = new Shipper();
        $shipper->setIsResponse();

        if (isset($xml->ShipperNumber)) {
            $shipper->setNumber($xml->ShipperNumber);
        }

        if (isset($xml->Address)) {
            $shipper->setAddress(InstructionalAddress::fromXml($xml->Address));
        }

        return $shipper;
    }
}