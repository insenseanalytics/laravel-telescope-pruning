<?php

return [
    
    /*
     |--------------------------------------------------------------------------
     | Pruning Settings
     |--------------------------------------------------------------------------
     |
     | Telescope pruning is enabled by default. If you wish to enable only in
     | production, set this value to null. Set to false to disable it.
     |
     */
    'enabled' => env('TELESCOPE_PRUNING_ENABLED', true),

    /*
     |--------------------------------------------------------------------------
     | Every Request Pruning
     |--------------------------------------------------------------------------
     |
     | By default, telescope pruning happens on every request. This does not
     | impact the response time as pruning starts after the response has
     | been sent. If you prefer to do this by scheduling a command, you
     | may turn this off by setting it to false.
     |
     */
    'every_request_pruning' => env('TELESCOPE_PRUNE_EVERY_REQUEST', true),

    /*
    |--------------------------------------------------------------------------
    | Pruning Limit
    |--------------------------------------------------------------------------
    |
    | This configuration option determines how many Telescope batches will
    | be kept in storage. This allows you to control the amount of disk
    | space claimed by Telescope's entry storage.
    |
    */
    'limit' => env('TELESCOPE_LIMIT', 100),

    /*
    |--------------------------------------------------------------------------
    | Pruning Whitelist
    |--------------------------------------------------------------------------
    |
    | This option allows you to whitelist specific or monitored tags that
    | must not be pruned. Set the whitelist limit to prune old entries
    | once the whitelist limit is hit. Set to null for no limit.
    |
    | For whitelisted entries, the entire batch will not be pruned.
    |
    */
    'whitelist' => [
        'monitored_tags' => env('TELESCOPE_WHITELIST_MONITORED_TAG_PRUNING', true),
        'specific_tags' => [

        ],
        'whitelist_limit' => env('TELESCOPE_WHITELIST_LIMIT', null),
    ],
];
