<?php namespace SimpleUPS;

/**
 * This is the SimpleUPS PHP library
 * The SimpleUPS PHP Library is designed to simplify the UPS API.  It is intended
 * for Small Businesses and includes support for Address/Region Validation & Correction and
 * Small Package Tracking & Rates.+
 * @author      BenKuhl <benkuhl@gmail.com>
 * @license     http://www.simpleups.io/license
 * @link        https://www.ups.com/upsdeveloperkit UPS Developer Kit
 * @since       1.0
 */
class UPS
{

    private static

        /* @var null $accountNumber */
        $accountNumber,

        /* @var null $accessLicenseNumber */
        $accessLicenseNumber,

        /* @var null $userId */
        $userId,

        /* @var null $shipperNumber */
        $shipperNumber,

        /* @var null $password */
        $password,

        /* @var null $debug */
        $debug,

        /* @var \SimpleUPS\AddressValidate\Response $addressValidationResponse */
        $addressValidationResponse,

        /* @var \SimpleUPS\RegionValidate\Response $regionValidationResponse */
        $regionValidationResponse,

        /* @var Shipper $shipper */
        $shipper,

        /* @var string $currencyCode */
        $currencyCode = 'USD';

    public static
        /* @var Object UPS::$request The last request object */
        $request,

        /* @var string UPS::$response The last response xml */
        $response,

        /* @var string UPS::$libraryName The name of this library */
        $libraryName = 'SimpleUPS';

    /**
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     * @throws \SimpleUPS\Api\InvalidParameterException
     */
    public static function getRates(
        Rates\Shipment $shipment,
        $pickupType = Rates\PickupType::DAILY_PICKUP,
        $rateType = null
    ) {
        $request = new Rates\Request();
        $request->setShipment($shipment);
        $request->setPickupType($pickupType);

        if ($rateType != null) {
            $request->setRateType($rateType);
        }

        $response = $request->sendRequest();

        return $response->getShippingMethods();
    }

    /**
     * Determine if various combinations of city, state/province, postal code and country code are valid.
     * If an address is invalid while the region is, then the invalid portion of the address is the street.  Also
     * assists with determining if city is misspelled or state doesn't match the postal code.
     * Address fields used include:
     * <ul>
     *  <li>city</li>
     *  <li>stateProvinceCode</li>
     *  <li>postalCode - REQUIRED</li>
     *  <li>countryCode - REQUIRED</li>
     * </ul>
     * @note    If a postal code is missing, the region is not accurate enough and thus will always return false.
     * @see     \SimpleUPS\UPS::isValidAddress()
     * @see     \SimpleUPS\UPS::getSuggestedRegions()
     *
     * @param Address $address
     *
     * @return bool
     * @example "http://ups.local/examples/SimpleUPS.isValidRegion()_valid.phps" A valid region
     * @example "http://ups.local/examples/SimpleUPS.isValidRegion()_invalid.phps" An invalid region
     * @example "http://ups.local/examples/SimpleUPS.isValidRegion()_with_isValidAddress().phps" An invalid region
     * @api
     * @since   1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public static function isValidRegion(Address $address)
    {
        return self::_performRegionValidation($address)->isRegionValid();
    }

    /**
     * Get the suggested regions available for an invalid region.
     * Suggested regions are ordered by a "rank" that UPS provides based on estimated
     * accuracy.
     * A suggested region may be flagged as "corrected" and have a quality rating of 1
     * if there are minor mistakes such as a misspelled city or missing information that
     * UPS is able to correct.
     * @see   \SimpleUPS\UPS::isValidRegion()
     *
     * @param Address $address
     *
     * @return \SimpleUPS\RegionValidate\RegionSuggestion[]|null
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public static function getSuggestedRegions(Address $address)
    {
        return self::_performRegionValidation($address)->getSuggestedRegions();
    }

    /**
     * Track a small package by tracking number
     *
     * @param string $trackingNumber
     *
     * @return \SimpleUPS\Track\SmallPackage\Shipment[]|null
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     * @throws \SimpleUPS\Api\NoTrackingInformationException
     * @throws \SimpleUPS\Track\TrackingNumberNotFoundException
     */
    public static function trackByTrackingNumber($trackingNumber)
    {
        if (!is_numeric($trackingNumber{0})) {
            throw new \SimpleUPS\Api\MissingParameterException('Tracking number is invalid, is it a reference number?');
        }

        $request = new Track\SmallPackage\Request();
        $request->setTrackingNumber($trackingNumber);

        return $request->sendRequest()->getShipments();
    }

