<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Tests\Time\TimeZone;

use Carbon\CarbonImmutable;
use PhoneBurner\Pinch\Time\TimeZone\TimeZoneCollection;
use PhoneBurner\Pinch\Time\TimeZone\TimeZoneFactory;
use PhoneBurner\Pinch\Time\TimeZone\Tz;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TimeZoneCollectionTest extends TestCase
{
    #[Test]
    public function zeroTimeZoneCaseBehavior(): void
    {
        $collection = TimeZoneCollection::make();
        self::assertEmpty($collection);
        self::assertSame([], [...$collection]);
        self::assertSame('', (string)$collection);
        self::assertEquals($collection, \unserialize(\serialize($collection)));
        self::assertSame($collection, $collection->getTimeZones());

        $now = CarbonImmutable::now();
        self::assertNull($collection->getMinOffsetTimeZone($now));
        self::assertNull($collection->getMaxOffsetTimeZone($now));
        self::assertNull($collection->getEarliestLocalTime($now));
        self::assertNull($collection->getLatestLocalTime($now));

        $this->expectException(\UnderflowException::class);
        $collection->first();
    }

    #[Test]
    public function singleTimeZoneCaseBehavior(): void
    {
        $timezone = TimeZoneFactory::make(Tz::Chicago);
        $collection = TimeZoneCollection::make($timezone);
        self::assertCount(1, $collection);
        self::assertSame([$timezone], [...$collection]);
        self::assertSame(Tz::Chicago->value, (string)$collection);
        self::assertEquals($collection, \unserialize(\serialize($collection)));
        self::assertSame($collection, $collection->getTimeZones());

        $now = CarbonImmutable::now();
        self::assertSame($timezone, $collection->getMinOffsetTimeZone($now));
        self::assertSame($timezone, $collection->getMaxOffsetTimeZone($now));
        self::assertEquals($now->setTimezone($timezone), $collection->getEarliestLocalTime($now));
        self::assertEquals($now->setTimezone($timezone), $collection->getLatestLocalTime($now));
        self::assertSame($timezone, $collection->first());
    }

    #[Test]
    public function multipleTimeZoneCaseBehavior(): void
    {
        $chicago = TimeZoneFactory::make(Tz::Chicago);
        $los_angeles = TimeZoneFactory::make(Tz::LosAngeles);
        $new_york = TimeZoneFactory::make(Tz::NewYork);
        $denver = TimeZoneFactory::make(Tz::Denver);

        $collection = TimeZoneCollection::make(
            $chicago,
            $chicago,
            $los_angeles,
            $los_angeles,
            $denver,
            $denver,
            $denver,
            $denver,
            $denver,
            $new_york,
            $new_york,
            $chicago,
        );

        self::assertCount(4, $collection);
        self::assertSame([
            $chicago,
            $los_angeles,
            $denver,
            $new_york,
        ], [...$collection]);
        self::assertSame(\sprintf('%s&%s&%s&%s', ...[
            Tz::Chicago->value,
            Tz::Denver->value,
            Tz::LosAngeles->value,
            Tz::NewYork->value,
        ]), (string)$collection);
        self::assertEquals($collection, \unserialize(\serialize($collection)));
        self::assertSame($collection, $collection->getTimeZones());

        $now = CarbonImmutable::now();
        self::assertSame($los_angeles, $collection->getMinOffsetTimeZone($now));
        self::assertSame($new_york, $collection->getMaxOffsetTimeZone($now));
        self::assertEquals($now->setTimezone($los_angeles), $collection->getEarliestLocalTime($now));
        self::assertEquals($now->setTimezone($new_york), $collection->getLatestLocalTime($now));
        self::assertSame($chicago, $collection->first());
    }

    #[Test]
    public function fullNanpTimezoneListBehavior(): void
    {
        $collection = TimeZoneFactory::collect(...[
            Tz::Adak,
            Tz::Anchorage,
            Tz::Barbados,
            Tz::Chicago,
            Tz::Denver,
            Tz::Edmonton,
            Tz::FortNelson,
            Tz::GrandTurk,
            Tz::Halifax,
            Tz::Jamaica,
            Tz::LosAngeles,
            Tz::NewYork,
            Tz::Panama,
            Tz::Phoenix,
            Tz::PuertoRico,
            Tz::Regina,
            Tz::SantoDomingo,
            Tz::StJohns,
            Tz::Toronto,
            Tz::Vancouver,
            Tz::Winnipeg,
            Tz::Bermuda,
            Tz::Guam,
            Tz::Honolulu,
            Tz::PagoPago,
        ]);

        self::assertCount(25, $collection);

        $now = CarbonImmutable::now();
        self::assertSame(TimeZoneFactory::make(Tz::PagoPago->value), $collection->getMinOffsetTimeZone($now));
        self::assertSame(TimeZoneFactory::make(Tz::Guam->value), $collection->getMaxOffsetTimeZone($now));
        self::assertEquals($now->setTimezone(Tz::Guam->value), $collection->getEarliestLocalTime($now));
        self::assertEquals($now->setTimezone(Tz::Halifax->value), $collection->getLatestLocalTime($now));
        self::assertEquals($now->setTimezone(Tz::Bermuda->value), $collection->getLatestLocalTime($now));
        self::assertSame(TimeZoneFactory::make(Tz::Adak->value), $collection->first());
    }
}
