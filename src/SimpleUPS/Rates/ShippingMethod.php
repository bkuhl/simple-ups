<?php namespace SimpleUPS\Rates;

use \SimpleUPS\Service;

/**
 * A shipping method is a shipping service associated with a price
 * @since 1.0
 */
class ShippingMethod extends \SimpleUPS\Model
{
    private
        /* @var \SimpleUPS\Service $service */
        $service,
        /* @var integer|null $guaranteedDaysToDelivery */
        $guaranteedDaysToDelivery,

        /* @var float $transportationCharges */
        $transportationCharges,
        /* @var float $serviceOptionsCharges */
        $serviceOptionsCharges,

        /* @var float $totalCharges */
        $totalCharges,
        /* @var DateTime|null $scheduledDeliveryTime */
        $scheduledDeliveryTime,

        /* @var string[]|null $warnings */
        $warnings;

    /**
     * @internal
     *
     * @param integer $guaranteedDaysToDelivery
     *
     * @return ShippingMethod
     */
    public function setGuaranteedDaysToDelivery($guaranteedDaysToDelivery)
    {
        $this->guaranteedDaysToDelivery = $guaranteedDaysToDelivery;
        return $this;
    }

    /**
     * @return integer
     */
    public function getGuaranteedDaysToDelivery()
    {
        return $this->guaranteedDaysToDelivery;
    }

    /**
     * @internal
     *
     * @param \DateTime $scheduledDeliveryTime
     *
     * @return ShippingMethod
     */
    public function setScheduledDeliveryTime(\DateTime $scheduledDeliveryTime)
    {
        $this->scheduledDeliveryTime = $scheduledDeliveryTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getScheduledDeliveryTime()
    {
        return $this->scheduledDeliveryTime;
    }

    /**
     * @internal
     *
     * @param \SimpleUPS\Service $service
     *
     * @return ShippingMethod
     */
    public function setService(\SimpleUPS\Service $service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return \SimpleUPS\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @internal
     *
     * @param float $serviceOptionsCharges
     *
     * @return ShippingMethod
     */
    public function setServiceOptionsCharges($serviceOptionsCharges)
    {
        $this->serviceOptionsCharges = (float)$serviceOptionsCharges;
        return $this;
    }

    /**
     * @return float
     */
    public function getServiceOptionsCharges()
    {
        return $this->serviceOptionsCharges;
    }

    /**
     * @internal
     *
     * @param float $transportationCharges
     *
     * @return ShippingMethod
     */
    public function setTransportationCharges($transportationCharges)
    {
        $this->transportationCharges = (float)$transportationCharges;
        return $this;
    }

    /**
     * @return float
     */
    public function getTransportationCharges()
    {
        return $this->transportationCharges;
    }

    /**
     * @internal
     *
     * @param float $totalCharges
     *
     * @return ShippingMethod
     */
    public function setTotalCharges($totalCharges)
    {
        $this->totalCharges = (float)$totalCharges;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCharges()
    {
        return $this->totalCharges;
    }

    /**
     * @internal
     *
     * @param string $totalCharges
     *
     * @return ShippingMethod
     */
    public function addWarning($warning)
    {
        if ($this->warnings === null) {
            $this->warnings = array();
        }

        $this->warnings[] = $warning;
        return $this;
    }

    /**
     * Determine if UPS provided any warnings for this shipment
     * @see ShippingMethod::getWarnings()
     * @return bool
     */
    public function hasWarnings()
    {
        return $this->warnings == null;
    }

    /**
     * Get warnings associated with this shipping method
     * @see ShippingMethod::hasWarnings()
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return ShippingMethod
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $shippingMethod = new ShippingMethod();
        $shippingMethod->setIsResponse();
        $shippingMethod->setService(Service::fromXml($xml->Service));

        if (isset($xml->TotalCharges->MonetaryValue)) {
            $shippingMethod->setTotalCharges($xml->TotalCharges->MonetaryValue);
        }

        if (isset($xml->TransportationCharges->MonetaryValue)) {
            $shippingMethod->setTransportationCharges($xml->TransportationCharges->MonetaryValue);
        }

        if (isset($xml->ServiceOptionsCharges->MonetaryValue)) {
            $shippingMethod->setServiceOptionsCharges($xml->ServiceOptionsCharges->MonetaryValue);
        }

        if (isset($xml->GuaranteedDaysToDelivery) && is_numeric($xml->GuaranteedDaysToDelivery)) {
            $shippingMethod->setGuaranteedDaysToDelivery($xml->GuaranteedDaysToDelivery);
        }

        if (isset($xml->ScheduledDeliveryTime)) {
            $shippingMethod->setScheduledDeliveryTime(new \DateTime($xml->ScheduledDeliveryTime));
        }

        return $shippingMethod;
    }
}