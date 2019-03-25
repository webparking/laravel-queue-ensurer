<?php

return [
    // Configure the number of processes you desire to run per queue
    'queues' => [
        'default' => 1,
    ],
    // Should we schedule the ensurer command to run every minute?
    'schedule' => true,
];
