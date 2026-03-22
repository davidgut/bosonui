<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rules Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for generating AI agent rule files (.mdc).
    |
    */
    'rules' => [
        /*
        |----------------------------------------------------------------------
        | Canary Test
        |----------------------------------------------------------------------
        |
        | Inject a verification canary string into the generated file to test
        | context loading. When enabled, adds a comment stating
        | "I am reading the boson.mdc file".
        |
        */
        'canary' => false,
    ],
];
