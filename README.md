# figuren.theater | Maintenance

Everything you need to maintain and maybe debug a running WordPress Multisite like [figuren.theater](https://figuren.theater).

---

## Plugins included

This package contains the following plugins. 
Thoose are completely managed by code and lack of their typical UI.

* [Query Monitor](https://wordpress.org/plugins/query-monitor/#developers)
* [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/#developers)
* [WP Sync DB](https://github.com/hrsetyono/wp-sync-db)

## What does this package do in addition?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

- Show a Dashboard Widget with the content of either the Error- or the Debug-logfile to Users with `manage-sites` capabilities.
- manual maintenance mode, just define `FT_MAINTENANCE_MODE` somewhere, before the `init` action is called
- Delete some additional DB tables, created by plugins, during blog-deletion.
- Drop-ins:

Add the following to your composer project:

```
"extra": {
    "dropin-paths": {
        "htdocs/wp-content/": [
            "package:figuren-theater/ft-maintenance:templates/htdocs/wp-content/db-error.php",
            "package:figuren-theater/ft-maintenance:templates/htdocs/wp-content/maintenance.php",
            "package:figuren-theater/ft-maintenance:templates/htdocs/wp-content/php-error.php"
        ]
    }
}
```
