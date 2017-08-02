<?php

$testcase = array(
    array(
        'status' => 'success',
        'points' => [["22.372081", "114.107877"], ["22.326442", "114.167811"], ["22.284419", "114.159510"]]
    ),
    array(
        'status' => 'success',
        'points' => [["22.41483717937219", "114.14794921875"], ["22.37928564733929", "114.23240661621094"], ["22.20711346718832", "114.24476623535156"]]
    ),
    array(
        'status' => 'success',
        'points' => [["22.3720", "114.1078"], ["22.2844", "114.1265"], ["22.2849", "114.1510"], ["22.2844", "114.1695"], ["22.3264", "114.1678"]]
    ),
    array(
        'error' => 'WAYPOINT_MIN',
        'points' => [["22.372081", "114.107877"]]
    ),
    array(
        'error' => 'WAYPOINT_FORMAT_ERROR',
        'points' => [["22.41483717937219", "114.14794921875"], ["22.37928564733929"]]
    ),
    array(
        'error' => 'WAYPOINT_MAX',
        'points' => [["22.372081", "114.107877"], ["22.372081", "114.107877"], ["22.372081", "114.107877"], ["22.326442", "114.167811"], ["22.284419", "114.159510"],
            ["22.326442", "114.167811"], ["22.284419", "114.159510"], ["22.37928564733929", "114.23240661621094"], ["22.20711346718832", "114.24476623535156"],
            ["22.41483717937219", "114.14794921875"], ["22.379286", "114.23240661621094"]]
    ),
    array(
        'status' => 'failure',
        'error' => 'NO_ROUTE_PROVIDED',
        'points' => [["22.41483717937219", "114.14794921875"], ["28.37928564733929", "117.159510"]]
    ),
    array(
        'status' => 'failure',
        'error' => 'NO_ROUTE_PROVIDED',
        'points' => [["22.273847411104626", "114.08889770507812"], ["22.262488997034357", "114.0860652923584"], ["22.25335393931268", "114.08589363098145"]]
    )
);

