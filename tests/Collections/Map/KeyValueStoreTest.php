<?php

declare(strict_types=1);

namespace PhoneBurner\Pinch\Tests\Collections\Map;

use PhoneBurner\Pinch\Collections\Map\KeyValueStore;
use PhoneBurner\Pinch\Container\Exception\NotFound;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KeyValueStoreTest extends TestCase
{
    #[Test]
    public function itCanSetAndGetValues(): void
    {
        $store = new KeyValueStore();

        $store->set('key', 'value');

        self::assertTrue($store->has('key'));
        self::assertSame('value', $store->get('key'));
        self::assertSame('value', $store->find('key'));
        self::assertTrue($store->contains('value'));

        self::assertFalse($store->has('non-existent-key'));
        self::assertFalse($store->contains('non-existent-value'));
        self::assertNull($store->find('non-existent-key'));

        $this->expectException(NotFound::class);
        self::assertNull($store->get('non-existent-key'));
    }

    #[Test]
    public function itCanUnsetAValue(): void
    {
        $store = new KeyValueStore(['key' => 'value']);
        self::assertTrue($store->has('key'));

        $store->unset('key');

        self::assertFalse($store->has('key'));
    }

    #[Test]
    public function itCanRememberAValueThatDoesNotExist(): void
    {
        $store = new KeyValueStore();

        $result = $store->remember('key', static fn(): string => 'computed-value');

        self::assertSame('computed-value', $result);
        self::assertTrue($store->has('key'));
        self::assertSame('computed-value', $store->get('key'));
    }

    #[Test]
    public function itCanRememberAValueThatExists(): void
    {
        $store = new KeyValueStore(['key1' => 'value1', 'key2' => 'value2']);
        self::assertTrue($store->has('key2'));

        $result = $store->remember('key2', static fn(): string => 'computed-value');

        self::assertSame('value2', $result);
    }

    #[Test]
    public function itCanForgetAValue(): void
    {
        $store = new KeyValueStore(['key' => 'value']);
        self::assertTrue($store->has('key'));

        $result = $store->forget('key');

        self::assertSame('value', $result);
        self::assertFalse($store->has('key'));
    }

    #[Test]
    public function itCanClearAllValues(): void
    {
        $store = new KeyValueStore(['key1' => 'value1', 'key2' => 'value2']);
        self::assertSame(['key1' => 'value1', 'key2' => 'value2'], $store->toArray());
        self::assertFalse($store->isEmpty());
        self::assertCount(2, $store);

        $store->clear();

        self::assertSame([], $store->toArray());
        self::assertTrue($store->isEmpty());
        self::assertEmpty($store);
    }

    #[Test]
    public function itCanIterateOverValues(): void
    {
        $store = new KeyValueStore(['key1' => 'value1', 'key2' => 'value2']);

        $values = [];
        foreach ($store as $key => $value) {
            $values[$key] = $value;
        }

        self::assertSame(['key1' => 'value1', 'key2' => 'value2'], $values);
    }

    #[Test]
    public function itCanGetAllKeys(): void
    {
        $store = new KeyValueStore(['key1' => 'value1', 'key2' => 'value2']);

        $keys = $store->keys();

        self::assertSame(['key1', 'key2'], $keys);
    }

    #[Test]
    public function itCanFilterData(): void
    {
        $store = new KeyValueStore(['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 0]);

        $store->filter(static fn($value): bool => $value > 1);

        self::assertSame(['key2' => 2, 'key3' => 3], $store->toArray());
    }

    #[Test]
    public function itCanRejectData(): void
    {
        $store = new KeyValueStore(['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 0]);

        $store->reject(static fn($value): bool => $value > 1);

        self::assertSame(['key1' => 1, 'key4' => 0], $store->toArray());
    }

    #[Test]
    public function itCanMapData(): void
    {
        $store = new KeyValueStore(['key1' => 1, 'key2' => 2]);

        /** @phpstan-ignore argument.type */
        $mapped = $store->map(static fn(int $value): int => $value * 2);

        self::assertSame(['key1' => 2, 'key2' => 4], $mapped->toArray());
    }

    #[Test]
    public function itCanAllData(): void
    {
        $store = new KeyValueStore(['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 0]);

        $result = $store->all(static fn($value): bool => $value > 0);

        self::assertFalse($result);

        $result = $store->all(static fn($value): bool => $value < 5);

        self::assertTrue($result);
    }

    #[Test]
    public function itCanAnyData(): void
    {
        $store = new KeyValueStore(['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 0]);

        $result = $store->any(static fn($value): bool => $value > 3);

        self::assertFalse($result);

        $result = $store->any(static fn($value): bool => $value > 2);

        self::assertTrue($result);
    }

    #[Test]
    public function itCanSerializeAndUnserialize(): void
    {
        $store = new KeyValueStore(['key' => 'value']);

        $serialized = \serialize($store);
        $unserialized = \unserialize($serialized);

        self::assertInstanceOf(KeyValueStore::class, $unserialized);
        self::assertSame(['key' => 'value'], $unserialized->toArray());
    }

    #[Test]
    public function itCanCheckIfEmpty(): void
    {
        $empty_store = new KeyValueStore();
        $non_empty_store = new KeyValueStore(['key' => 'value']);

        self::assertTrue($empty_store->isEmpty());
        self::assertFalse($non_empty_store->isEmpty());
    }

    #[Test]
    public function itHasArrayAccessBehavior(): void
    {
        $store = new KeyValueStore();

        $store['key'] = 'value';

        self::assertArrayHasKey('key', $store);
        self::assertArrayNotHasKey('key2', $store);
        self::assertSame('value', $store['key']);
        self::assertNull($store['non-existent-key']);

        unset($store['key']);
        unset($store['non-existent-key']);

        self::assertArrayNotHasKey('key', $store);
    }
}
