<?php namespace SimpleUPS\Track\SmallPackage;

use \SimpleUPS\Address;

use \SimpleUPS\Weight;

use \SimpleUPS\Track\ReferenceNumber;

use \SimpleUPS\Track\SmallPackage\Activity;

/**
 * A package represents a box or item
 * @since 1.0
 */
class Package extends \SimpleUPS\Package
{

    private
        $SIGNATURE_REQUIRED_ADULT = 'A',
        $SIGNATURE_REQUIRED = 'S',
        $LOCATION_ASSURED = 1;

    private
        /* @var string $trackingNumber */
        $trackingNumber,

        /* @var Activity[] $activity */
        $activity,
        /* @var \DateTime $scheduledDeliveryTime */
        $scheduledDeliveryTime,

        /* @var \DateTime $rescheduledDeliveryTime */
        $rescheduledDeliveryTime,
        /* @var \SimpleUPS\Address $rerouteAddress */
        $rerouteAddress,

        /* @var \SimpleUPS\Address $returnToAddress */
        $returnToAddress,
        /* @var string $signatureType */
        $signatureType,

        /* @var ReferenceNumber[] $referenceNumbers */
        $referenceNumbers,
        /* @var ProductType $productType */
        $productType,

        /* @var integer $locationAssured */
        $locationAssured,

        /* @var Accessorial[] $accessorials */
        $accessorials;

    /**
     * @internal
     *
     * @param \SimpleUPS\Address $rerouteAddress
     *
     * @return Package
     */
    public function setRerouteAddress(Address $rerouteAddress)
    {
        $this->rerouteAddress = $rerouteAddress;
        return $this;
    }

    /**
     * Reroute address when an package has been rerouted
     * When a requester to intercept US50/PR package at the destination center at
     * any time before it has been delivered,
     * @return \SimpleUPS\Address
     */
    public function getRerouteAddress()
    {
        return $this->rerouteAddress;
    }

    /**
     * @internal
     *
     * @param Activity $activity
     *
     * @return Package
     */
    public function addActivity(Activity $activity)
    {
        if ($this->activity === null) {
            $this->activity = array();
        }

        $this->activity[] = $activity;
        return $this;
    }

    /**
     * @return Activity[]
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @internal
     *
     * @param \DateTime $scheduledDeliveryTime
     *
     * @return Package
     */
    public function setScheduledDeliveryTime(\DateTime $scheduledDeliveryTime)
    {
        $this->scheduledDeliveryTime = $scheduledDeliveryTime;
        return $this;
    }

    /**
     * Date shipment was originally scheduled for delivery.
     * @return \DateTime|null
     */
    public function getScheduledDeliveryTime()
    {
        return $this->scheduledDeliveryTime;
    }

    /**
     * @internal
     *
     * @param \DateTime $rescheduledDeliveryTime
     *
     * @return Package
     */
    public function setRescheduledDeliveryTime(\DateTime $rescheduledDeliveryTime)
    {
        $this->rescheduledDeliveryTime = $rescheduledDeliveryTime;
        return $this;
    }

    /**
     * The delivery is rescheduled to this date
     * @return \DateTime|null
     */
    public function getRescheduledDeliveryTime()
    {
        return $this->rescheduledDeliveryTime;
    }

    /**
     * @internal
     *
     * @param \SimpleUPS\Address $returnToAddress
     *
     * @return Package
     */
    public function setReturnToAddress(Address $returnToAddress)
    {
        $this->returnToAddress = $returnToAddress;
        return $this;
    }

    /**
     * If the package is returned, the address to whom it was returned
     * @return \SimpleUPS\Address|null
     */
    public function getReturnToAddress()
    {
        return $this->returnToAddress;
    }

    /**
     * @internal
     *
     * @param string $signatureType
     *
     * @return Package
     */
    public function setSignatureType($signatureType)
    {
        $this->signatureType = (string)$signatureType;
        return $this;
    }

    /**
     * @internal
     * @return string
     */
    public function getSignatureType()
    {
        return $this->signatureType;
    }

    /**
     * @internal
     *
     * @param string $trackingNumber
     *
     * @return Package
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = (string)$trackingNumber;
        return $this;
    }

    /**
     * Tracking number
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @internal
     *
     * @param ReferenceNumber $referenceNumber
     *
     * @return Package
     */
    public function addReferenceNumber(ReferenceNumber $referenceNumber)
    {
        if ($this->referenceNumbers === null) {
            $this->referenceNumbers = array();
        }

        $this->referenceNumbers[] = $referenceNumber;
        return $this;
    }

