<?php namespace SimpleUPS\AddressValidate;

/**
 * @internal
 */
class Response extends \SimpleUPS\Api\Response
{
    private
        /* @var \SimpleUPS\Address $address */
        $address,

        /* @var Address|null $correctedAddress */
        $correctedAddress,

        /* @var Address[]|null $suggestedAddresses */
        $suggestedAddresses,

        /* @var bool|null $isValidAddress */
        $isValidAddress;

    /**
     * @param \SimpleUPS\Address $address The address to be validated
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Set the validity of the address
     *
     * @param bool $isValidAddress
     */
    public function setIsValidAddress($isValidAddress)
    {
        $this->isValidAddress = $isValidAddress;
    }

    /**
     * Get address to be used in this request
     * @return \SimpleUPS\Address|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Determine if this address was valid from the previous request
     * @return bool|null
     */
    public function isAddressValid()
    {
        return $this->isValidAddress;
    }

    /**
     * @param Address $correctedAddress
     *
     * @return Response
     */
    public function setCorrectedAddress(Address $correctedAddress)
    {
        $this->correctedAddress = $correctedAddress;
        return $this;
    }

    /**
     * Sometimes UPS will "fix" an address if some information is invalid but it's still able to determine
     * what the correct address is.  It will also add an extended postcode (ie: 30721-4932).
     * @return Address
     */
    public function getCorrectedAddress()
    {
        return $this->correctedAddress;
    }

    /**
     * @param Address $suggestedAddress
     *
     * @return Response
     */
    public function addSuggestedAddress(Address $suggestedAddress)
    {
        if ($this->suggestedAddresses == null) {
            $this->suggestedAddresses = array();
        }

        $this->suggestedAddresses[] = $suggestedAddress;
        return $this;
    }

    /**
     * Some requests may return a more accurate address
     * @return Address[]
     */
    public function getSuggestedAddresses()
    {
        return $this->suggestedAddresses;
    }

    /**
     * Populate this response from XML
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    public function fromXml(\SimpleXMLElement $xml)
    {
        //populate address objects
        $addressesInResponse = array();
        foreach ($xml->AddressKeyFormat as $suggestedAddress) {
            $address = new Address();
            $address->setIsResponse();
            $address
                ->setClassification($suggestedAddress->AddressClassification->Code)
                ->setStreet($suggestedAddress->AddressLine[0])
                ->setCity($suggestedAddress->PoliticalDivision2)
                ->setStateProvinceCode($suggestedAddress->PoliticalDivision1)
                ->setPostalCode($suggestedAddress->PostcodePrimaryLow)
                ->setPostalCodeExtended($suggestedAddress->PostcodeExtendedLow)
                ->setCountryCode($suggestedAddress->CountryCode);
            if ($suggestedAddress->AddressLine[1]) {
                $address->setAddressLine2($suggestedAddress->AddressLine[1]);
                if ($suggestedAddress->AddressLine[2]) {
                    $address->setAddressLine3($suggestedAddress->AddressLine[2]);
                }
            }
            $addressesInResponse[] = $address;
        }

        //if address is valid, provide the full valid address
        if (isset($xml->ValidAddressIndicator)) {
            $this->setIsValidAddress(true);
            $this->setCorrectedAddress($addressesInResponse[0]);
        } elseif (isset($xml->AmbiguousAddressIndicator)) {
            foreach ($addressesInResponse as $address) {
                $this->addSuggestedAddress($address);
            }
        }

        return $this;
    }
}