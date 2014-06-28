<?php namespace SimpleUPS\RegionValidate;

use \SimpleUPS\Address;

/**
 * @internal
 */
class Response extends \SimpleUPS\Api\Response
{
    private
        $address,
        $suggestedRegions;

    /**
     * @param \SimpleUPS\Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Determine if the given address region (city, state and postal code) are valid
     * @return bool
     */
    public function isRegionValid()
    {
        $suggestedRegions = $this->getSuggestedRegions();
        return count($suggestedRegions) == 1 && $suggestedRegions[0]->getQuality() == 1;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param RegionSuggestion $suggestedRegion
     *
     * @return Response
     */
    public function addSuggestedRegion(RegionSuggestion $suggestedRegion)
    {
        if ($this->suggestedRegions == null) {
            $this->suggestedRegions = array();
        }

        $this->suggestedRegions[] = $suggestedRegion;
        return $this;
    }

    /**
     * @return RegionSuggestion
     */
    public function getSuggestedRegions()
    {
        return $this->suggestedRegions;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    public function fromXml(\SimpleXMLElement $xml)
    {
        $response = new Response($this->getAddress());
        foreach ($xml->AddressValidationResult as $rating) {
            $address = new Address();
            $address->setIsResponse();
            $address
                ->setCity((string)$rating->Address->City)
                ->setStateProvinceCode((string)$rating->Address->StateProvinceCode);
            $regionSuggestion = new RegionSuggestion();
            $regionSuggestion->setRank((int)$rating->Rank);
            $regionSuggestion->setQuality((float)$rating->Quality);
            $regionSuggestion->setAddress($address);
            $regionSuggestion->setPostalCodeLowEnd((string)$rating->PostalCodeLowEnd);
            $regionSuggestion->setPostalCodeHighEnd((string)$rating->PostalCodeHighEnd);

            $response->addSuggestedRegion($regionSuggestion);
        }

        return $response;
    }
}