    /**
     * Reference numbers
     * @return Package[]|null
     */
    public function getReferenceNumbers()
    {
        return $this->referenceNumbers;
    }

    /**
     * @internal
     *
     * @param Weight $weight
     *
     * @return Package
     */
    public function setWeight(Weight $weight)
    {
        parent::setWeight($weight);
        return $this;
    }

    /**
     * @internal
     *
     * @param ProductType $productType
     *
     * @return Package
     */
    public function setProductType(ProductType $productType)
    {
        $this->productType = $productType;
        return $this;
    }

    /**
     * The product type of product
     * @return ProductType
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @internal
     *
     * @param Accessorial $accessorial
     *
     * @return Package
     */
    public function addAccessorial(Accessorial $accessorial)
    {
        if ($this->accessorials === null) {
            $this->accessorials = array();
        }

        $this->accessorials[] = $accessorial;
        return $this;
    }

    /**
     * @return Accessorial[]
     */
    public function getAccessorials()
    {
        return $this->accessorials;
    }

    /**
     * @internal
     *
     * @param integer $locationAssured
     *
     * @return Package
     */
    public function setLocationAssured($locationAssured)
    {
        $this->locationAssured = (int)$locationAssured;
        return $this;
    }

    /**
     * @internal
     * @return integer
     */
    public function getLocationAssured()
    {
        return $this->locationAssured;
    }

    /**
     * Indication of Location Assured Service.
     * @return bool
     */
    public function isLocationAssured()
    {
        return $this->getLocationAssured() == $this->LOCATION_ASSURED;
    }

    /**
     * Does package require a signature for delivery
     * @return bool
     */
    public function isSignatureRequired()
    {
        return $this->getSignatureType() == $this->SIGNATURE_REQUIRED;
    }

    /**
     * Does package require an adult signature for delivery
     * @return bool
     */
    public function isAdultSignatureRequired()
    {
        return $this->getSignatureType() == $this->SIGNATURE_REQUIRED_ADULT;
    }

    /**
     * Create an address from XML.  SimpleXMLElement passed must have immediate children like AddressLine1, City, etc.
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Package
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $package = new Package();
        $package->setIsResponse();

        if (isset($xml->TrackingNumber)) {
            $package->setTrackingNumber($xml->TrackingNumber);
        }

        if (isset($xml->RescheduledDeliveryDate) && isset($xml->RescheduledDeliveryTime)) {
            $package->setRescheduledDeliveryTime(
                new \DateTime(trim($xml->RescheduledDeliveryDate . ' ' . $xml->RescheduledDeliveryTime))
            );
        }

        if (isset($xml->ScheduledDeliveryDate) && isset($xml->ScheduledDeliveryTime)) {
            $package->setScheduledDeliveryTime(
                new \DateTime(trim($xml->ScheduledDeliveryDate . ' ' . $xml->ScheduledDeliveryTime))
            );
        }

        if (isset($xml->Reroute->Address)) {
            $package->setRerouteAddress(Address::fromXml($xml->Reroute->Address));
        }

        if (isset($xml->ReturnTo->Address)) {
            $package->setReturnToAddress(Address::fromXml($xml->ReturnTo->Address));
        }

        if (isset($xml->PackageServiceOptions->SignatureRequired->Code)) {
            $package->setSignatureType($xml->PackageServiceOptions->SignatureRequired->Code);
        }

        if (isset($xml->PackageWeight)) {
            $package->setWeight(Weight::fromXml($xml->PackageWeight));
        }

        if (isset($xml->ReferenceNumber)) {
            foreach ($xml->ReferenceNumber as $referenceNumber) {
                $package->addReferenceNumber(ReferenceNumber::fromXml($referenceNumber));
            }
        }

        if (isset($xml->Activity)) {
            foreach ($xml->Activity as $activity) {
                $package->addActivity(Activity::fromXml($activity));
            }
        }

        if (isset($xml->ShipmentWeight)) {
            $package->setWeight(Weight::fromXml($xml->ShipmentWeight));
        }

        if (isset($xml->LocationAssured)) {
            $package->setLocationAssured($xml->LocationAssured);
        }

        if (isset($xml->Accessorial)) {
            foreach ($xml->Accessorial as $accessorial) {
                $package->addAccessorial(Accessorial::fromXml($accessorial));
            }
        }

        return $package;
    }
}