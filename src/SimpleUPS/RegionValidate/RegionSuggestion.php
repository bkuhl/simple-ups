<?php namespace SimpleUPS\RegionValidate;

use SimpleUPS\Address;

/**
 * A suggested region is provided during region validation when UPS provides more
 * information about a region.  Usually this means something was wrong with the
 * original region being validated.
 * If a postal code was omitted in validation, the postal code low and high ends will
 * be populated with the postal codes that fit within that region.
 * @see   \SimpleUPS\UPS::isValidRegion()
 * @see   \SimpleUPS\UPS::getRegionSuggestions()
 * @since 1.0
 */
class RegionSuggestion
{
    private
        $rank,
        $quality,
        $address,
        $postalCodeLowEnd,
        $postalCodeHighEnd;

    /**
     * @internal
     *
     * @param integer $rank
     *
     * @return RegionSuggestion
     */

    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * The rank of each region suggestion
     * @since 1.0
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @internal
     *
     * @param float $quality
     *
     * @return RegionSuggestion
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * The quality factor, which describes the accuracy of the result compared to the request.
     * <ul>
     *  <li>1.0 = Exact match.  Usually this means UPS provided a correction</li>
     *  <li>95-.99 = Very close match.</li>
     *  <li>90-.94 = Close match.</li>
     *  <li>70-.89 = Possible match.</li>
     *  <li>00-.69 = Poor match</li>
     * </ul>
     * @see   isCorrected()
     * @since 1.0
     * @return float
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Determine if this region is a correction of the original region requested for validation
     * @see   getQuality()
     * @since 1.0
     * @return bool
     */
    public function isCorrected()
    {
        return $this->getQuality() == 1;
    }

    /**
     * @internal
     *
     * @param \SimpleUPS\Address $address
     *
     * @return RegionSuggestion
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Provides the city, state/province, and country code
     * @since 1.0
     * @return \SimpleUPS\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @internal
     *
     * @param integer $postalCodeLowEnd
     *
     * @return RegionSuggestion
     */
    public function setPostalCodeLowEnd($postalCodeLowEnd)
    {
        $this->postalCodeLowEnd = $postalCodeLowEnd;
        return $this;
    }

    /**
     * When matches for a given region combination, a postal code range may be associated with each match. This is the low end of the range.
     * @since 1.0
     * @return integer
     */
    public function getPostalCodeLowEnd()
    {
        return $this->postalCodeLowEnd;
    }

    /**
     * @internal
     *
     * @param integer $postalCodeHighEnd
     *
     * @return RegionSuggestion
     */
    public function setPostalCodeHighEnd($postalCodeHighEnd)
    {
        $this->postalCodeHighEnd = $postalCodeHighEnd;
        return $this;
    }

    /**
     * When matches for a given region combination, a postal code range may be associated with each match. This is the high end of the range.
     * @since 1.0
     * @return integer
     */
    public function getPostalCodeHighEnd()
    {
        return $this->postalCodeHighEnd;
    }
}