    /**
     * Determine if provided street address is valid.
     * If an address is invalid while the region is, then the invalid portion of the address is the street.  Also
     * assists with determining if city is misspelled or state doesn't match the postal code.
     * UPS will auto correct certain things about an address.  For example, if the state is "XY"
     * and the zip code is correct, UPS will ignore the state completely.  However, UPS will provide
     * a fully corrected address.  It's advisable you use this address.
     * @see   getCorrectedAddress
     * @see   getSuggestedAddresses
     * @see   isValidRegion
     *
     * @param Address $address
     *
     * @return bool
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public static function isValidAddress(Address $address)
    {
        return self::_performAddressValidation($address)->isAddressValid();
    }

    /**
     * Take an address and provide a corrected, fully valid address
     * UPS will correct a slightly invalid address for you if it is able to determine the real
     * address.  For example, if the state is "XY" and the zip code is correct, UPS will provide
     * a correct address where the state matches the zip code.  It's advisable under all circumstances
     * you use this address when it's provided.
     * It is recommended that you always check the validity of an address before obtaining it's
     * corrected version.
     * @see   isValidAddress
     * @see   getSuggestedAddresses
     *
     * @param Address $address
     *
     * @return \SimpleUPS\AddressValidate\Address|null
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public static function getCorrectedAddress(Address $address)
    {
        return self::_performAddressValidation($address)->getCorrectedAddress();
    }

    /**
     * If a address is invalid, UPS may provide suggestions for what the correct address is.
     * If there are errors in a street address such as the street number not matching an actual
     * address UPS may provide some suggestions.
     * @note  Suggested addresses can sometimes be grouped together.  For instance if you enter a street number that is significantly off from what's available on the street, the suggested addresses will be grouped by the hundred. (ie: 10900-10998 MY STREET DR)
     * @see   isValidAddress
     * @see   getCorrectedAddress
     *
     * @param Address $address
     *
     * @return \SimpleUPS\AddressValidate\Address[]|null
     * @api
     * @since 1.0
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    public static function getSuggestedAddresses(Address $address)
    {
        return self::_performAddressValidation($address)->getSuggestedAddresses();
    }

    /**
     * Perform validation on the provided address if not already performed
     *
     * @param Address $address
     *
     * @internal
     * @return \SimpleUPS\AddressValidate\Request
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    private static function _performAddressValidation(Address $address)
    {
        if (self::$addressValidationResponse === null || self::$addressValidationResponse->getAddress() != $address) {
            $request = new AddressValidate\Request();
            $request->setAddress($address);

            self::$addressValidationResponse = $request->sendRequest();
        }

        return self::$addressValidationResponse;
    }

    /**
     * Perform validation on the provided region of the address if not already performed
     *
     * @param Address $address
     *
     * @internal
     * @return \SimpleUPS\AddressValidate\Request
     * @throws \SimpleUPS\Api\InvalidResponseException
     * @throws \SimpleUPS\Api\RequestFailedException
     * @throws \SimpleUPS\Api\ResponseErrorException
     * @throws \SimpleUPS\Api\MissingParameterException
     */
    private static function _performRegionValidation(Address $address)
    {
        if (self::$regionValidationResponse === null || self::$regionValidationResponse->getAddress() != $address) {
            $request = new RegionValidate\Request();
            $request->setAddress($address);

            self::$regionValidationResponse = $request->sendRequest();
        }

        return self::$regionValidationResponse;
    }

