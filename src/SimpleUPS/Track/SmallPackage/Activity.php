<?php namespace SimpleUPS\Track\SmallPackage;

use \SimpleUPS\Address;

/**
 * A record of activity on a package
 * @since 1.0
 */
class Activity extends \SimpleUPS\Model
{
    private
        /* @var \SimpleUPS\Address $address */
        $address,
        /* @var string $locationCode */
        $locationCode,

        /* @var string $locationDescription */
        $locationDescription,
        /* @var string $signedForBy */
        $signedForBy,

        /* @var StatusType $statusType */
        $statusType,
        /* @var string $statusCode */
        $statusCode,

        /* @var \DateTime $timestamp */
        $timestamp;

    /**
     * @internal
     *
     * @param \SimpleUPS\Address $address
     *
     * @return Activity
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Location for this activity
     * @return \SimpleUPS\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @internal
     *
     * @param string $statusCode
     *
     * @return Activity
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (string)$statusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @internal
     *
     * @param StatusType $statusType
     *
     * @return Activity
     */
    public function setStatusType(StatusType $statusType)
    {
        $this->statusType = $statusType;
        return $this;
    }

    /**
     * Status type of an activity
     * @return StatusType
     */
    public function getStatusType()
    {
        return $this->statusType;
    }

    /**
     * @internal
     *
     * @param \DateTime $timestamp
     *
     * @return Activity
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * When activity took place
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @internal
     *
     * @param string $locationCode
     *
     * @return Activity
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = (string)$locationCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * @internal
     *
     * @param string $locationDescription
     *
     * @return Activity
     */
    public function setLocationDescription($locationDescription)
    {
        $this->locationDescription = (string)$locationDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocationDescription()
    {
        return $this->locationDescription;
    }

    /**
     * @internal
     *
     * @param string $signedForBy
     *
     * @return Activity
     */
    public function setSignedForBy($signedForBy)
    {
        $this->signedForBy = (string)$signedForBy;
        return $this;
    }

    /**
     * Name of the person who signed
     * @return string|null
     */
    public function getSignedForBy()
    {
        return $this->signedForBy;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Activity
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $activity = new Activity();
        $activity->setIsResponse();

        if (isset($xml->ActivityLocation->Code)) {
            $activity->setLocationCode($xml->ActivityLocation->Code);
        }

        if (isset($xml->ActivityLocation->Description)) {
            $activity->setLocationDescription($xml->ActivityLocation->Description);
        }

        if (isset($xml->ActivityLocation->SignedForByName)) {
            $activity->setSignedForBy($xml->ActivityLocation->SignedForByName);
        }

        if (isset($xml->Date) && isset($xml->Time)) {
            $activity->setTimestamp(new \DateTime($xml->Date . ' ' . $xml->Time));
        }

        if (isset($xml->Status->StatusType)) {
            $activity->setStatusType(StatusType::fromXml($xml->Status->StatusType));
        }

        if (isset($xml->Status->StatusCode)) {
            $activity->setStatusCode($xml->Status->StatusCode->Code);
        }

        if (isset($xml->ActivityLocation->Address)) {
            $activity->setAddress(Address::fromXml($xml->ActivityLocation->Address));
        }

        return $activity;
    }
}