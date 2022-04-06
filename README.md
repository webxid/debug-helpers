# Description

The lib contains the tools for debug. It doesn't have any external dependencies to other libs or frameworks, so it could be used in any project.

It's helpful for me. Hope, it'll be helpful for someone else 

# Install

Run `composer require webxid/debug-helpers`


# How To Use

## Simple data dump

```php
use function WebXID\DebugHelpers\_dump;
use function WebXID\DebugHelpers\__dump;

_dump($data); // To print data dump into a browser
__dump($data); // To print data dump into a command line
```

## Dump a Backtrace

```php
use function WebXID\DebugHelpers\_backtrace;

_backtrace(); // print a Back trace from a script start file to the currecn place
```


## Get Execution time

```php
use function WebXID\DebugHelpers\_trackTime;

_trackTime('track_magic_time'); // start track of `track_magic_time`

// Do a magic

echo _trackTime('track_magic_time'); // print a `track_magic_time` execution time

echo _trackTime(true); // print the all registered time track keys data
```

## Cache a var data

```php
use function WebXID\DebugHelpers\_cachedLog;

$var = 1;
_cachedLog('$var', $var);

$var = 2;
_cachedLog('$var', $var);

$var = 3;
_cachedLog('$var', $var);

// print cached data of the key `$var` 
print_r(_cachedLog('$var'));
/**
 * will prints the array
 * [
 *      '$var' => [
 *          1,
 *          2,
 *          3,
 *      ],
 * ]
 */
 
 print_r(_cachedLog()); // prints all cached data

```


## Dump data into log file

To dump a data into a log file use the next functions

```php
use WebXID\DebugHelpers\Environment;
use function WebXID\DebugHelpers\_log;
use function WebXID\DebugHelpers\_logWithClean; 
use function WebXID\DebugHelpers\_logAndDie;
use function WebXID\DebugHelpers\_logWithCleanAndDie;

Environment::setRootDir(__DIR__);

_log($data); // Adds Dump of the $data var to the end of log file and continue a script procssing
_logWithClean($data); // Cleaned up the log file, Dumps the $data var and continue a script procssing
_logAndDie($data); // Adds Dump of the $data var to the end of log file and break a script processing
_logWithCleanAndDie($data); // Cleaned up the log file, Adds Dump of the $data var to the end of log file and break a script processing
```

The log file route: `Environment::getRootDir() . '/logs/webxid.log'`.

> !!! Note !!!
> 
> The dir `logs` has to be readable and writable for the script. Otherwise you will get a permission error.