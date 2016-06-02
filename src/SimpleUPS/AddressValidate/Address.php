<?php namespace SimpleUPS\AddressValidate;

/**
 * @since 1.0
 */
class Address extends \SimpleUPS\InstructionalAddress
{
    protected $CLASSIFICATION_UNKNOWN = 0;

    protected $CLASSIFICATION_COMMERCIAL = 1;

    protected $CLASSIFICATION_RESIDENTIAL = 2;

    private $classification;

    /**
     * @internal
     *
     * @param integer $classification
     *
     * @return Address
     */
    public function setClassification($classification)
    {
        $this->classification = (int)$classification;
        return $this;
    }

    /**
     * The classification of a given address by UPS
     * @internal
     * @return integer
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Determine if the address is a commercial location
     * It is possible for an address to be "Unknown", meaning it will not be
     * either Commercial or Residential.
     * This method is only usable when this object is supplied by the
     * address validation methods.
     * @see \SimpleUPS\AddressValidate\Address::isResidential()
     * @see \SimpleUPS\UPS::isValidAddress()
     * @return bool
     */
    public function isCommercial()
    {
        return $this->getClassification() == $this->CLASSIFICATION_COMMERCIAL;
    }

    /**
     * Determine if the address is a residential location.
     * It is possible for an address to be "Unknown", meaning it will not be
     * either Commercial or Residential.
     * This method is only usable when this object is supplied by the
     * address validation methods.
     * @see \SimpleUPS\AddressValidate\Address::isCommercial()
     * @see \SimpleUPS\UPS::isValidAddress()
     * @return bool
     */
    public function isResidential()
    {
        return $this->getClassification() == $this->CLASSIFICATION_RESIDENTIAL;
    }
}
