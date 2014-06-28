<?php namespace SimpleUPS\Track;

use \SimpleUPS\UPS;

/**
 * @internal
 */
abstract class Request extends \SimpleUPS\Api\Request
{

    const
        TYPE_SMALL_PACKAGE = '01',
        TYPE_FREIGHT = '02',
        TYPE_MAIL_INNOVATIONS = '03',
        REQUEST_INVALID_TRACKING_NUMBER = 151018,
        REQUEST_NO_TRACKING_INFORMATION = 151044;

    /**
     * Determine which API call will be made
     * @return string
     */
    public function getUrl()
    {
        return $this->getDebug(
        ) ? 'https://wwwcie.ups.com/ups.app/xml/Track' : 'https://onlinetools.ups.com/ups.app/xml/Track';
    }

    public function handleResponseErrors(\SimpleXMLElement $xml)
    {
        if ($xml->Response->ResponseStatusCode == Request::REQUEST_FAIL) {
            if ((int)$xml->Response->Error->ErrorCode == Request::REQUEST_INVALID_TRACKING_NUMBER) {
                throw new \SimpleUPS\Track\TrackingNumberNotFoundException(
                    $xml->Response->Error->ErrorDescription,
                    (int)$xml->Response->Error->ErrorCode,
                    (int)$xml->Response->Error->ErrorSeverity
                );
            } else {
                if ((int)$xml->Response->Error->ErrorCode == Request::REQUEST_NO_TRACKING_INFORMATION) {
                    throw new \SimpleUPS\Api\NoTrackingInformationException(
                        $xml->Response->Error->ErrorDescription,
                        (int)$xml->Response->Error->ErrorCode,
                        (int)$xml->Response->Error->ErrorSeverity
                    );
                } else {
                    throw new \SimpleUPS\Api\ResponseErrorException(
                        $xml->Response->Error->ErrorDescription,
                        (int)$xml->Response->Error->ErrorCode,
                        (int)$xml->Response->Error->ErrorSeverity
                    );
                }
            }
        } else {
            if (isset($xml->Shipment->ShipmentType->Code) && (string)$xml->Shipment->ShipmentType->Code != Request::TYPE_SMALL_PACKAGE) {
                throw new \SimpleUPS\Api\ResponseErrorException(
                    'In order to track freight or mail innovations you must upgrade your ' . UPS::$libraryName . ' license',
                    (int)$xml->Response->Error->ErrorCode,
                    (int)$xml->Response->Error->ErrorSeverity
                );
            }
        }
    }
}