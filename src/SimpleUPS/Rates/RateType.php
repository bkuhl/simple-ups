<?php namespace SimpleUPS\Rates;

/**
 * The type of rate quote to get
 * @todo  elaborate on different types of quotes
 * @since 1.0
 */
class RateType
{
    const
        RATES_WITH_SHIPPER_NUMBER = '00',
        DAILY_RATES = '01',
        RETAIL_RATES = '04',
        STANDARD_RATES = '53';
}