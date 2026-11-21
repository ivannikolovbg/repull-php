<?php
/**
 * Pre-codegen spec transform: writes the spec php-nextgen generates from.
 *
 *   php scripts/prepare-spec.php IN OUT
 *
 * openapi/v1.json stays byte-identical to the live spec (the freshness check
 * compares it); only the copy handed to the generator is rewritten.
 *
 * Rule: a oneOf/anyOf whose branches are all primitives (boolean, string,
 * number, integer) becomes an untyped schema, which php-nextgen types as
 * `mixed`. php-nextgen has no oneOf support for primitives: it generates an
 * empty model class with no property to hold the value, so a field such as a
 * Vrbo amenity value (`true`, `"FREE"`, `2`) could only be sent as `{}` and was
 * read back as an empty object. With `mixed` the caller passes the plain value
 * and the SDK sends it unchanged. nullable/description/example are kept.
 *
 * Idempotent. No dependencies beyond ext-json.
 */

declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "usage: php prepare-spec.php IN OUT\n");
    exit(2);
}

// Decode to stdClass, not arrays: arrays would turn every `{}` in the spec into `[]`.
$spec = json_decode((string) file_get_contents($argv[1]), false, 512, JSON_THROW_ON_ERROR);
$schemas = $spec->components->schemas ?? new stdClass();

$resolve = function (object $s) use ($schemas): object {
    $seen = 0;
    while (isset($s->{'$ref'}) && $seen++ < 20) {
        $ref = $s->{'$ref'};
        $name = substr($ref, strrpos($ref, '/') + 1);
        $s = $schemas->{$name} ?? new stdClass();
    }
    return $s;
};

$isPrimitive = function ($s) use ($resolve): bool {
    if (!is_object($s)) {
        return false;
    }
    $s = $resolve($s);
    $t = $s->type ?? null;
    return in_array($t, ['boolean', 'string', 'number', 'integer'], true) && !isset($s->properties);
};

$count = 0;
$walk = function ($node) use (&$walk, $isPrimitive, &$count): void {
    if (is_object($node)) {
        foreach (['oneOf', 'anyOf'] as $k) {
            if (isset($node->{$k}) && is_array($node->{$k}) && $node->{$k} !== [] && !isset($node->discriminator)) {
                $all = true;
                foreach ($node->{$k} as $branch) {
                    if (!$isPrimitive($branch)) {
                        $all = false;
                        break;
                    }
                }
                if ($all) {
                    unset($node->{$k}, $node->type);
                    $count++;
                }
            }
        }
        foreach (get_object_vars($node) as $child) {
            $walk($child);
        }
    } elseif (is_array($node)) {
        foreach ($node as $child) {
            $walk($child);
        }
    }
};
$walk($spec);

file_put_contents($argv[2], json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
fwrite(STDERR, "prepare-spec: {$count} primitive-only oneOf/anyOf schemas made untyped (mixed)\n");
