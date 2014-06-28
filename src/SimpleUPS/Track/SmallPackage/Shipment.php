<?php namespace SimpleUPS\Track\SmallPackage;

use \SimpleUPS\Service;
use \SimpleUPS\Shipper;
use \SimpleUPS\InstructionalAddress;
use \SimpleUPS\Track\ShipmentType;
use \SimpleUPS\Track\Status;
use \SimpleUPS\Track\ReferenceNumber;

/**
 * A shipment is associated with a tracking number and is made up of 1 or more packages
 * @since 1.0
 */
class Shipment extends \SimpleUPS\Shipment
{

    private
        /* @var \SimpleUPS\Track\Status $status */
        $status,
        /* @var \SimpleUPS\Track\ShipmentType $shipmentType */
        $shipmentType,

        /* @var ReferenceNumber $referenceNumber */
        $referenceNumber,
        /* @var string $shipmentIdentificationNumber */
        $shipmentIdentificationNumber,

        /* @var \DateTime $pickupDate */
        $pickupDate,
        /* @var Weight $weight */
        $weight,

        /* @var \DateTime $deliveryTime */
        $deliveryTime;

    /**
     * @internal
     *
     * @param \DateTime $deliveryTime
     *
     * @return Shipment
     */
    public function setDeliveryTime(\DateTime $deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    /**
     * When the item was delivered
     * @see getStatus()
     * @return \DateTime
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @internal
     *
     * @param \DateTime $pickupDate
     *
     * @return Shipment
     */
    public function setPickupDate(\DateTime $pickupDate)
    {
        $this->pickupDate = $pickupDate;
        return $this;
    }

    /**
     * Date the shipment was picked up from the shipper
     * @return \DateTime
     */
    public function getPickupDate()
    {
        return $this->pickupDate;
    }

    /**
     * @internal
     *
     * @param \SimpleUPS\Track\ShipmentType $shipmentType
     *
     * @return Shipment
     */
    public function setShipmentType(\SimpleUPS\Track\ShipmentType $shipmentType)
    {
        $this->shipmentType = $shipmentType;
        return $this;
    }

    /**
     * The type of shipment being tracked
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType->getDescription();
    }

    /**
     * Determine if this shipment is a small package
     * @return bool
     */
    public function isSmallPackage()
    {
        return ShipmentType::TYPE_SMALL_PACKAGE == $this->shipmentType->getCode();
    }

    /**
     * Determine if this shipment is freight
     * @return bool
     */
    public function isFreight()
    {
        return ShipmentType::TYPE_FREIGHT == $this->shipmentType->getCode();
    }

    /**
     * Determine if this shipment is mail innovations
     * @return bool
     */
    public function isMailInnovation()
    {
        return ShipmentType::TYPE_MAIL_INNOVATION == $this->shipmentType->getCode();
    }

    /**
     * @internal
     *
     * @param Status $status
     *
     * @return Shipment
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Status of shipment
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @internal
     *
     * @param \SimpleUPS\Weight $weight
     *
     * @return Shipment
     */
    public function setWeight(\SimpleUPS\Weight $weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Shipment weight
     * @return \SimpleUPS\Weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @internal
     *
     * @param ReferenceNumber $referenceNumber
     *
     * @return Shipment
     */
    public function setReferenceNumber(ReferenceNumber $referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * Shipment reference number
     * @return ReferenceNumber
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @internal
     *
     * @param string $shipmentIdentificationNumber
     *
     * @return Shipment
     */
    public function setShipmentIdentificationNumber($shipmentIdentificationNumber)
    {
        $this->shipmentIdentificationNumber = (string)$shipmentIdentificationNumber;
        return $this;
    }

    /**
     * Shipment identification number
     * @return string
     */
    public function getShipmentIdentificationNumber()
    {
        return $this->shipmentIdentificationNumber;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Shipment
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shipment = new Shipment();
        $shipment->setIsResponse();

        if (isset($xml->PickupDate)) {
            $shipment->setPickupDate(new \DateTime((string)$xml->PickupDate));
        }

        if (isset($xml->ScheduledDeliveryDate) && isset($xml->ScheduledDeliveryTime)) {
            $shipment->setDeliveryTime(
                new \DateTime((string)trim($xml->ScheduledDeliveryDate . ' ' . $xml->ScheduledDeliveryTime))
            );
        }

        if (isset($xml->ShipmentType)) {
            $shipment->setShipmentType(ShipmentType::fromXml($xml->ShipmentType));
        }

        if (isset($xml->Shipper)) {
            $shipment->setShipper(Shipper::fromXml($xml->Shipper));
        }

        if (isset($xml->ShipTo->Address)) {
            $shipment->setDestination(InstructionalAddress::fromXml($xml->ShipTo->Address));
        }

        if (isset($xml->Package)) {
            foreach ($xml->Package as $package) {
                $shipment->addPackage(Package::fromXml($package));
            }
        }

        if (isset($xml->Service)) {
            $shipment->setService(Service::fromXml($xml->Service));
        }

        if (isset($xml->CurrentStatus)) {
            $shipment->setStatus(Status::fromXml($xml->CurrentStatus));
        }

        if (isset($xml->ReferenceNumber)) {
            $shipment->setReferenceNumber(ReferenceNumber::fromXml($xml->ReferenceNumber));
        }

        if (isset($xml->ShipmentWeight)) {
            $shipment->setWeight(\SimpleUPS\Weight::fromXml($xml->ShipmentWeight));
        }

        if (isset($xml->ShipmentIdentificationNumber)) {
            $shipment->setShipmentIdentificationNumber($xml->ShipmentIdentificationNumber);
        }

        return $shipment;

    }

}