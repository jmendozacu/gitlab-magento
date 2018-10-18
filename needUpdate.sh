#!/bin/sh
# Tests for fileset update
# Can take a branch as a parameter
git remote update
REV=`git rev-parse @{u}`
echo "REV "$REV
BRANCH=`git rev-parse --abbrev-ref HEAD`
echo "BRANCH "$BRANCH;
LOCAL=$(git rev-parse @{0})
echo "LOCAL "$LOCAL
if [ $LOCAL = $REV ]; then
    echo "Up-to-date"
else
    git pull origin $BRANCH
fi