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

namespace PrestaShopHeaven\Runtime\SharedKernel;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Which shops a command or query is about: one shop, one shop group or all shops (ARCH-13).
 * The domain's own version of PrestaShop's ShopConstraint, so Domain and Application stay free of
 * PrestaShop classes; ShopConstraintMapper of prestashopheaven/runtime-multistore converts it.
 */
final class ShopConstraint
{
    /**
     * Use the named constructors shop(), shopGroup() and allShops().
     */
    private function __construct(
        private readonly ?int $shopId,
        private readonly ?int $shopGroupId,
    ) {
    }

    /**
     * One shop.
     *
     * @throws \InvalidArgumentException when the id is not positive
     */
    public static function shop(int $shopId): self
    {
        if ($shopId <= 0) {
            throw new \InvalidArgumentException(sprintf('Shop id must be positive, %d given.', $shopId));
        }

        return new self($shopId, null);
    }

    /**
     * One shop group (the shops of the group without an own value inherit it).
     *
     * @throws \InvalidArgumentException when the id is not positive
     */
    public static function shopGroup(int $shopGroupId): self
    {
        if ($shopGroupId <= 0) {
            throw new \InvalidArgumentException(sprintf('Shop group id must be positive, %d given.', $shopGroupId));
        }

        return new self(null, $shopGroupId);
    }

    /**
     * All shops (the global value).
     */
    public static function allShops(): self
    {
        return new self(null, null);
    }

    /**
     * The shop id, or null for a group or all shops.
     */
    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    /**
     * The shop group id, or null for a shop or all shops.
     */
    public function getShopGroupId(): ?int
    {
        return $this->shopGroupId;
    }

    /**
     * True for all shops (the global value).
     */
    public function isAllShops(): bool
    {
        return $this->shopId === null && $this->shopGroupId === null;
    }

    /**
     * True when a shop of the given group is one of these shops: the shop itself, a shop of the group, or any shop.
     */
    public function covers(int $shopId, int $shopGroupId): bool
    {
        if ($this->shopId !== null) {
            return $this->shopId === $shopId;
        }
        if ($this->shopGroupId !== null) {
            return $this->shopGroupId === $shopGroupId;
        }

        return true;
    }

    /**
     * True when both constraints are about the same shops.
     */
    public function equals(self $other): bool
    {
        return $this->shopId === $other->shopId && $this->shopGroupId === $other->shopGroupId;
    }
}
