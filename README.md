# prestashopheaven/runtime-shared-kernel

Value objects every PrestaHaven module shares, in pure PHP without PrestaShop: the shop constraint (one shop, a shop group or all shops). The only package the Domain layer of a module may use (PKG-09).

## Use when

A command, query or domain object says which shops it is about (one shop, a shop group, all shops), or the Domain layer needs a shared value object without depending on PrestaShop.

## Install

```
composer require prestashopheaven/runtime-shared-kernel
```

## Usage

<!-- Examples of the public API. -->

---

Read-only split of [`prestashopheaven/mono-runtime-core`](https://github.com/prestashopheaven/mono-runtime-core): changes,
issues and pull requests go there. MIT License.
