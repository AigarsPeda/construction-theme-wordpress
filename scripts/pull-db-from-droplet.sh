#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

SSH_KEY="${SSH_KEY:-/Users/aigarspeda/.ssh/digitalocean_construction}"
REMOTE_HOST="${REMOTE_HOST:-root@201.79.12.67}"
REMOTE_WP_PATH="${REMOTE_WP_PATH:-/var/www/construction}"
REMOTE_URL="${REMOTE_URL:-http://201.79.12.67}"
LOCAL_URL="${LOCAL_URL:-http://construction.local}"
LOCAL_WP_PATH="${LOCAL_WP_PATH:-/Users/aigarspeda/Local Sites/construction/app/public}"
LOCAL_PHP_BIN="${LOCAL_PHP_BIN:-/Users/aigarspeda/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php}"
LOCAL_PHP_INI="${LOCAL_PHP_INI:-/Users/aigarspeda/Library/Application Support/Local/run/NuVAbV1Y1/conf/php/php.ini}"
LOCAL_WP_CLI="${LOCAL_WP_CLI:-/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar}"
LOCAL_MYSQL_BIN_DIR="${LOCAL_MYSQL_BIN_DIR:-/Users/aigarspeda/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin}"
LOCAL_MYSQL_SOCKET="${LOCAL_MYSQL_SOCKET:-/Users/aigarspeda/Library/Application Support/Local/run/NuVAbV1Y1/mysql/mysqld.sock}"
BACKUP_DIR="${BACKUP_DIR:-$PROJECT_ROOT/../construction-backups}"
BACKUP_KEEP="${BACKUP_KEEP:-3}"

ASSUME_YES=0
TEMP_DIR=""

usage() {
	cat <<'EOF'
Usage: ./scripts/pull-db-from-droplet.sh [--yes]

Replace the local WordPress database with the droplet database. The script:
  1. backs up the current local database;
  2. streams a fresh database export from the droplet;
  3. imports it into Local;
  4. replaces the live URL with http://construction.local;
  5. flushes caches and rewrite rules.

This replaces local pages, menus, settings, users, and plugin data.
It does not copy files from wp-content/uploads.
Without --yes, type PULL when prompted.

Environment overrides:
  SSH_KEY, REMOTE_HOST, REMOTE_WP_PATH, REMOTE_URL, LOCAL_URL
  LOCAL_WP_PATH, LOCAL_PHP_BIN, LOCAL_PHP_INI, LOCAL_WP_CLI
  LOCAL_MYSQL_BIN_DIR, LOCAL_MYSQL_SOCKET, BACKUP_DIR, BACKUP_KEEP
EOF
}

die() {
	printf 'Error: %s\n' "$*" >&2
	exit 1
}

cleanup() {
	if [ -n "$TEMP_DIR" ] && [ -d "$TEMP_DIR" ]; then
		rm -f -- "$TEMP_DIR/droplet.sql"
		rmdir "$TEMP_DIR" 2>/dev/null || true
	fi
}
trap cleanup EXIT

local_wp() {
	MYSQL_UNIX_PORT="$LOCAL_MYSQL_SOCKET" PATH="$LOCAL_MYSQL_BIN_DIR:$PATH" \
		"$LOCAL_PHP_BIN" -c "$LOCAL_PHP_INI" "$LOCAL_WP_CLI" \
		--path="$LOCAL_WP_PATH" "$@"
}

prune_local_backups() {
	find "$BACKUP_DIR" -maxdepth 1 -type f -name 'local-before-pull-*.sql.gz' -print \
		| LC_ALL=C sort -r \
		| awk -v keep="$BACKUP_KEEP" 'NR > keep' \
		| while IFS= read -r old_backup; do
			rm -f -- "$old_backup"
		done
}

for arg in "$@"; do
	case "$arg" in
		--yes)
			ASSUME_YES=1
			;;
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
command -v gzip >/dev/null 2>&1 || die "gzip is not installed."
command -v grep >/dev/null 2>&1 || die "grep is not installed."

