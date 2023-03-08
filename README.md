# seatsio-php, the official Seats.io PHP client library

[![Build](https://github.com/seatsio/seatsio-php/workflows/Build/badge.svg)](https://github.com/seatsio/seatsio-php/actions/workflows/build.yml)
[![Latest Stable Version](https://poser.pugx.org/seatsio/seatsio-php/v/stable)](https://packagist.org/packages/seatsio/seatsio-php)

This is the official PHP client library for the [Seats.io V2 REST API](https://docs.seats.io/docs/api-overview).

## Installing seatsio-php

The recommended way to install seatsio-php is through [Composer](http://getcomposer.org).

```bash
composer require seatsio/seatsio-php
```

The minimum required PHP version is 7.4.

## Versioning

seatsio-php follows semver since v62.3.0.

## Usage

### General instructions

To use this library, you'll need to create a `SeatsioClient`:

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
...
```

You can find your _workspace secret key_ in the [settings section of the workspace](https://app.seats.io/workspace-settings).

The region should correspond to the region of your account:

- `Region::EU()`: Europe
- `Region::NA()`: North-America
- `Region::SA()`: South-America
- `Region::OC()`: Oceania

If you're unsure about your region, have a look at your [company settings page](https://app.seats.io/company-settings).

### Creating a chart and an event

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$chart = $seatsio->charts->create();
$event = $seatsio->events->create($chart->key);
echo 'Created event with key ' . $event->key;
```

### Booking objects

Changes the object status to ‘booked’. Booked seats are not selectable on a rendered chart.

[https://docs.seats.io/docs/api-book-objects](https://docs.seats.io/docs/api-book-objects)

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->book(<AN EVENT KEY>, ["A-1", "A-2"]);
```

### Booking objects that have been held

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->book(<AN EVENT KEY>, ["A-1", "A-2"], <A HOLD TOKEN>);
```

### Booking general admission areas

Either

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->book(<AN EVENT KEY>, ["GA1", "GA1", "GA1"]);
```

Or

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->book(<AN EVENT KEY>, [["objectId" => "GA1", "quantity" => 3]]);
```

### Releasing objects

Changes the object status to ‘free’. Free seats are selectable on a rendered chart.

[https://docs.seats.io/docs/api-release-objects](https://docs.seats.io/docs/api-release-objects)

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->release(<AN EVENT KEY>, ["A-1", "A-2"]);
```

### Changing object status

Changes the object status to a custom status of your choice. If you need more statuses than just booked and free, you can use this to change the status of a seat, table or booth to your own custom status.

[https://docs.seats.io/docs/api-custom-object-status](https://docs.seats.io/docs/api-custom-object-status)

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->events->changeObjectStatus(<AN EVENT KEY>, ["A-1", "A-2"], "unavailable");
```

### Retrieving object category and status (and other information)

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$objectInfos = $seatsio->events->retrieveObjectInfos($event->key, ["A-1", "A-2"]);

print_r($objectInfos["A-1"]->categoryKey)
print_r($objectInfos["A-1"]->categoryLabel)
print_r($objectInfos["A-1"]->status)

print_r($objectInfos["A-2"]->categoryKey)
print_r($objectInfos["A-2"]->categoryLabel)
print_r($objectInfos["A-2"]->status)
```

### Event reports

Want to know which seats of an event are booked, and which ones are free? That’s where reporting comes in handy.

The report types you can choose from are:
- byStatus
- byCategoryLabel
- byCategoryKey
- byLabel
- byOrderId

[https://docs.seats.io/docs/api-event-reports](https://docs.seats.io/docs/api-event-reports)

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);
$seatsio->eventReports->byStatus(<AN EVENT KEY>, <OPTIONAL FILTER>);
```

### Listing all charts

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>);

$charts = $seatsio->charts->listAll();
foreach($charts as $chart) {
    echo 'Chart ' . $chart->key;
}
```

Note: `listAll()` returns an iterator, which under the hood calls the seats.io API to fetch charts page by page. So multiple API calls may be done underneath to fetch all charts.

### Listing charts page by page

E.g. to show charts in a paginated list on a dashboard.

Each page contains an `items` array of charts, and `nextPageStartsAfter` and `previousPageEndsBefore` properties. Those properties are the chart IDs after which the next page starts or the previous page ends.

```php
// ... user initially opens the screen ...

$firstPage = $seatsio->charts->listFirstPage();
foreach($firstPage->items as $chart) {
    echo 'Chart ' . $chart->key;
}
```

```php
// ... user clicks on 'next page' button ...

$nextPage = $seatsio->charts->listPageAfter($firstPage->nextPageStartsAfter);
foreach($nextPage->items as $chart) {
    echo 'Chart ' . $chart->key;
}
```

```php
// ... user clicks on 'previous page' button ...

$previousPage = $seatsio->charts->listPageBefore($nextPage->previousPageEndsBefore);
foreach($page->items as $chart) {
    echo 'Chart ' . $chart->key;
}
```

### Creating a workspace

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

// company admin key can be found on https://app.seats.io/company-settings
$seatsio = new SeatsioClient(Region::EU(), <COMPANY ADMIN KEY>);
$seatsio->workspaces->create("a workspace");
```

### Creating a chart and an event with the company admin key

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

// company admin key can be found on https://app.seats.io/company-settings
// workspace public key can be found on https://app.seats.io/workspace-settings
$seatsio = new SeatsioClient(Region::EU(), <COMPANY ADMIN KEY>, <WORKSPACE PUBLIC KEY>);
$chart = $seatsio->charts->create();
$event = $seatsio->events->create($chart->key);
echo 'Created event with key ' . $event->key;
```

## Error handling

When an API call results in a 4xx or 5xx error (e.g. when a chart could not be found), a SeatsioException is thrown.

This exception contains a message string describing what went wrong, and also two other properties:

- `messages`: an array of error messages that the server returned. In most cases, this array will contain only one element.
- `requestId`: the identifier of the request you made. Please mention this to us when you have questions, as it will make debugging easier.

## Rate limiting - exponential backoff

This library supports [exponential backoff](https://en.wikipedia.org/wiki/Exponential_backoff).

When you send too many concurrent requests, the server returns an error `429 - Too Many Requests`. The client reacts to this by waiting for a while, and then retrying the request.
If the request still fails with an error `429`, it waits a little longer, and try again. By default this happens 5 times, before giving up (after approximately 15 seconds).

We throw a `RateLimitExceededException` (which is a subclass of `SeatsioException`) when exponential backoff eventually fails.

To change the maximum number of retries, create the `SeatsioClient` as follows:

```php
require 'vendor/autoload.php';

use Seatsio\Region;
use Seatsio\SeatsioClient;

$seatsio = new SeatsioClient(Region::EU(), <WORKSPACE SECRET KEY>, null, 3);
```

Passing in 0 disables exponential backoff completely. In that case, the client will never retry a failed request.


## Upgrading

### v82 -> v83
- signature of `$seatsioClient->events->markAsForSale()` and `$seatsioClient->events->markAsNotForSale()` changed. Added new parameter `$areaPlaces`, between `$objects` and `$categories`.
- added `$seatsioClient->charts->listCategories(string $chartKey)` to retrieve an array of `Category` instances for a specified chart.

### v81 -> v82
- removed fields from usage report by month: `$numFirstBookingsOrSelections`, `$numGASelectionsWithoutBooking`, `$numNonGASelectionsWithoutBooking`. Instead
of `$numFirstBookingsOrSelections`, use `$numUsedObjects`.

### v80 -> v81
- oldest supported PHP version is now PHP 7.4

### v79 -> v80
- `$seatsioClient->seasons->createEvents()` returns an array of Event objects now, instead of the Season object

### v76 -> v77

- Renamed `$seatsioClient->events->retrieveObjectStatus()` to `$seatsioClient->events->retrieveObjectInfo()`
- Renamed `\Seatsio\Reports\Events\EventReportItem` to `\Seatsio\Events\EventObjectInfo`
- Renamed `\Seatsio\Events\ObjectStatus` to `\Seatsio\Events\EventObjectInfo`
- Renamed `\Seatsio\Reports\Charts\ChartReportItem` to `\Seatsio\Charts\ChartObjectInfo`
- Renamed `ObjectStatus->quantity` to `EventObjectInfo->numBooked`

### v72 -> v73

The `SeatsioClient` now takes a region as first parameter. This is the region of your account.

### v69 -> v70

Switched to builder pattern for creating social distancing rulesets. Removed constructor of `SocialDistancingRuleset` class.

Rule-based rulesets:

```php
$ruleset = SocialDistancingRuleset::ruleBased("My first ruleset")
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
```

Fixed rulesets:

```php
$ruleset = SocialDistancingRuleset::fixed("My second ruleset")
    ->setIndex(1)
    ->setDisabledSeats(["A-1"])
    ->build();
```

### v68 -> v69

Replaced `SeatsioException->$messages` by `SeatsioException->$errors`. An error contains both a `$code` and a `$message`.

To implement logic based on the exception type, use the techincal `$code` instead of the human-readable `$message`.

### v67 -> v68

#### Retrieving an event

The `Event` class does not have `$bookWholeTables` and `$tableBookingModes` properties anymore. Those are replaced by a single
`$tableBookingConfig` property:

- `$bookWholeTables` equal to `true` corresponds to a `$tableBookingConfig` with `$mode` equal to `ALL_BY_TABLE`
- `$bookWholeTables` equal to `false` corresponds to a `$tableBookingConfig` with `$mode` either `ALL_BY_SEAT`, `INHERIT` or `CUSTOM`
- The list of tables in `$tableBookingModes` is now `$tableBookingConfig->tables` (but only if `$mode` equals `CUSTOM`)

#### Creating an event

When creating an event, you now pass in an (optional) `$tableBookingConfig` instead of `$bookWholeTablesOrTableBookingModes`:

```php
$seatsioClient->events->create(
  "4250fffc-e41f-c7cb-986a-2c5e728b8c28", null,
  TableBookingConfig::custom(["T1" => "BY_TABLE", "T2" => "BY_SEAT"])
);
```

#### Creating multiple events

When creating multiple events, you now pass in an (optional) `$tableBookingConfig` instead of `$bookWholeTables` and `$tableBookingModes`:

```php
$params = [
    (new EventCreationParams())
    ->setEventKey("event34")
    ->setTableBookingConfig(TableBookingConfig::allByTable()),
    (new EventCreationParams())
    ->setEventKey("event35")
    ->setTableBookingConfig(TableBookingConfig::allBySeat())
];

$events = $seatsioClient->events->createMultiple("4250fffc-e41f-c7cb-986a-2c5e728b8c28", $params);
```

### v66 -> v67

No migration needed

### v65 -> v66

You now need to be on PHP 7.1 or newer to use this library.

### v64 -> v65

Added boolean parameter `$oneGroupPerTable` to the constructor of `SocialDistancingRuleset`. Pass in `false` to not force only one group
to sit at a table.

Also added this parameter to `SocialDistancingRuleset::ruleBased()`

### v63 -> v64

Added `$maxOccupancyAbsolute`, `$maxOccupancyPercentage` and `$fixedGroupLayout` to the constructor of `SocialDistancingRuleset`.

To keep the default behaviour, pass in the following:

- `$maxOccupancyAbsolute = 0`
- `$maxOccupancyPercentage = 0`
- `$fixedGroupLayout = false`

### v62 -> v63

`events->bookBestAvailable()`, `events->holdBestAvailable()` and `events->changeBestAvailableObjectStatus()` take optional `$extraData` and
`$ticketTypes` parameters. Pass in `null` to keep the default behaviour.
