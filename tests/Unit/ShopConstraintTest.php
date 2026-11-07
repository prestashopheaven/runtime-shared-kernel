<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @author    prestahaven.com
 * @copyright Since 2026 George van Engers
 * @license   https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace PrestaShopHeaven\Runtime\SharedKernel\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PrestaShopHeaven\Runtime\SharedKernel\ShopConstraint;

/**
 * ShopConstraint: one shop, one group or all shops, never an invalid id (ARCH-13).
 */
final class ShopConstraintTest extends TestCase
{
    public function testNamedConstructors(): void
    {
        self::assertSame(3, ShopConstraint::shop(3)->getShopId());
        self::assertNull(ShopConstraint::shop(3)->getShopGroupId());
        self::assertSame(2, ShopConstraint::shopGroup(2)->getShopGroupId());
        self::assertTrue(ShopConstraint::allShops()->isAllShops());
        self::assertFalse(ShopConstraint::shop(1)->isAllShops());
        self::assertTrue(ShopConstraint::shop(1)->equals(ShopConstraint::shop(1)));
        self::assertFalse(ShopConstraint::shop(1)->equals(ShopConstraint::shopGroup(1)));
    }

    /**
     * @return array<string, array{ShopConstraint, int, int, bool}>
     */
    public static function coverage(): array
    {
        return [
            'own shop' => [ShopConstraint::shop(2), 2, 1, true],
            'other shop of the same group' => [ShopConstraint::shop(2), 1, 1, false],
            'shop of the group' => [ShopConstraint::shopGroup(1), 2, 1, true],
            'shop of another group' => [ShopConstraint::shopGroup(1), 3, 2, false],
            'all shops' => [ShopConstraint::allShops(), 3, 2, true],
            'all shops, unknown group' => [ShopConstraint::allShops(), 3, 0, true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('coverage')]
    public function testCovers(ShopConstraint $constraint, int $shopId, int $groupId, bool $expected): void
    {
        self::assertSame($expected, $constraint->covers($shopId, $groupId));
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidIds(): array
    {
        return ['zero' => [0], 'negative' => [-1], 'minimum int' => [PHP_INT_MIN]];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidIds')]
    public function testShopRejectsAnInvalidShopId(int $id): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ShopConstraint::shop($id);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidIds')]
    public function testShopGroupRejectsAnInvalidShopGroupId(int $id): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ShopConstraint::shopGroup($id);
    }

    public function testGetShopIdAndGetShopGroupIdOfAllShopsAreNull(): void
    {
        self::assertNull(ShopConstraint::allShops()->getShopId());
        self::assertNull(ShopConstraint::allShops()->getShopGroupId());
        self::assertNull(ShopConstraint::shopGroup(4)->getShopId());
    }
}
