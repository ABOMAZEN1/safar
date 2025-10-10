#!/bin/sh
. "$(dirname "$0")/_/husky.sh"

# Run tlint (ensuring tlint.json is used)
echo "Running tlint..."
vendor/bin/tlint
RESULT=$?
if [ $RESULT -ne 0 ]; then
    echo "Tlint errors found. Aborting commit."
    exit 1
fi

# Run duster (ensuring duster.json is used)
echo "Running duster..."
vendor/bin/duster
RESULT=$?
if [ $RESULT -ne 0 ]; then
    echo "Duster issues found. Aborting commit."
    exit 1
fi

echo "Pre-commit checks passed."
