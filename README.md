# SimpleUPS

An easy to use PHP UPS Library for tracking, rates and address validation

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bkuhl/simple-ups/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bkuhl/simple-ups/?branch=master)

## README Contents

* [Features](#features)
* [Installation](#installation)
* [Usage](#usage)
  * [Address Validation](#address-validation)
  * [Region Validation](#region-validation)
  * [Tracking Shipments](#tracking-shiments)
  * [Fetching Rates](#fetching-rates)

<a name="features" />
## Features

 * **Address Validation** - Ensure an address is valid before it's accepted by your application
 * **Address Correction** - If an address is invalid, we'll help you correct it
 * **Track Packages** - See current status, recent activity, delivery requirements (signature, etc.), insurance details and more
 * **Shipping Rates** - Get shipping estimates for packages

<a name="installation" />
## Installation

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"bkuhl/simple-ups": "dev-master"
````

Then run `composer update`.

<a name="usage" />
## Usage

SimpleUPS is currently only available in a static context with the following methods:

 * SimpleUPS::getRates()
 * SimpleUPS::isValidRegion()
 * SimpleUPS::getSuggestedRegions()
 * SimpleUPS::trackByTrackingNumber()
 * SimpleUPS::isValidAddress()
 * SimpleUPS::getCorrectedAddress()
 * SimpleUPS::getSuggestedAddresses()
 * SimpleUPS::setAuthentication()
 * SimpleUPS::getAccountNumber()
 * SimpleUPS::getAccessLicenseNumber()
 * SimpleUPS::getPassword()
 * SimpleUPS::getUserId()
 * SimpleUPS::setShipper()
 * SimpleUPS::getShipper()
 * SimpleUPS::setCurrencyCode()
 * SimpleUPS::setDebug()
 * SimpleUPS::getDebugOutput()

<a name="address-validation" />
### Address Validation

Validating an address can be useful to ensure an address that a user provides can be shipped to.

```php
$address = new Address();
$address->setStreet('1001 North Alameda Street');
$address->setStateProvinceCode('CA');
$address->setCity('Los Angeles');
$address->setPostalCode(90012);
$address->setCountryCode('US');
 
try {
    var_dump(UPS::isValidAddress($address)); // true
} catch(Exception $e) {
    //unable to validate address
}
```

<a name="region-validation" />
### Region Validation

If an address fails, validating the region can help you determine if the city, state and zip is valid even if the street address isn't.

```php
$address = new Address();
$address->setStreet('xx North Alameda Street');
$address->setStateProvinceCode('CA');
$address->setCity('Los Angeles');
$address->setPostalCode(90012);
$address->setCountryCode('US');
 
try {
    if (!UPS::isValidAddress($address))
        var_dump(UPS::isValidRegion($address)); // true
} catch(Exception $e) {
    //unable to validate region or address
}
```

<a name="tracking-shipments" />
### Tracking Shipments

Tracking numbers may contain multiple shipments, and shipments may contain multiple packages, and activity is associated with packages.

```php
try {
    /* @var $shipment \SimpleUPS\Track\SmallPackage\Shipment */
    foreach (UPS::trackByTrackingNumber('1Z4861WWE194914215') as $shipment)
        foreach ($shipment->getPackages() as $package)
            foreach ($package->getActivity() as $activity)
                if ($activity->getStatusType()->isDelivered())
                    echo 'DELIVERED';
} catch (TrackingNumberNotFoundException $e) {
    //Tracking number does not exist
} catch (Exception $e) {
    //Unable to track package
}
 
var_dump(UPS::isValidAddress($address)); // false
```

<a name="fetching-rates" />
### Fetching Rates

```php
try {
    //set shipper
    $fromAddress = new \SimpleUPS\InstructionalAddress();
    $fromAddress->setAddressee('Mark Stevens');
    $fromAddress->setStreet('10571 Pico Blvd');
    $fromAddress->setStateProvinceCode('CA');
    $fromAddress->setCity('Los Angeles');
    $fromAddress->setPostalCode(90064);
    $fromAddress->setCountryCode('US');
 
    $shipper = new \SimpleUPS\Shipper();
    $shipper->setNumber('xxxxxxx');
    $shipper->setAddress($fromAddress);
 
    UPS::setShipper($shipper);
 
    //define a shipping destination
    $shippingDestination = new \SimpleUPS\InstructionalAddress();
    $shippingDestination->setStreet('220 Bowery');
    $shippingDestination->setStateProvinceCode('NY');
    $shippingDestination->setCity('New York');
    $shippingDestination->setPostalCode(10453);
    $shippingDestination->setCountryCode('US');
 
    //define a package, we could specify the dimensions of the box if we wanted a more accurate estimate
    $package = new \SimpleUPS\Rates\Package();
    $package->setWeight('7');
 
    $shipment = new \SimpleUPS\Rates\Shipment();
    $shipment->setDestination($shippingDestination);
    $shipment->addPackage($package);
 
    echo 'Rates: ';
 
    echo '<ul>';
        foreach (UPS::getRates($shipment) as $shippingMethod)
            echo '<li>'.$shippingMethod->getService()->getDescription().' ($'.$shippingMethod->getTotalCharges().')</li>';
 
    echo '</ul>';
 
} catch (Exception $e) {
    //doh, something went wrong
    echo 'Failed: ('.get_class($e).') '.$e->getMessage().'<br/>';
    echo 'Stack trace:<br/><pre>'.$e->getTraceAsString().'</pre>';
}
```