    /**
     * Set the UPS Access License Number, User ID, Password and Account Number.
     * Can also be set with the constants UPS_ACCESSLICENSENUMBER, UPS_USERID, UPS_PASSWORD
     * @see   \SimpleUPS\UPS::getAccountNumber()
     * @see   \SimpleUPS\UPS::getAccessLicenseNumber()
     * @see   \SimpleUPS\UPS::getUserId()
     * @see   \SimpleUPS\UPS::getPassword()
     * @api
     * @since 1.0
     *
     * @param string $accessLicenseNumber
     * @param string $userId
     * @param string $password
     *
     * @throws \Exception
     */
    public static function setAuthentication($accessLicenseNumber, $userId, $password)
    {
        self::setAccessLicenseNumber($accessLicenseNumber);
        self::setUserId($userId);
        self::setPassword($password);
    }

    /**
     * @internal
     *
     * @param $accountNumber
     */
    private static function setAccountNumber($accountNumber)
    {
        self::$accountNumber = $accountNumber;
    }

    /**
     * Get the account number to be used in API requests
     * Can also be set with the constant UPS_ACCOUNTNUMBER
     * @see   \SimpleUPS\UPS::setAuthentication()
     * @see   \SimpleUPS\UPS::getAccessLicenseNumber()
     * @see   \SimpleUPS\UPS::getUserId()
     * @see   \SimpleUPS\UPS::getPassword()
     * @api
     * @since 1.0
     * @return string|null
     */
    public static function getAccountNumber()
    {
        if (self::$accountNumber == null && defined('UPS_ACCOUNTNUMBER')) {
            self::$accountNumber = UPS_ACCOUNTNUMBER;
        }

        return self::$accountNumber;
    }

    /**
     * @param $accessLicenseNumber
     *
     * @internal
     */
    private static function setAccessLicenseNumber($accessLicenseNumber)
    {
        self::$accessLicenseNumber = $accessLicenseNumber;
    }

    /**
     * Get the license number to be used in API requests
     * Can also be set with the constant UPS_ACCESSLICENSENUMBER
     * @see   \SimpleUPS\UPS::setAuthentication()
     * @see   \SimpleUPS\UPS::getAccountNumber()
     * @see   \SimpleUPS\UPS::getUserId()
     * @see   \SimpleUPS\UPS::getPassword()
     * @api
     * @since 1.0
     * @return string|null
     */
    public static function getAccessLicenseNumber()
    {
        if (self::$accessLicenseNumber == null && defined('UPS_ACCESSLICENSENUMBER')) {
            self::$accessLicenseNumber = UPS_ACCESSLICENSENUMBER;
        }

        return self::$accessLicenseNumber;
    }

    /**
     * @param $password
     *
     * @internal
     */
    private static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * Get the password to be used in API requests
     * Can also be set with the constant UPS_PASSWORD
     * @see   \SimpleUPS\UPS::setAuthentication()
     * @see   \SimpleUPS\UPS::getAccountNumber()
     * @see   \SimpleUPS\UPS::getAccessLicenseNumber()
     * @api
     * @since 1.0
     * @return string|null
     */
    public static function getPassword()
    {
        if (self::$password == null && defined('UPS_PASSWORD')) {
            self::$password = UPS_PASSWORD;
        }

        return self::$password;
    }

    /**
     * @param $userId
     *
     * @internal
     */
    private static function setUserId($userId)
    {
        self::$userId = $userId;
    }

    /**
     * Get the user id to be used in API requests
     * Can also be set with the constant UPS_USERID
     * @see   \SimpleUPS\UPS::setAuthentication()
     * @see   \SimpleUPS\UPS::getAccountNumber()
     * @see   \SimpleUPS\UPS::getAccessLicenseNumber()
     * @see   \SimpleUPS\UPS::getPassword()
     * @api
     * @since 1.0
     * @return string|null
     */
    public static function getUserId()
    {
        if (self::$userId == null && defined('UPS_USERID')) {
            self::$userId = UPS_USERID;
        }

        return self::$userId;
    }

    /**
     * Number to use in rate requests
     * Can also be set with the constants UPS_SHIPPERNUMBER
     * @api
     *
     * @param Shipper $shipper
     */
    public static function setShipper(Shipper $shipper)
    {
        self::$shipper = $shipper;
    }

