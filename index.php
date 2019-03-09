<?php

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Search\TargetingSearchTypes;
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Values\CampaignObjectiveValues;
use FacebookAds\Object\Values\InsightsOperators;

require 'vendor/autoload.php';
require 'FacebookSettings.php';

class FacebookAdSetCreation
{

    /**
     * https://developers.facebook.com/docs/marketing-api/targeting-search#interests
     */
    public static function findInterestTargeting()
    {
        $result = TargetingSearch::search(TargetingSearchTypes::INTEREST, null, 'php');
        var_dump($result);
    }
}

FacebookAdSetCreation::findInterestTargeting();