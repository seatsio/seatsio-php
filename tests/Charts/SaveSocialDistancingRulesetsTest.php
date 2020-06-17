<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;

class SaveSocialDistancingRulesetsTest extends SeatsioClientTest
{

    public function test()
    {
        $chart = $this->seatsioClient->charts->create();

        $rulesets = [
            "ruleset1" => new SocialDistancingRuleset(0, "My first ruleset", 1, true, 2, 1, ["A-1"], ["A-2"]),
            "ruleset2" => new SocialDistancingRuleset(1, "My second ruleset")
        ];
        $this->seatsioClient->charts->saveSocialDistancingRulesets($chart->key, $rulesets);

        $retrievedChart = $this->seatsioClient->charts->retrieve($chart->key);
        self::assertEquals($rulesets, $retrievedChart->socialDistancingRulesets);
    }

}
