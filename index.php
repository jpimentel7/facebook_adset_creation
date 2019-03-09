<?php

use FacebookAds\Api;
use FacebookAds\Http\Exception\RequestException;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\TargetingFields;
use FacebookAds\Object\Search\TargetingSearchTypes;
use FacebookAds\Object\Targeting;
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;

require 'vendor/autoload.php';
require 'FacebookSettings.php';

class FacebookAdSetCreation
{

    public static function main()
    {
        $facebookSettings = new FacebookSettings();

        // The facebook api has to be initiated before use
        Api::init($facebookSettings->getAppId(), $facebookSettings->getAppSecret(), $facebookSettings->getUserToken());

        $facebookCampaignId = "INSERT CAMPAIGN ID HERE";
        $audienceId = self::findInterestTargeting('php');
        $cityId = self::findCityTargeting('los angeles');
        $schoolId = self::findCollegeTargeting('California State University, Northridge');
        $minAge = 21;

        $adset = new AdSet(null, $facebookSettings->getDefaultAdAccount());
        $adset->setData([
            AdSetFields::NAME => 'Targeting Demo Ad Set',
            AdSetFields::OPTIMIZATION_GOAL => AdSetOptimizationGoalValues::REACH,
            AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
            AdSetFields::BID_AMOUNT => 2,
            AdSetFields::DAILY_BUDGET => 1000,
            AdSetFields::CAMPAIGN_ID => $facebookCampaignId,
            AdSetFields::TARGETING => (new Targeting())->setData([
                TargetingFields::GEO_LOCATIONS => [
                    'cities' => [
                        [
                            'key' => $cityId,
                            'radius' => 12,
                            'distance_unit' => 'mile'
                        ]
                    ],
                ],
                TargetingFields::INTERESTS => [
                    [
                        'id' => $audienceId
                    ]
                ],
                TargetingFields::EDUCATION_SCHOOLS => [
                    [
                        'id' => $schoolId
                    ]
                ],
                TargetingFields::AGE_MIN => $minAge
            ])
        ]);

        try {
            $adset->validate([AdSet::STATUS_PARAM_NAME => AdSet::STATUS_ACTIVE]);
        } catch (RequestException $e) {
            // The response has additional information about why the request failed.
            $response = json_decode($e->getResponse()->getBody(), true);
            var_dump($response);
        }
    }

    /**
     * @link https://developers.facebook.com/docs/marketing-api/targeting-search#interests
     * @param $query
     * @return null
     */
    public static function findInterestTargeting($query)
    {
        $result = TargetingSearch::search(TargetingSearchTypes::INTEREST, null, $query);
        $response = json_decode($result->getLastResponse()->getBody(), true);

        /*
         array(6) {
          ["id"]=>
                string(13) "6003017204650"
          ["name"]=>
                string(3) "PHP"
          ["audience_size"]=>
                int(553384620)
          ["path"]=>
              array(3) {
                [0]=>
                    string(9) "Interests"
                [1]=>
                    string(20) "Additional Interests"
                [2]=>
                    string(3) "PHP"
              }
          ["description"]=>
                NULL
          ["topic"]=>
                 string(10) "Technology"
        }
         */
        var_dump($response['data'][0]);
        return $response['data'][0]['id'];
    }

    /**
     * @link https://developers.facebook.com/docs/marketing-api/targeting-search#geo
     * @param $query
     * @return mixed
     */
    public static function findCityTargeting($query)
    {

        // Can be country, country_group, region, city, zip, geo_market, or electoral_district
        $locationType = ['city'];

        $result = TargetingSearch::search(
            TargetingSearchTypes::GEOLOCATION,
            null,
            $query,
            [
                'location_types' => $locationType
            ]
        );

        $response = json_decode($result->getLastResponse()->getBody(), true);

        /*
         array(9) {
          ["key"]=>
                string(7) "2420379"
          ["name"]=>
                string(11) "Los Angeles"
          ["type"]=>
                string(4) "city"
          ["country_code"]=>
                string(2) "US"
          ["country_name"]=>
                string(13) "United States"
          ["region"]=>
                string(10) "California"
          ["region_id"]=>
                int(3847)
          ["supports_region"]=>
                bool(true)
          ["supports_city"]=>
                bool(true)
        }
         */
        var_dump($response['data'][0]);
        return $response['data'][0]['key'];
    }

    /**
     * @link https://developers.facebook.com/docs/marketing-api/targeting-search#demo
     * @param $query
     * @return mixed
     */
    public static function findCollegeTargeting($query)
    {
        $result = TargetingSearch::search(
            TargetingSearchTypes::EDUCATION,
            null,
            $query);

        $response = json_decode($result->getLastResponse()->getBody(), true);

        /*
        array(4) {
            ["id"]=>
                string(10) "6684699242"
            ["name"]=>
                string(39) "California State University, Northridge"
            ["coverage"]=>
                int(697570)
            ["subtext"]=>
                string(22) "Northridge, California"
        }
        */
        var_dump($response['data'][0]);
        return $response['data'][0]['id'];
    }
}

FacebookAdSetCreation::main();