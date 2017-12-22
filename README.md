# seatsio-php, the official Seats.io PHP client library

[![Build Status](https://travis-ci.org/seatsio/seatsio-php.svg?branch=master)](https://travis-ci.org/seatsio/seatsio-php)

This is the official PHP client library for the [Seats.io V2 REST API](https://www.seats.io/docs/api-v2).

## Installing seatsio-php

The recommended way to install seatsio-php is through [Composer](http://getcomposer.org).

```bash
composer require seatsio/seatsio-php
```

The minimum required PHP version is 5.5.

## Examples

### Creating a chart and an event

```php
$seatsio = new \Seatsio\SeatsioClient(<SECRET KEY>); // can be found on https://app.seats.io/settings
$chart = $seatsio->charts()->create();
$event = $seatsio->events()->create($chart->key);
echo 'Created event with key ' . $event->key;
```

### Booking objects

```php
$seatsio = new \Seatsio\SeatsioClient(<SECRET KEY>);
$seatsio->events()->book(<AN EVENT KEY>, ["A-1", "A-2"]);
```

### Releasing objects

```php
$seatsio = new \Seatsio\SeatsioClient(<SECRET KEY>);
$seatsio->events()->release(<AN EVENT KEY>, ["A-1", "A-2"]);
```

### Booking objects that have been held

```php
$seatsio = new \Seatsio\SeatsioClient(<SECRET KEY>);
$seatsio->events()->book(<AN EVENT KEY>, ["A-1", "A-2"], <A HOLD TOKEN>);
```

### Listing charts

```php
$seatsio = new \Seatsio\SeatsioClient(<SECRET KEY>);

$chart1 = $seatsio->charts()->create();
$chart2 = $seatsio->charts()->create();
$chart3 = $seatsio->charts()->create();

$charts = $seatsio->charts()->lister()->all();
foreach($charts as $chart) {
    echo 'Chart ' . $chart->key;
}
```