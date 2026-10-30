# Database: Supabase Postgres

Project: "Jeee unical" (ref qjmipamaydaaxzpqzwxb, eu-west-1).

Use the **Session pooler** values from Supabase > Connect (Render cannot reach the IPv6-only direct host):

| Var | Value |
|---|---|
| DB_CONNECTION | pgsql |
| DB_HOST | the pooler host shown in Connect (aws-*-eu-west-1.pooler.supabase.com) |
| DB_PORT | 5432 (session mode; never 6543) |
| DB_DATABASE | postgres |
| DB_USERNAME | postgres.qjmipamaydaaxzpqzwxb |
| DB_PASSWORD | your database password (Project Settings > Database) |
| DB_SSLMODE | require |

First deploy with RUN_MIGRATIONS=true creates the tables. Then, in the Render Shell, once:
`php artisan db:seed --class=SettingsSeeder`

Make an admin after registering (run in Supabase SQL editor):
`UPDATE users SET admin = true WHERE email = 'you@example.com';`

**Security: enable RLS on every table after the first migration.** Tables in the `public` schema are
exposed through Supabase's public Data API, and Laravel does not enable RLS. Laravel connects as the
`postgres` role, which bypasses RLS, so turning RLS on (with no policies) blocks the public API
without affecting the app:

```sql
DO $$ DECLARE r record; BEGIN
  FOR r IN SELECT tablename FROM pg_tables WHERE schemaname='public' LOOP
    EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', r.tablename);
  END LOOP; END $$;
```
Re-run it after any future migration that adds tables.
