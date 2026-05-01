#!/usr/bin/env bash
# Regenerate the PHP SDK from the live Repull OpenAPI spec.
# Requires: java, npx (openapi-generator-cli is fetched via npx).

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SPEC_URL="${SPEC_URL:-https://api.repull.dev/openapi.json}"
SPEC_PATH="${ROOT}/openapi/v1.json"
OUT_DIR="$(mktemp -d)"

echo "==> Fetching spec from ${SPEC_URL}"
curl -sSf "${SPEC_URL}" -o "${SPEC_PATH}"
echo "    Saved $(wc -c <"${SPEC_PATH}") bytes to ${SPEC_PATH}"

echo "==> Generating PHP SDK with openapi-generator-cli (php-nextgen)"
npx --yes @openapitools/openapi-generator-cli@latest generate \
  -i "${SPEC_PATH}" \
  -g php-nextgen \
  -o "${OUT_DIR}" \
  --additional-properties=invokerPackage=Repull,packageName=repull-sdk,gitUserId=ivannikolovbg,gitRepoId=repull-php \
  > /tmp/repull-php-gen.log 2>&1

echo "==> Copying generated src/ into ${ROOT}/src/"
rm -rf "${ROOT}/src"
cp -r "${OUT_DIR}/src" "${ROOT}/src"

echo "==> Patching enum validators for forward compat"
php "${ROOT}/scripts/relax-enums.php"

echo "==> Done. Run \`composer install && composer phpstan && composer test\` to validate."
