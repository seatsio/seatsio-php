<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class SaveSocialDistancingRulesetsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $ruleset1 = SocialDistancingRuleset::ruleBased("My first ruleset")
            ->setIndex(0)
            ->setNumberOfDisabledSeatsToTheSides(1)
            ->setDisableSeatsInFrontAndBehind(true)
            ->setDisableDiagonalSeatsInFrontAndBehind(true)
            ->setNumberOfDisabledAisleSeats(2)
            ->setMaxGroupSize(1)
            ->setMaxOccupancyAbsolute(10)
            ->setOneGroupPerTable(true)
            ->setDisabledSeats(["A-1"])
            ->setEnabledSeats(["A-2"])
            ->build();

        $ruleset2 = SocialDistancingRuleset::fixed("My second ruleset")
            ->setIndex(1)
            ->setDisabledSeats(["A-1"])
            ->build();

        $rulesets = ["ruleset1" => $ruleset1, "ruleset2" => $ruleset2];
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chart->key, $rulesets);

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals($rulesets, $retrievedChart->socialDistancingRulesets);
    }

}