[ -r "$SSH_KEY" ] || die "SSH key not found: $SSH_KEY"
[ -x "$LOCAL_PHP_BIN" ] || die "Local PHP binary not found: $LOCAL_PHP_BIN"
[ -r "$LOCAL_PHP_INI" ] || die "Local php.ini not found: $LOCAL_PHP_INI"
[ -r "$LOCAL_WP_CLI" ] || die "Local WP-CLI not found: $LOCAL_WP_CLI"
[ -d "$LOCAL_WP_PATH" ] || die "Local WordPress path not found: $LOCAL_WP_PATH"
[ -x "$LOCAL_MYSQL_BIN_DIR/mysql" ] || die "Local MySQL client not found in $LOCAL_MYSQL_BIN_DIR"
[ -x "$LOCAL_MYSQL_BIN_DIR/mysqldump" ] || die "Local mysqldump not found in $LOCAL_MYSQL_BIN_DIR"
[ -S "$LOCAL_MYSQL_SOCKET" ] || die "Local MySQL socket not found: $LOCAL_MYSQL_SOCKET. Start the construction site in Local."
case "$BACKUP_KEEP" in
	''|*[!0-9]*) die "BACKUP_KEEP must be a positive integer." ;;
esac
[ "$BACKUP_KEEP" -ge 1 ] || die "BACKUP_KEEP must be at least 1."

REMOTE_URL="${REMOTE_URL%/}"
LOCAL_URL="${LOCAL_URL%/}"
SSH_ARGS=(-i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=10)

local_wp --skip-plugins --skip-themes db check >/dev/null \
	|| die "Local database is unavailable. Start the construction site in Local."
ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
	"wp --allow-root --path='$REMOTE_WP_PATH' --skip-plugins --skip-themes db check >/dev/null" \
	|| die "Droplet database is unavailable."

if [ "$ASSUME_YES" -ne 1 ]; then
	printf 'This will replace the LOCAL database with the database from %s.\n' "$REMOTE_HOST"
	printf 'Type PULL to continue: '
	read -r confirmation || die "No confirmation received."
	[ "$confirmation" = "PULL" ] || die "Cancelled."
fi

mkdir -p "$BACKUP_DIR"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
LOCAL_BACKUP="$BACKUP_DIR/local-before-pull-$TIMESTAMP.sql"
TEMP_DIR="$(mktemp -d /tmp/construction-pull.XXXXXX)"
REMOTE_DUMP="$TEMP_DIR/droplet.sql"

printf 'Backing up the local database...\n'
local_wp --skip-plugins --skip-themes db export "$LOCAL_BACKUP" --add-drop-table --quiet
gzip -f "$LOCAL_BACKUP"
printf 'Local rollback backup: %s.gz\n' "$LOCAL_BACKUP"

printf 'Exporting the droplet database...\n'
ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
	"wp --allow-root --path='$REMOTE_WP_PATH' --skip-plugins --skip-themes db export - --add-drop-table --quiet" \
	> "$REMOTE_DUMP"

[ -s "$REMOTE_DUMP" ] || die "The droplet export is empty. Local was not changed."
grep -q 'CREATE TABLE' "$REMOTE_DUMP" || die "The droplet export does not look like a SQL dump. Local was not changed."

printf 'Importing the droplet database into Local...\n'
local_wp --skip-plugins --skip-themes db import "$REMOTE_DUMP"
local_wp search-replace \
	"$REMOTE_URL" "$LOCAL_URL" \
	--all-tables-with-prefix --precise --skip-columns=guid --report-changed-only
local_wp --skip-plugins --skip-themes option update home "$LOCAL_URL"
local_wp --skip-plugins --skip-themes option update siteurl "$LOCAL_URL"
local_wp cache flush
local_wp rewrite flush

ACTUAL_HOME="$(local_wp --skip-plugins --skip-themes option get home)"
[ "${ACTUAL_HOME%/}" = "$LOCAL_URL" ] || die "Local URL verification failed. Backup: $LOCAL_BACKUP.gz"

prune_local_backups

printf 'Local database now matches the droplet.\n'
printf 'Rollback backup: %s.gz\n' "$LOCAL_BACKUP"
