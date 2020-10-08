<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class SaveSocialDistancingRulesetsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $rulesets = [
            "ruleset1" => new SocialDistancingRuleset(0, "My first ruleset", 1, true, 2, 1, 10, 0, false, ["A-1"], ["A-2"]),
            "ruleset2" => new SocialDistancingRuleset(1, "My second ruleset")
        ];
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chart->key, $rulesets);

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals($rulesets, $retrievedChart->socialDistancingRulesets);
    }

    public function testFixed()
    {
        $chart = $this->seatsioClient->charts->create();

        $this->seatsioClient->charts->saveSocialDistancingRulesets($chart->key, [
            "ruleset1" => SocialDistancingRuleset::fixed(0, "My first ruleset", ["A-1"])
        ]);

        $rulesets = [
            "ruleset1" => new SocialDistancingRuleset(0, "My first ruleset", 0, false, 0, 0, 0, 0, true, ["A-1"], []),
        ];
        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals($rulesets, $retrievedChart->socialDistancingRulesets);
    }

    public function testRuleBased()
    {
        $chart = $this->seatsioClient->charts->create();

        $this->seatsioClient->charts->saveSocialDistancingRulesets($chart->key, [
            "ruleset1" => SocialDistancingRuleset::ruleBased(0, "My first ruleset", 1, true, 2, 1, 10, 0, ["A-1"], ["A-2"])
        ]);

        $rulesets = [
            "ruleset1" => new SocialDistancingRuleset(0, "My first ruleset", 1, true, 2, 1, 10, 0, false, ["A-1"], ["A-2"]),
        ];
        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals($rulesets, $retrievedChart->socialDistancingRulesets);
    }

}
