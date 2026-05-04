<?php
/**
 * Post-codegen patcher: relax strict enum validation in generated models.
 *
 * The OpenAPI spec is occasionally out of sync with what the live API returns
 * (new statuses, new platforms, internal test values like "test-flows"). The
 * default php-nextgen generator throws InvalidArgumentException on any unknown
 * enum value, which would crash the SDK on every list call.
 *
 * This patcher rewrites every "Invalid value '%s' for 'X', must be one of '%s'"
 * throw into a no-op so the SDK accepts unknown values and lets the caller
 * decide what to do with them.
 *
 * Run after `regen.sh`. Idempotent.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$dir  = $root . '/src/Model';

if (!is_dir($dir)) {
    fwrite(STDERR, "Model dir not found: $dir\n");
    exit(1);
}

$files   = glob($dir . '/*.php') ?: [];
$patched = 0;

foreach ($files as $file) {
    $src = file_get_contents($file);
    if ($src === false) {
        continue;
    }

    // Match the enum-validation block. Two variants emitted by php-nextgen:
    //   (a) non-nullable:  if (!in_array($x, $allowedValues, true)) { ... }
    //   (b) nullable:      if (!is_null($x) && !in_array($x, $allowedValues, true)) { ... }
    $pattern = '/(\s+)\$allowedValues = self::get\w+AllowableValues\(\);\s*\n'
             . '\s+if \((?:!is_null\(\$\w+\) && )?!in_array\(\$\w+, \$allowedValues, true\)\) \{\s*\n'
             . '\s+throw new InvalidArgumentException\(\s*\n'
             . '\s+sprintf\(\s*\n'
             . '\s+"Invalid value [^"]+",\s*\n'
             . '(?:\s+\$\w+,\s*\n)+'
             . '\s+implode\([^)]+\)\s*\n'
             . '\s+\)\s*\n'
             . '\s+\);\s*\n'
             . '\s+\}/m';

    $replaced = preg_replace($pattern, '$1// (relax-enums.php) accept unknown enum values for forward compat', $src);

    if ($replaced !== null && $replaced !== $src) {
        file_put_contents($file, $replaced);
        $patched++;
        echo "patched: " . basename($file) . "\n";
    }
}

echo "==> Relaxed enum validation in $patched file(s).\n";

// php-nextgen emits invalid PHP constants when enum values contain dots
// (e.g. `reservation.created` becomes `TYPE_RESERVATION/CREATED` instead of
// `TYPE_RESERVATION_CREATED`). Rewrite those constant names so the file
// parses. The string values on the RHS already contain the dots correctly.
$slashFiles  = glob($dir . '/*.php') ?: [];
$slashFixed  = 0;
foreach ($slashFiles as $file) {
    $src = file_get_contents($file);
    if ($src === false) {
        continue;
    }

    // Match TYPE_, MODE_, STATUS_, … followed by an UPPERCASE identifier with
    // one or more `/` segments inside. We only touch the identifier shape, so
    // any URL-like literal stays intact.
    $constPattern = '/\b([A-Z][A-Z0-9]*(?:_[A-Z0-9]+)*)((?:\/[A-Z][A-Z0-9_]*)+)\b/';
    $replaced = preg_replace_callback($constPattern, function ($m) {
        return $m[1] . str_replace('/', '_', $m[2]);
    }, $src);

    if ($replaced !== null && $replaced !== $src) {
        file_put_contents($file, $replaced);
        $slashFixed++;
        echo "fixed slashed constants: " . basename($file) . "\n";
    }
}
echo "==> Fixed slashed constant names in $slashFixed file(s).\n";
