# php-nextgen template overrides

`scripts/regen.sh` passes `-t templates`, so these files replace the built-in
php-nextgen templates of the generator version pinned in `openapitools.json`
(7.22.0). Every other template comes from the generator unchanged. Each file is
the upstream 7.22.0 template with one patch, marked `[repull]` where it lands in
generated code. When bumping the generator version, re-copy the upstream files
and re-apply the patch.

- `ObjectSerializer.mustache` — `toQueryValue()` takes the parameter's format and
  serialises any `DateTimeInterface`: `Y-m-d` for `format: date`, the configured
  date-time format otherwise. Upstream only handled an openApiType of
  `\DateTime`, which a `format: date` query parameter never has (its type is
  `string`), so passing a `\DateTime` to e.g. `start`/`end` threw
  "Object of class DateTime could not be converted to string" before sending.
- `api.mustache` — each `toQueryValue()` call passes the parameter's format
  (`'date'`, `'date-time'` or `null`).

Primitive-only oneOf/anyOf fields are handled in `scripts/prepare-spec.php`
(they become `mixed`), not here.
