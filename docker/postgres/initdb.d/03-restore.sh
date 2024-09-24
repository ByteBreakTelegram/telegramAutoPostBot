#!/bin/bash

set -e

# Load extensions into both template1, $POSTGRES_DB and $POSTGRES_TEST_DB
if [ -f /backup/db.dump ]; then
    echo "restore database: $POSTGRES_TEST_DB"
    pg_restore --username $POSTGRES_USER --no-owner -d $POSTGRES_TEST_DB /backup/db.dump
fi
