<?php

return [
    // Default queue:worker options
    'defaults' => [
        'specify-queue' => true, // Should the --queue parameter be used?
        'timeout' => 0, // The timeout for the worker process.
        'sleep' => 10, // The sleep time when there are no jobs.
        'tries' => 5, // The maximum number of tries
    ],

    // Configure the number of processes you want to run per queue or
    // alternatively, define a more in depth configuration.
    'queues' => [
        'default' => 1,
        'another' => [
            'amount' => 1, // The number of processes you want to run.
            'connection' => 'second-connection', // Optional: the connection you'd like to use.
            // Override any of the default options here.
        ],
    ],

    // Should we schedule the ensurer command to run every minute?
    'schedule' => true,
];