    /**
     * Shipper information used in rate requests
     * @todo  document how to set this via objects & constants
     * @api
     * @since 1.0
     * @return Shipper
     */
    public static function getShipper()
    {
        if (self::$shipper == null) {
            self::$shipper = new Shipper();

            if (self::$shipperNumber == null && defined('UPS_SHIPPER_NUMBER')) {
                self::$shipper->setNumber(UPS_SHIPPER_NUMBER);
            }


            $shipperAddress = new InstructionalAddress();

            if (defined('UPS_SHIPPER_ADDRESSEE')) {
                $shipperAddress->setAddressee(UPS_SHIPPER_ADDRESSEE);
            }

            if (defined('UPS_SHIPPER_STREET')) {
                $shipperAddress->setStreet(UPS_SHIPPER_STREET);
            }

            if (defined('UPS_SHIPPER_ADDRESS_LINE2')) {
                $shipperAddress->setAddressLine2(UPS_SHIPPER_ADDRESS_LINE2);
            }

            if (defined('UPS_SHIPPER_ADDRESS_LINE3')) {
                $shipperAddress->setAddressLine3(UPS_SHIPPER_ADDRESS_LINE3);
            }

            if (defined('UPS_SHIPPER_CITY')) {
                $shipperAddress->setCity(UPS_SHIPPER_CITY);
            }

            if (defined('UPS_SHIPPER_STATEPROVINCE_CODE')) {
                $shipperAddress->setState(UPS_SHIPPER_STATEPROVINCE_CODE);
            }

            if (defined('UPS_SHIPPER_POSTAL_CODE')) {
                $shipperAddress->setPostalCode(UPS_SHIPPER_POSTAL_CODE);
            }

            if (defined('UPS_SHIPPER_COUNTRY_CODE')) {
                $shipperAddress->setCountryCode(UPS_SHIPPER_COUNTRY_CODE);
            }

            self::$shipper->setAddress($shipperAddress);
        }

        return self::$shipper;
    }

    /**
     * Set the currency code to be used
     * @api
     *
     * @param string $currencyCode
     */
    public static function setCurrencyCode($currencyCode)
    {
        self::$currencyCode = $currencyCode;
    }

    /**
     * Get the currency code
     * @since 1.0
     * @return string
     */
    public static function getCurrencyCode()
    {
        if (defined('UPS_CURRENCY_CODE')) {
            self::setCountryCode(UPS_CURRENCY_CODE);
        }

        return self::$currencyCode;
    }

    /**
     * Define debug mode
     * Can also be set with the constant UPS_DEBUG
     * @note  While the library is in debug mode, it uses more memory storing additional information about each request.
     * @see   \SimpleUPS\UPS::getDebugOutput()
     * @api
     * @since 1.0
     *
     * @param bool $debug
     */
    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }

    /**
     * Determine if the library is in debug mode
     * @see   \SimpleUPS\UPS::getDebugOutput()
     * @see   \SimpleUPS\UPS::getDebug()
     * @see   \SimpleUPS\UPS::setDebug()
     * @since 1.0
     * @return bool
     */
    public static function getDebug()
    {
        if (self::$debug == null) {
            self::$debug = false;

            if (defined('UPS_DEBUG')) {
                self::$debug = UPS_DEBUG;
            }
        }

        return !!self::$debug;
    }

    //@todo Add logging method here that could be overridden for frameworks like Yii

    /**
     * Prints debug output
     * @see   \SimpleUPS\UPS::getDebug()
     * @see   \SimpleUPS\UPS::setDebug()
     * @api
     * @since 1.0
     */
    public static function getDebugOutput()
    {
        if (UPS::getDebug()) {
            require_once 'debug_output.inc';
        } else {
            throw new \Exception('Debug mode must be enabled');
        }
    }

    /**
     * Used as the autoloader to load classes as they're needed
     *
     * @param $class
     *
     * @internal
     */
    public static function load($class)
    {
        $path = realpath(
            __DIR__ . str_replace(
                array(
                    __NAMESPACE__,
                    "\\"
                ),
                array(
                    '',
                    DIRECTORY_SEPARATOR
                ),
                $class
            ) . '.php'
        );

        if ($path !== false) {
            require_once $path;
        }
    }
}

spl_autoload_register(__NAMESPACE__ . '\UPS::load');