#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib/db-copy.sh
. "$SCRIPT_DIR/lib/db-copy.sh"

SSH_KEY="${SSH_KEY:-/Users/aigarspeda/.ssh/digitalocean_construction}"
REMOTE_HOST="${REMOTE_HOST:-root@201.79.12.67}"
REMOTE_WP_PATH="${REMOTE_WP_PATH:-/var/www/construction}"
REMOTE_URL="${REMOTE_URL:-https://oaksbuild.lv}"
REMOTE_BACKUP_DIR="${REMOTE_BACKUP_DIR:-/var/backups/construction}"
LOCAL_URL="${LOCAL_URL:-http://construction.local}"
LOCAL_WP_PATH="${LOCAL_WP_PATH:-/Users/aigarspeda/Local Sites/construction/app/public}"
LOCAL_UPLOADS_PATH="${LOCAL_UPLOADS_PATH:-$LOCAL_WP_PATH/wp-content/uploads}"
REMOTE_UPLOADS_PATH="${REMOTE_UPLOADS_PATH:-$REMOTE_WP_PATH/wp-content/uploads}"
LOCAL_PHP_BIN="${LOCAL_PHP_BIN:-/Users/aigarspeda/Library/Application Support/Local/lightning-services/php-8.2.30+1/bin/darwin-arm64/bin/php}"
LOCAL_PHP_INI="${LOCAL_PHP_INI:-/Users/aigarspeda/Library/Application Support/Local/run/NuVAbV1Y1/conf/php/php.ini}"
LOCAL_WP_CLI="${LOCAL_WP_CLI:-/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar}"
LOCAL_MYSQL_BIN_DIR="${LOCAL_MYSQL_BIN_DIR:-/Users/aigarspeda/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin}"
LOCAL_MYSQL_SOCKET="${LOCAL_MYSQL_SOCKET:-/Users/aigarspeda/Library/Application Support/Local/run/NuVAbV1Y1/mysql/mysqld.sock}"
BACKUP_KEEP="${BACKUP_KEEP:-3}"

ASSUME_YES=0
ALLOW_OVERWRITE=0
SKIP_UPLOADS=0
TEMP_DIR=""
REMOTE_TEMP_DIR=""
REMOTE_BACKUP=""

