# HCBP Area 3 Apps

Aplikasi HCBP Area 3 berbasis Laravel untuk pengelolaan IDP, penetapan rencana pengembangan, monitoring, coaching, evaluasi, dan pengaturan role pengguna.

## Production

Aplikasi dikonfigurasi untuk deployment Laravel pada Vercel menggunakan community PHP runtime. Runtime PHP 8.3 dipakai agar sesuai dengan requirement Laravel di `composer.json`.

## Environment production

Minimal siapkan:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=<generated-secret>`
- `APP_URL=https://<domain-vercel>`
- `DB_CONNECTION=<managed-database>`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER=cookie`
- `CACHE_STORE=array`

Untuk upload bukti coaching, gunakan object storage yang persisten pada production dan set `PUBLIC_FILESYSTEM_DRIVER=s3` beserta kredensial storage yang sesuai. Filesystem serverless bersifat ephemeral/read-only sehingga file upload tidak boleh mengandalkan disk lokal untuk data production.

## Deployment

Push ke branch `main` akan memicu deployment otomatis jika repository sudah terhubung ke Vercel. Build frontend dijalankan melalui `vercel-build.sh`, sementara request aplikasi diarahkan ke `api/index.php` sebagai front controller Laravel.
