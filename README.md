# # canadian-sme
# Event-App
# Laravel SetUp
composer install command add in yml file 
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan queue:restart
```

Build assets in CI or another build environment if Node.js is unavailable on the server. Back up the database before migrations and seed only data explicitly required by the release.

The existing `.github/workflows/deploy.yml` deploys pushes to `main` over SSH using the GitHub secrets `PRIVATE_KEY`, `HOST`, `USERNAME`, and `DEPLOY_PATH`. It pulls code, runs Composer install **and update**, clears caches, and restarts PHP 8.2 FPM and Nginx. It does not build frontend assets or run migrations; account for those steps in your release process. Review the update step if releases must reproduce `composer.lock` exactly.

## Troubleshooting

| Problem | Checks |
| --- | --- |
| Missing application key or JWT secret | Generate keys for a new installation and clear cached configuration. |
| Database, cache, or session table errors | Check credentials, database availability, and `php artisan migrate:status`. |
| Vite manifest missing | Start `npm run dev` locally or run `npm run build`. |
| Uploaded files unavailable | Check `APP_URL`, the storage link, filesystem disk, and permissions. |
| QR generation fails | Check GD and write access to `public/qrcodes/` and `storage/framework/qr-locks/`. |
| Email does not arrive | Check mail settings, logs, and queue workers. The log mailer does not deliver email. |
| Queued work is delayed | Check the queue connection, workers, and `php artisan queue:failed`. |
| Environment changes have no effect | Run `php artisan config:clear` and restart long-running workers. |

## License

`composer.json` declares the project license as MIT. No standalone license file is currently included in the repository.
