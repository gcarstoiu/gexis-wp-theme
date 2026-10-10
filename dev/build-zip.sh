#!/usr/bin/env bash
# Release zip of the committed theme, without dev/: dist/gexis.zip, unpacking
# to gexis/ so WordPress installs it under the right slug.
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist
git archive --format=zip --prefix=gexis/ -o dist/gexis.zip HEAD
echo "dist/gexis.zip"
