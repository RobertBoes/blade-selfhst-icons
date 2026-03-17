#!/bin/bash

# Sync SVG icons from the selfhst/icons repository
# Usage: ./bin/sync.sh [version]
#   version: optional git tag/branch to checkout (default: latest tag)

set -euo pipefail

REPO_URL="https://github.com/selfhst/icons.git"
DIST_DIR="$(cd "$(dirname "$0")/.." && pwd)/dist"
ICONS_DIR="${DIST_DIR}/svg"

# Clean previous dist
rm -rf "${DIST_DIR}"
mkdir -p "${DIST_DIR}"

# Clone the repo (shallow)
echo "Cloning selfhst/icons..."
git clone --depth 1 --filter=blob:none --sparse "${REPO_URL}" "${DIST_DIR}/repo"

cd "${DIST_DIR}/repo"
git sparse-checkout set svg

# If a version was specified, fetch and checkout that tag
if [ -n "${1:-}" ]; then
    echo "Checking out version: $1"
    git fetch --depth 1 origin "refs/tags/$1:refs/tags/$1" 2>/dev/null || git fetch --depth 1 origin "$1"
    git checkout "$1"
fi

# Move SVGs to dist directory
mv svg "${ICONS_DIR}"

# Clean up cloned repo
rm -rf "${DIST_DIR}/repo"

ICON_COUNT=$(find "${ICONS_DIR}" -name "*.svg" | wc -l | tr -d ' ')
echo "Synced ${ICON_COUNT} icons to dist/svg"
