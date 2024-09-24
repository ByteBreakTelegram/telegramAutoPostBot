#!/bin/bash

set -e

# Load extensions into both template1, $POSTGRES_DB and $POSTGRES_TEST_DB
for DB in template1 $POSTGRES_DB $POSTGRES_TEST_DB; do
    echo "Loading extensions into $DB"

    psql=(psql -v ON_ERROR_STOP=1)

    "${psql[@]}" --username $POSTGRES_USER -d $DB <<-'EOSQL'
		CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
EOSQL

done