usage() {
	cat <<'EOF'
Usage: ./scripts/push-db-to-droplet.sh [--yes] [--allow-overwrite] [--skip-uploads]

Replace the droplet WordPress database with the local database. The script:
  1. refuses to run if production has content that Local lacks (posts only
     on production, or edited there later) — pull first, or pass
     --allow-overwrite after checking the list;
  2. creates a compressed droplet backup;
  3. keeps production's own mail and Google settings: WP Mail SMTP options
     and logs, Site Kit options and user tokens are saved before the import
     and restored after it (Local's copies are discarded);
  4. imports Local with production URLs;
  5. copies Local uploads that production lacks (never deletes or replaces);
  6. flushes caches and rewrite rules.

This overwrites production pages, menus, other settings, users and plugin data.
Without --yes, type PUSH TO PRODUCTION when prompted.

Environment overrides:
  SSH_KEY, REMOTE_HOST, REMOTE_WP_PATH, REMOTE_URL, REMOTE_BACKUP_DIR
  LOCAL_URL, LOCAL_WP_PATH, LOCAL_UPLOADS_PATH, REMOTE_UPLOADS_PATH
  LOCAL_PHP_BIN, LOCAL_PHP_INI, LOCAL_WP_CLI
  LOCAL_MYSQL_BIN_DIR, LOCAL_MYSQL_SOCKET, BACKUP_KEEP
EOF
}

die() {
	printf 'Error: %s\n' "$*" >&2
	exit 1
}

cleanup() {
	if [ -n "$TEMP_DIR" ] && [ -d "$TEMP_DIR" ]; then
		rm -f -- "$TEMP_DIR"/*
		rmdir "$TEMP_DIR" 2>/dev/null || true
	fi

	if [ -n "$REMOTE_TEMP_DIR" ]; then
		ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" "rm -rf -- '$REMOTE_TEMP_DIR'" >/dev/null 2>&1 || true
	fi
}
trap cleanup EXIT

local_wp() {
	MYSQL_UNIX_PORT="$LOCAL_MYSQL_SOCKET" PATH="$LOCAL_MYSQL_BIN_DIR:$PATH" \
		"$LOCAL_PHP_BIN" -c "$LOCAL_PHP_INI" "$LOCAL_WP_CLI" \
		--path="$LOCAL_WP_PATH" "$@"
}

remote() {
	ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" "$@"
}

remote_wp() {
	remote "wp --allow-root --path='$REMOTE_WP_PATH' $*"
}

remote_wp_tables() {
	remote_wp --skip-plugins --skip-themes db tables "'*wpmailsmtp_*'" --all-tables-with-prefix --format=csv 2>/dev/null || true
}

for arg in "$@"; do
	case "$arg" in
		--yes) ASSUME_YES=1 ;;
		--allow-overwrite) ALLOW_OVERWRITE=1 ;;
		--skip-uploads) SKIP_UPLOADS=1 ;;
		-h|--help)
			usage
			exit 0
			;;
		*)
			die "Unknown argument: $arg"
			;;
	esac
done

command -v ssh >/dev/null 2>&1 || die "ssh is not installed."
command -v scp >/dev/null 2>&1 || die "scp is not installed."
command -v rsync >/dev/null 2>&1 || die "rsync is not installed."
command -v curl >/dev/null 2>&1 || die "curl is not installed."
command -v grep >/dev/null 2>&1 || die "grep is not installed."

[ -r "$SSH_KEY" ] || die "SSH key not found: $SSH_KEY"
[ -x "$LOCAL_PHP_BIN" ] || die "Local PHP binary not found: $LOCAL_PHP_BIN"
[ -r "$LOCAL_PHP_INI" ] || die "Local php.ini not found: $LOCAL_PHP_INI"
[ -r "$LOCAL_WP_CLI" ] || die "Local WP-CLI not found: $LOCAL_WP_CLI"
[ -d "$LOCAL_WP_PATH" ] || die "Local WordPress path not found: $LOCAL_WP_PATH"
[ -x "$LOCAL_MYSQL_BIN_DIR/mysql" ] || die "Local MySQL client not found in $LOCAL_MYSQL_BIN_DIR"
[ -x "$LOCAL_MYSQL_BIN_DIR/mysqldump" ] || die "Local mysqldump not found in $LOCAL_MYSQL_BIN_DIR"
[ -S "$LOCAL_MYSQL_SOCKET" ] || die "Local MySQL socket not found: $LOCAL_MYSQL_SOCKET. Start the construction site in Local."
[ -r "$SCRIPT_DIR/lib/environment-settings.php" ] || die "Missing scripts/lib/environment-settings.php"
[ -r "$SCRIPT_DIR/lib/content-inventory.php" ] || die "Missing scripts/lib/content-inventory.php"
case "$BACKUP_KEEP" in
	''|*[!0-9]*) die "BACKUP_KEEP must be a positive integer." ;;
esac
[ "$BACKUP_KEEP" -ge 1 ] || die "BACKUP_KEEP must be at least 1."

REMOTE_URL="${REMOTE_URL%/}"
LOCAL_URL="${LOCAL_URL%/}"
SSH_ARGS=(-i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=10)

local_wp --skip-plugins --skip-themes db check >/dev/null \
	|| die "Local database is unavailable. Start the construction site in Local."
remote_wp --skip-plugins --skip-themes db check >/dev/null \
	|| die "Droplet database is unavailable."
remote "command -v rsync >/dev/null" || die "rsync is not installed on the droplet."

ACTUAL_LOCAL_HOME="$(local_wp --skip-plugins --skip-themes option get home)"
[ "${ACTUAL_LOCAL_HOME%/}" = "$LOCAL_URL" ] \
	|| die "Local home URL is $ACTUAL_LOCAL_HOME, expected $LOCAL_URL"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
TEMP_DIR="$(mktemp -d /tmp/construction-push.XXXXXX)"
REMOTE_TEMP_DIR="$(remote "umask 077; mktemp -d /tmp/construction-push.XXXXXX")"
scp -q "${SSH_ARGS[@]}" "$SCRIPT_DIR/lib/environment-settings.php" "$SCRIPT_DIR/lib/content-inventory.php" "$REMOTE_HOST:$REMOTE_TEMP_DIR/"

# 1. Never silently drop work that only exists on production.
printf 'Comparing Local and production content...\n'
local_wp --skip-plugins --skip-themes eval-file "$SCRIPT_DIR/lib/content-inventory.php" > "$TEMP_DIR/local.tsv"
remote_wp --skip-plugins --skip-themes eval-file "$REMOTE_TEMP_DIR/content-inventory.php" > "$TEMP_DIR/remote.tsv"
[ -s "$TEMP_DIR/local.tsv" ] && [ -s "$TEMP_DIR/remote.tsv" ] || die "Could not list content on both sites."
LOSSES="$(find_overwrites "$TEMP_DIR/local.tsv" "$TEMP_DIR/remote.tsv")"
if [ -n "$LOSSES" ]; then
	printf 'Production has content that this push would delete or replace:\n%s\n' "$LOSSES"
	if [ "$ALLOW_OVERWRITE" -ne 1 ]; then
		die "Nothing was changed. Pull production into Local first (scripts/pull-db-from-droplet.sh), or re-run with --allow-overwrite if these may be lost."
	fi
	printf 'Continuing because --allow-overwrite was given.\n'
fi

if [ "$ASSUME_YES" -ne 1 ]; then
	printf 'This will REPLACE the database on %s with the local database.\n' "$REMOTE_HOST"
	printf 'Production pages, menus, settings, users and plugin data will be overwritten;\n'
	printf 'its mail (WP Mail SMTP) and Site Kit settings are kept.\n'
	printf 'Type PUSH TO PRODUCTION to continue: '
	read -r confirmation || die "No confirmation received."
	[ "$confirmation" = "PUSH TO PRODUCTION" ] || die "Cancelled."
fi

LOCAL_EXPORT="$TEMP_DIR/local-for-droplet.sql"
REMOTE_IMPORT="$REMOTE_TEMP_DIR/local-push.sql"
REMOTE_BACKUP="$REMOTE_BACKUP_DIR/before-local-push-$TIMESTAMP.sql.gz"

printf 'Preparing the local database with production URLs...\n'
local_wp search-replace \
	"$LOCAL_URL" "$REMOTE_URL" \
	--all-tables-with-prefix --precise --skip-columns=guid \
	--export="$LOCAL_EXPORT" >/dev/null

[ -s "$LOCAL_EXPORT" ] || die "The local export is empty. Production was not changed."
grep -q 'CREATE TABLE' "$LOCAL_EXPORT" || die "The local export does not look like a SQL dump. Production was not changed."

printf 'Backing up the droplet database...\n'
remote "set -e; umask 077; mkdir -p '$REMOTE_BACKUP_DIR'; wp --allow-root --path='$REMOTE_WP_PATH' --skip-plugins --skip-themes db export '${REMOTE_BACKUP%.gz}' --add-drop-table --quiet; gzip -f '${REMOTE_BACKUP%.gz}'"
printf 'Remote rollback backup: %s\n' "$REMOTE_BACKUP"

# 3. Production keeps its own mail delivery and Google connections.
printf 'Saving production mail and Site Kit settings...\n'
remote_wp --skip-plugins --skip-themes eval-file "$REMOTE_TEMP_DIR/environment-settings.php" save "$REMOTE_TEMP_DIR/environment.json"
MAIL_TABLES="$(remote_wp_tables)"
if [ -n "$MAIL_TABLES" ]; then
	remote_wp --skip-plugins --skip-themes db export "$REMOTE_TEMP_DIR/mail-logs.sql" --tables="$MAIL_TABLES" --add-drop-table --quiet
fi

printf 'Uploading and importing the local database...\n'
scp -q "${SSH_ARGS[@]}" "$LOCAL_EXPORT" "$REMOTE_HOST:$REMOTE_IMPORT"
remote "set -e; W=\"wp --allow-root --path='$REMOTE_WP_PATH'\"; \$W --skip-plugins --skip-themes db import '$REMOTE_IMPORT'; \$W search-replace '$LOCAL_URL' '$REMOTE_URL' --all-tables-with-prefix --precise --skip-columns=guid --quiet; \$W --skip-plugins --skip-themes option update home '$REMOTE_URL' --quiet; \$W --skip-plugins --skip-themes option update siteurl '$REMOTE_URL' --quiet"

printf 'Restoring production mail and Site Kit settings...\n'
remote_wp --skip-plugins --skip-themes eval-file "$REMOTE_TEMP_DIR/environment-settings.php" restore "$REMOTE_TEMP_DIR/environment.json" \
	|| die "Restoring production mail/Site Kit settings failed. Restore from $REMOTE_BACKUP"
if [ -n "$MAIL_TABLES" ]; then
	remote_wp --skip-plugins --skip-themes db import "$REMOTE_TEMP_DIR/mail-logs.sql" >/dev/null
fi

# 5. Media: copy what production lacks; never delete or replace files there.
if [ "$SKIP_UPLOADS" -ne 1 ]; then
	printf 'Copying uploads that production lacks...\n'
	rsync -a --ignore-existing --itemize-changes \
		-e "ssh -i $SSH_KEY -o BatchMode=yes -o ConnectTimeout=10" \
		--exclude='.DS_Store' \
		"$LOCAL_UPLOADS_PATH/" "$REMOTE_HOST:$REMOTE_UPLOADS_PATH/"
	remote "chown -R www-data:www-data '$REMOTE_UPLOADS_PATH'"
fi

remote "set -e; W=\"wp --allow-root --path='$REMOTE_WP_PATH'\"; \$W cache flush; \$W rewrite flush"

remote "count=0; for backup in \$(find '$REMOTE_BACKUP_DIR' -maxdepth 1 -type f -name 'before-local-push-*.sql.gz' -print | sort -r); do count=\$((count + 1)); if [ \"\$count\" -gt '$BACKUP_KEEP' ]; then rm -f -- \"\$backup\"; fi; done"

ACTUAL_REMOTE_HOME="$(remote_wp --skip-plugins --skip-themes option get home)"
[ "${ACTUAL_REMOTE_HOME%/}" = "$REMOTE_URL" ] \
	|| die "Production URL verification failed. Restore from $REMOTE_BACKUP"

curl --fail --silent --show-error --location "$REMOTE_URL/" >/dev/null

printf 'Droplet database replaced and verified at %s\n' "$REMOTE_URL"
printf 'Remote rollback backup: %s\n' "$REMOTE_BACKUP"
