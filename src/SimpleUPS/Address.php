<?php namespace SimpleUPS;

use SimpleUPS\Api\InvalidParameterException;

/**
 * Information about an address
 * This class is used as a container throughout the library to pass addresses back
 * and forth between objects.
 * @since 1.0
 */
class Address extends \SimpleUPS\Model
{

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $stateProvinceCode;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $postalCodeExtended;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * Set the street
     *
     * @param string $street
     *
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = (string)$street;
        return $this;
    }

    /**
     * Street name and number (when applicable)
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the city
     * Must be between 1-40 characters
     *
     * @param string $city
     *
     * @throws \SimpleUPS\Api\InvalidParameterException
     * @return Address
     */
    public function setCity($city)
    {
        if (!$this->isResponse() && $city != null && strlen($city) > 40) {
            throw new InvalidParameterException('City must be between 1-40 characters');
        }

        $this->city = (string)$city;
        return $this;
    }

    /**
     * Get the city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the state or province code
     * Must be 2 characters
     *
     * @param string $stateProvinceCode
     *
     * @throws \SimpleUPS\Api\InvalidParameterException
     * @return Address
     */
    public function setStateProvinceCode($stateProvinceCode)
    {
        if (!$this->isResponse() && $stateProvinceCode != null && strlen($stateProvinceCode) != 2)
            throw new InvalidParameterException('State/Province Code must be 2 characters');

        $this->stateProvinceCode = (string)$stateProvinceCode;
        return $this;
    }

    /**
     * Get the state or province
     * @return string
     */
    public function getStateProvinceCode()
    {
        return $this->stateProvinceCode;
    }

    /**
     * Set the postal code
     * Must be between 1-9 characters
     * @see setPostalCodeExtended
     *
     * @param string $postalCode
     *
     * @return Address
     * @throws \SimpleUPS\Api\InvalidParameterException
     */
    public function setPostalCode($postalCode)
    {
        if (!$this->isResponse() && $postalCode != null && (strlen($postalCode) < 1 || strlen($postalCode) > 9))
            throw new InvalidParameterException('Postal Code must be between 1-9 characters');

        if (!$this->isResponse() && strstr($postalCode, '-'))
            throw new InvalidParameterException(
                'Postal Code may not contain any special characters, use the extended postal code'
            );

        $this->postalCode = (string)$postalCode;
        return $this;
    }

    /**
     * Get the postal code
     * @see getPostalCodeExtended
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the extended postal code
     * @link http://en.wikipedia.org/wiki/ZIP_code#ZIP.2B4
     *
     * @param string $postalCodeExtended
     *
     * @return Address
     */
    public function setPostalCodeExtended($postalCodeExtended)
    {
        $this->postalCodeExtended = (string)$postalCodeExtended;
        return $this;
    }

    /**
     * The last segment of a zip code from the format xxxxx-xxxx
     * @link http://en.wikipedia.org/wiki/ZIP_code#ZIP.2B4
     * @return string
     */
    public function getPostalCodeExtended()
    {
        return $this->postalCodeExtended;
    }

    /**
     * Set the country
     * Must be 2 characters
     *
     * @param string $countryCode
     *
     * @return Address
     * @throws \SimpleUPS\Api\InvalidParameterException
     */
    public function setCountryCode($countryCode)
    {
        if (!$this->isResponse() && $countryCode != null && strlen($countryCode) != 2)
            throw new InvalidParameterException('Country Code must be 2 characters');

        $this->countryCode = (string)$countryCode;
        return $this;
    }

    /**
     * Get the country code
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Create an address from XML.  SimpleXMLElement passed must have immediate children like AddressLine1, City, etc.
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleUPS\Address
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $address = new Address();
        $address->setIsResponse();

        //@todo Consider alternatives for what to do with Consignee
        if (isset($xml->AddressLine1)) {
            $street = $xml->AddressLine1;
            if (isset($xml->AddressLine2))
                $street .= ' ' . $xml->AddressLine2;
            if (isset($xml->AddressLine3))
                $street .= ' ' . $xml->AddressLine3;

            $address->setStreet(trim((string)$street));
        }

        if (isset($xml->City))
            $address->setCity($xml->City);

        if (isset($xml->StateProvinceCode))
            $address->setStateProvinceCode((string)$xml->StateProvinceCode);

        if (isset($xml->PostalCode))
            $address->setPostalCode((string)$xml->PostalCode);

        if (isset($xml->CountryCode))
            $address->setCountryCode((string)$xml->CountryCode);

        return $address;
    }
}
