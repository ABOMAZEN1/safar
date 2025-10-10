#!/bin/sh
. "$(dirname "$0")/_/husky.sh"

# Run PHPStan analysis before push
echo "Running PHPStan analysis..."
vendor/bin/phpstan analyse
RESULT=$?
if [ $RESULT -ne 0 ]; then
    echo "PHPStan found issues. Aborting push."
    exit 1
fi

echo "Pre-push checks passed."
