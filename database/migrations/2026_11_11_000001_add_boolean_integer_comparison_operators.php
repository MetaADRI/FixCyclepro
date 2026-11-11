<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBooleanIntegerComparisonOperators extends Migration
{
    /**
     * Postgres ships no `boolean = integer` comparison operator. This app (and
     * vendor packages such as Passport) query boolean columns with integer
     * literals, e.g. where('revoked', 0), which Postgres rejects with
     * "operator does not exist: boolean = integer". The overloads below are
     * purely additive: they only define comparisons that previously raised an
     * error, so no existing query semantics change.
     */
    public function up()
    {
        DB::statement('DROP OPERATOR IF EXISTS public.= (boolean, integer)');
        DB::statement('DROP OPERATOR IF EXISTS public.= (integer, boolean)');
        DB::statement('DROP FUNCTION IF EXISTS public.fixcycle_bool_eq_int(boolean, integer)');
        DB::statement('DROP FUNCTION IF EXISTS public.fixcycle_int_eq_bool(integer, boolean)');
        DB::statement('CREATE FUNCTION public.fixcycle_bool_eq_int(boolean, integer) RETURNS boolean LANGUAGE sql IMMUTABLE PARALLEL SAFE AS $$ SELECT $1 = ($2 <> 0) $$');
        DB::statement('CREATE OPERATOR public.= (LEFTARG = boolean, RIGHTARG = integer, PROCEDURE = public.fixcycle_bool_eq_int)');
        DB::statement('CREATE FUNCTION public.fixcycle_int_eq_bool(integer, boolean) RETURNS boolean LANGUAGE sql IMMUTABLE PARALLEL SAFE AS $$ SELECT ($1 <> 0) = $2 $$');
        DB::statement('CREATE OPERATOR public.= (LEFTARG = integer, RIGHTARG = boolean, PROCEDURE = public.fixcycle_int_eq_bool)');
    }

    public function down()
    {
        DB::statement('DROP OPERATOR IF EXISTS public.= (boolean, integer)');
        DB::statement('DROP OPERATOR IF EXISTS public.= (integer, boolean)');
        DB::statement('DROP FUNCTION IF EXISTS public.fixcycle_bool_eq_int(boolean, integer)');
        DB::statement('DROP FUNCTION IF EXISTS public.fixcycle_int_eq_bool(integer, boolean)');
    }
}
