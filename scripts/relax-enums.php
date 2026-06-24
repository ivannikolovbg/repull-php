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
 * It ALSO fixes a php-nextgen codegen defect: enum values containing dots
 * (e.g. "account.created", "reservation.message.received") are turned into
 * constant names with a "/" separator — "TYPE_ACCOUNT/CREATED" — which is
 * illegal PHP and fails phpstan with "Syntax error, unexpected '/'". The
 * legacy `php` generator produces valid "_" separators; `php-nextgen` does
 * not (reproduced on 7.22.0 and 7.23.0). We rewrite "/" to "_" inside
 * generated const declarations and self::CONST references so the names are
 * valid PHP regardless of which generator version npx resolves.
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
    if ($replaced === null) {
        $replaced = $src;
    }

    // Fix php-nextgen's illegal "/" separator in enum constant names.
    //   public const TYPE_ACCOUNT/CREATED = 'account.created';
    //   self::TYPE_ACCOUNT/CREATED,
    // become
    //   public const TYPE_ACCOUNT_CREATED = 'account.created';
    //   self::TYPE_ACCOUNT_CREATED,
    // A PHP const name is /[A-Z_][A-Z0-9_]*/i; the only place a "/" appears
    // inside such a token is this codegen defect, so a token-scoped replace is
    // safe. We loop until stable to collapse multi-dot names (A/B/C -> A_B_C).
    $fixConstSlashes = static function (string $code): string {
        // const declarations: `const NAME/WITH/SLASHES`
        $code = preg_replace_callback(
            '/\bconst\s+([A-Za-z_][A-Za-z0-9_]*(?:\/[A-Za-z0-9_]+)+)\b/',
            static fn(array $m): string => 'const ' . str_replace('/', '_', $m[1]),
            $code
        );
        // self:: references: `self::NAME/WITH/SLASHES`
        $code = preg_replace_callback(
            '/\bself::([A-Za-z_][A-Za-z0-9_]*(?:\/[A-Za-z0-9_]+)+)\b/',
            static fn(array $m): string => 'self::' . str_replace('/', '_', $m[1]),
            $code
        );
        return $code;
    };
    $beforeSlashFix = $replaced;
    $replaced = $fixConstSlashes($replaced);
    if ($replaced !== $beforeSlashFix) {
        echo "fixed const-name slashes: " . basename($file) . "\n";
    }

    if ($replaced !== null && $replaced !== $src) {
        file_put_contents($file, $replaced);
        $patched++;
        echo "patched: " . basename($file) . "\n";
    }
}

echo "==> Relaxed enum validation in $patched file(s).\n";
