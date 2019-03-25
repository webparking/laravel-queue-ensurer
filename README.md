<h1 align="center">
  Laravel Queue Ensurer
</h1>

<p align="center">
    <a href="https://travis-ci.org/webparking/laravel-queue-ensurer">
        <img src="https://travis-ci.org/webparking/laravel-queue-ensurer.svg?branch=master" alt="Build Status">
    </a> 
    <a href="https://scrutinizer-ci.com/g/webparking/laravel-queue-ensurer/?branch=master">
        <img src="https://scrutinizer-ci.com/g/webparking/laravel-queue-ensurer/badges/quality-score.png?b=master" alt="Quality score">
    </a> 
    <a href="https://scrutinizer-ci.com/g/webparking/laravel-queue-ensurer/?branch=master">
        <img src="https://scrutinizer-ci.com/g/webparking/laravel-queue-ensurer/badges/coverage.png?b=master" alt="Code coverage">
    </a> 
</p>

This package provides a command (`queue:ensure-processes`) to allow running the Laravel queue worker (`queue:work`) from the Laravel schedule. This enables a cronjob to ensure that configured queue workers are running. It eliminates the need for a process manager like [supervisord](http://supervisord.org/), which is not available in all production environments (like when working with DirectAdmin or most other server control panels).

Multiple queues can be configured and the number of desired processes can be configured per queue (which makes it possible to run multiple jobs in parallel). Doing so, allows having the queue configuration in your project's codebase.

This package is doesn't care about which queue driver(s) you use and `queue:restart` still works as normal.

## Installation
```
composer require webparking/laravel-queue-ensurer
```

By default, the `queue:ensure-processes` command is configured to run once a minute, ensuring one worker for the default queue. So if that's all you desire, you're good to go.

## Configuration
You can publish the configuration file to your project by running `php artisan vendor:publish --provider="Webparking\QueueEnsurer\ServiceProvider" --tag="config"`.

```php
return [
    // Configure the number of processes you desire to run per queue
    'queues' => [
        'default' => 1,
    ],
    // Should we schedule the ensurer command to run every minute?
    'schedule' => true,
];
```

## Working
The queue ensurer works by keeping a cache of process id's (PID's) it has started. Every time the ensurer runs, it does this:

1. Remove any PID's of stopped processes from the cache.
   These processes may have been stopped by a server reboot, `queue:restart` or for any other reason.
2. Kill processes belonging to no longer configured queue's and remove their PID's from the cache.
   When a queue was configured to have processes before, but is not configured now.
3. Kill processes that are no longer required and remove their PID's from the cache.
   When the number of configured processes is lower than the number of running processes.
4. Start new processes and add their PID's to the cache.
   When the number of congitured processes is higher than the number of running processes.

This means that the ensurer will not take in account any processes it has not started itself.

For the PID cache, the ensurer uses a JSON file (`storage/app/queue-listener-pids.json`) instead of the Laravel cache mechanism. If the queue ensurer were to use the Laravel cache and the cache were to be cleared (`php artisan cache:clear`), the running processes would not be known to the ensurer any longer. Resulting in it starting new processes, without every killing the old ones.

## Contribution and development
We're happy to receive pull requests or issues.

When developing, you can run `composer test` to execute all code quality checks and tests.

## Future features
These are features we may add. We don't have a specific need for them now, but we acknowledge their usefulness and we will add them when we have some down time. Should you or your project require one or more of these future features earlier, please submit a PR or create an issue.

* Add compatibility with more Laravel versions (only supporting 5.5 now)
* Configurable options for `queue:work`.
* Testing compatibility with Lumen
* Adding PHP 7.3 compatibility

## Licence and Postcardware

This software is open source and licensed under the [MIT license](LICENSE.md).

If you use this software in your daily development we would appreciate to receive a postcard of your hometown. 

Please send it to: Webparking BV, Cypresbaan 31a, 2908 LT Capelle aan den IJssel, The Netherlands
