# FAQ

## Why do I get `null` for a value that I can see in the data?

Check the path: segments are separated by `/`, keys are case-sensitive, and the
schema value must start with exactly `@data/`. List indexes are numbers:
`@data/items/0/sku`.

## How do I copy a string that starts with `@data/` literally?

Return it from a callback, or build it so it does not start with the prefix in
the schema, for example by mapping a placeholder and replacing it in the
callback.

## Can I map a JSON string directly?

Yes. Pass it to `StringObjects::instance($json)`. Invalid JSON throws
`InvalidArgumentException`.

## Can I map into objects instead of arrays?

`Mapper::map()` returns arrays. Pass the result to your object factory or
hydrator, or cast it: `(object) $result` for a flat result.

## Is the source data modified?

No. Dalue only reads from the `StringObjects` instance.

## Where do I report a bug?

Open an [issue](https://github.com/uuur86/dalue/issues). Report security
problems privately as described in
[SECURITY.md](https://github.com/uuur86/dalue/blob/main/SECURITY.md).
