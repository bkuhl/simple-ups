# Dispatcher

[<img src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher.png"/>](http://indatus.com/company/careers)


Dispatcher allows you to schedule your artisan commands within your [Laravel](http://laravel.com) project, eliminating the need to touch the crontab when deploying.  It also allows commands to run per environment and keeps your scheduling logic where it should be, in your version control.

---

## README Contents

* [Features](#features)
* [Usage](#usage)
  * [Address Validation](#address-validation)
  * [Region Validation](#region-validation)

<a name="features" />
## Features

 * **Address Validation** - Ensure an address is valid before it's accepted by your application
 * **Address Correction** - If an address is invalid, we'll help you correct it
 * **Track Packages** - See current status, recent activity, delivery requirements (signature, etc.), insurance details and more
 * **Shipping Rates** - Get shipping estimates for packages

<a name="usage" />
## Usage

<a name="address-validation" />
### Address Validation

Validating an address can be useful to ensure an address that a user provides can be shipped to.

```
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

```
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
