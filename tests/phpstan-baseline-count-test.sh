#!/bin/bash
# Test: PHPStan baseline must have fewer than 100 error entries.
# This enforces the target from issue #78.

BASELINE_FILE="phpstan-baseline.neon"
MAX_ERRORS=100

if [ ! -f "$BASELINE_FILE" ]; then
	echo "FAIL: $BASELINE_FILE not found"
	exit 1
fi

# Count error entries (each "count:" line represents an error group, sum their values)
TOTAL=$(grep -oP 'count: \K\d+' "$BASELINE_FILE" | paste -sd+ | bc)

if [ -z "$TOTAL" ]; then
	TOTAL=0
fi

echo "PHPStan baseline errors: $TOTAL (target: < $MAX_ERRORS)"

if [ "$TOTAL" -ge "$MAX_ERRORS" ]; then
	echo "FAIL: Baseline has $TOTAL errors, must be under $MAX_ERRORS"
	exit 1
fi

echo "PASS: Baseline is under the target"
exit 0
