<?php namespace SimpleUPS;

/**
 * An address with additional address lines for room, floor, or department that
 * a carrier might need when delivering a package
 * @since 1.0
 */
class InstructionalAddress extends Address
{
    private
        /* @var string $addressee */
        $addressee,

        /* @var string $addressLine2 */
        $addressLine2,

        /* @var string $addressLine3 */
        $addressLine3,

        /* @var bool $isResidential */
        $isResidential = false;

    /**
     * Name of the addressee
     *
     * @param string $addressee
     *
     * @return InstructionalAddress
     */
    public function setAddressee($addressee)
    {
        $this->addressee = (string)$addressee;
        return $this;
    }

    /**
     * Name of the addressee
     * @return string
     */
    public function getAddressee()
    {
        return $this->addressee;
    }

    /**
     * Additional address information, preferably room or floor
     *
     * @param string $addressLine2
     *
     * @return InstructionalAddress
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = (string)$addressLine2;
        return $this;
    }

    /**
     * Additional address information, preferably room or floor
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * Additional address information, preferably department name
     *
     * @param string $addressLine3
     *
     * @return InstructionalAddress
     */
    public function setAddressLine3($addressLine3)
    {
        $this->addressLine3 = (string)$addressLine3;
        return $this;
    }

    /**
     * Additional address information, preferably department name
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->addressLine3;
    }

    /**
     * Define if this address is residential
     *
     * @param $isResidential
     *
     * @return InstructionalAddress
     */
    public function setIsResidential($isResidential)
    {
        $this->isResidential = $isResidential;
        return $this;
    }

    /**
     * @internal
     * @return bool
     */
    public function isResidential()
    {
        return $this->isResidential;
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
        $address = $dom->createElement('Address');

        if ($this->getAddressee() != null) //no UPS api uses this
        {
            $address->appendChild($dom->createElement('Addressee', $this->getAddressee()));
        }

        if ($this->getStreet() != null) {
            $address->appendChild($dom->createElement('AddressLine1', $this->getStreet()));
        }

        if ($this->getAddressLine2() != null) {
            $address->appendChild($dom->createElement('AddressLine2', $this->getAddressLine2()));
        }

        if ($this->getAddressLine3() != null) {
            $address->appendChild($dom->createElement('AddressLine3', $this->getAddressLine3()));
        }

        if ($this->getCity() != null) {
            $address->appendChild($dom->createElement('City', $this->getCity()));
        }

        if ($this->getStateProvinceCode() != null) {
            $address->appendChild($dom->createElement('StateProvinceCode', $this->getStateProvinceCode()));
        }

        if ($this->getPostalCode() != null) {
            $address->appendChild($dom->createElement('PostalCode', $this->getPostalCode()));
        }

        if ($this->getCountryCode() != null) {
            $address->appendChild($dom->createElement('CountryCode', $this->getCountryCode()));
        }

        if ($this->isResidential()) {
            $address->appendChild($dom->createElement('ResidentialAddressIndicator'));
        }

        return $address;
    }

    /**
     * Create an address from XML.  SimpleXMLElement passed must have immediate children like AddressLine1, City, etc.
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleUPS\InstructionalAddress
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $address = new InstructionalAddress();
        $address->setIsResponse();

        //@todo Consider alternatives for what to do with Consignee

        if (isset($xml->AddressLine1)) {
            $address->setStreet(trim($xml->AddressLine1));
        }

        if (isset($xml->AddressLine2)) {
            $address->setAddressLine2(trim($xml->AddressLine2));
        }

        if (isset($xml->AddressLine3)) {
            $address->setAddressLine3(trim($xml->AddressLine3));
        }

        if (isset($xml->City)) {
            $address->setCity($xml->City);
        }

        if (isset($xml->StateProvinceCode)) {
            $address->setStateProvinceCode((string)$xml->StateProvinceCode);
        }

        if (isset($xml->PostalCode)) {
            $address->setPostalCode((string)$xml->PostalCode);
        }

        if (isset($xml->CountryCode)) {
            $address->setCountryCode((string)$xml->CountryCode);
        }

        return $address;
    }
}