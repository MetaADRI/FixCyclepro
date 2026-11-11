<?php

namespace App\Database;

use DateTimeInterface;
use Illuminate\Database\PostgresConnection;
use PDO;

/**
 * Postgres connection tuned for cross-driver binding compatibility.
 *
 * The base Connection binds PHP ints as PDO::PARAM_INT. When prepared
 * statements are emulated client-side (DB_EMULATE_PREPARES=true) PDO inlines
 * those values UNQUOTED, e.g. `where UserStatus = 1`. Postgres then rejects
 * the query when the column is not an integer type:
 *   - boolean columns:  "operator does not exist: boolean = integer"
 *   - varchar columns: "operator does not exist: character varying = integer"
 * Native server-side prepares dodge this because the server infers parameter
 * types from context, but emulation has no inference step.
 *
 * Binding all scalars as PARAM_STR (PDO inlines them as quoted literals of
 * unknown type) restores the same context-based coercion Postgres applies
 * to literals, for both native and emulated prepares. This matches how
 * Laravel binds on MySQL and keeps every column type working:
 *   int_col = '2' / varchar_col = '1' / boolean_col = '1' all coerce.
 */
class PgsqlConnection extends PostgresConnection
{
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif (is_bool($value)) {
                $bindings[$key] = $value ? '1' : '0';
            }
        }

        return $bindings;
    }

    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                is_resource($value) ? PDO::PARAM_LOB : PDO::PARAM_STR
            );
        }
    }
}
