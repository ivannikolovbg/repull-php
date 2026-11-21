#!/usr/bin/env bash
# Regenerate the PHP SDK from the live Repull OpenAPI spec.
# Requires: java, npx (openapi-generator-cli is fetched via npx).

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SPEC_URL="${SPEC_URL:-https://api.repull.dev/openapi.json}"
SPEC_PATH="${ROOT}/openapi/v1.json"
OUT_DIR="$(mktemp -d)"
GEN_SPEC="$(mktemp --suffix=.json)"
# openapitools.json (in ROOT) pins the generator version the templates/ overrides were copied from.
cd "${ROOT}"

echo "==> Fetching spec from ${SPEC_URL}"
curl -sSf "${SPEC_URL}" -o "${SPEC_PATH}"
echo "    Saved $(wc -c <"${SPEC_PATH}") bytes to ${SPEC_PATH}"

echo "==> Preparing the generator's copy of the spec (scripts/prepare-spec.php)"
php "${ROOT}/scripts/prepare-spec.php" "${SPEC_PATH}" "${GEN_SPEC}"

echo "==> Generating PHP SDK with openapi-generator-cli (php-nextgen, templates/ overrides)"
# templates/ holds patched copies of php-nextgen 7.22.0 templates (see templates/README.md).
npx --yes @openapitools/openapi-generator-cli@latest generate \
  -i "${GEN_SPEC}" \
  -g php-nextgen \
  -t "${ROOT}/templates" \
  -o "${OUT_DIR}" \
  --additional-properties=invokerPackage=Repull,packageName=repull-sdk,gitUserId=ivannikolovbg,gitRepoId=repull-php \
  > /tmp/repull-php-gen.log 2>&1

echo "==> Copying generated src/ into ${ROOT}/src/"
rm -rf "${ROOT}/src"
cp -r "${OUT_DIR}/src" "${ROOT}/src"

echo "==> Patching enum validators for forward compat"
php "${ROOT}/scripts/relax-enums.php"

echo "==> Done. Run \`composer install && composer phpstan && composer test\` to validate."
