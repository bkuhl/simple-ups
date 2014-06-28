<?php namespace SimpleUPS\Track\SmallPackage;

/**
 * What kind of message the custom can provide
 * @since 1.0
 */

class Message extends \SimpleUPS\Model
{

    private
        $CODE_ON_TIME = '01',
        $CODE_RESCHEDULED = '02',
        $CODE_RETURNED_TO_SHIPPER = '03';
    private
        /* @var string $code */
        $code,

        /* @var string $description */
        $description;

    /**
     * @internal
     *
     * @param string $code
     *
     * @return Message
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @internal
     *
     * @param string $description
     *
     * @return Message
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isOnTime()
    {
        return $this->getCode() == $this->CODE_ON_TIME;
    }

    /**
     * @return bool
     */
    public function isRescheduled()
    {
        return $this->getCode() == $this->CODE_RESCHEDULED;
    }

    /**
     * @return bool
     */
    public function isReturnedToShipper()
    {
        return $this->getCode() == $this->CODE_RETURNED_TO_SHIPPER;
    }

    /**
     * @internal
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Message
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $message = new Message();
        $message->setIsResponse();

        if (isset($xml->Code)) {
            $message->setCode($xml->Code);
        }

        if (isset($xml->Description)) {
            $message->setDescription($xml->Description);
        }

        return $message;
    }
}