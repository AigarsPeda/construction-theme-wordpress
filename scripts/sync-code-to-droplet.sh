#!/usr/bin/env bash

set -Eeuo pipefail
umask 077

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

SSH_KEY="${SSH_KEY:-/Users/aigarspeda/.ssh/digitalocean_construction}"
REMOTE_HOST="${REMOTE_HOST:-root@201.79.12.67}"
REMOTE_WP_PATH="${REMOTE_WP_PATH:-/var/www/construction}"
REMOTE_BACKUP_DIR="${REMOTE_BACKUP_DIR:-/var/backups/construction}"
REMOTE_URL="${REMOTE_URL:-https://oaksbuild.lv}"
LOCAL_THEME_PATH="${LOCAL_THEME_PATH:-$PROJECT_ROOT/theme/construction}"
REMOTE_THEME_PATH="$REMOTE_WP_PATH/wp-content/themes/construction"
LOCAL_MU_PATH="${LOCAL_MU_PATH:-$PROJECT_ROOT/mu-plugins}"
REMOTE_MU_PATH="$REMOTE_WP_PATH/wp-content/mu-plugins"

DRY_RUN=0

usage() {
	cat <<'EOF'
Usage: ./scripts/sync-code-to-droplet.sh [--dry-run]

Synchronize the local Construction theme and the repo's MU plugins with the
DigitalOcean droplet.
  - The remote theme directory becomes an exact copy of the local theme.
  - Each mu-plugins/*.php file from the repo is added or updated in
    wp-content/mu-plugins; other MU plugins on the server are left alone.
Apply runs save a private archive of the live theme and MU plugins first.

Environment overrides:
  SSH_KEY, REMOTE_HOST, REMOTE_WP_PATH, REMOTE_BACKUP_DIR, REMOTE_URL,
  LOCAL_THEME_PATH, LOCAL_MU_PATH
EOF
}

die() {
	printf 'Error: %s\n' "$*" >&2
	exit 1
}

for arg in "$@"; do
	case "$arg" in
		--dry-run)
			DRY_RUN=1
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
command -v rsync >/dev/null 2>&1 || die "rsync is not installed."
command -v shasum >/dev/null 2>&1 || die "shasum is not installed."
command -v curl >/dev/null 2>&1 || die "curl is not installed."

[ -r "$SSH_KEY" ] || die "SSH key not found: $SSH_KEY"
[ -d "$LOCAL_THEME_PATH" ] || die "Local theme not found: $LOCAL_THEME_PATH"
[ -f "$LOCAL_THEME_PATH/style.css" ] || die "Local theme is missing style.css."

case "$REMOTE_THEME_PATH" in
	*/wp-content/themes/construction)
		;;
	*)
		die "Refusing to synchronize unexpected remote path: $REMOTE_THEME_PATH"
		;;
esac
case "$REMOTE_BACKUP_DIR/" in
	"$REMOTE_WP_PATH/"*) die "Backups must be outside the public WordPress directory." ;;
esac

SSH_ARGS=(-i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=10)
RSYNC_SSH="ssh -i $SSH_KEY -o BatchMode=yes -o ConnectTimeout=10"

ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
	"test -d '$REMOTE_THEME_PATH' && test -f '$REMOTE_THEME_PATH/style.css'" \
	|| die "Remote Construction theme was not found at $REMOTE_THEME_PATH"

RSYNC_ARGS=(
	--archive
	--no-owner
	--no-group
	--compress
	--checksum
	--delete-after
	--itemize-changes
	--exclude=.DS_Store
)

if [ "$DRY_RUN" -eq 1 ]; then
	RSYNC_ARGS+=(--dry-run)
	printf 'Dry run. No remote files will change.\n'
else
	REMOTE_BACKUP="$(ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
		"umask 077; mkdir -p '$REMOTE_BACKUP_DIR' && mktemp -d '$REMOTE_BACKUP_DIR/theme-$(date +%Y%m%d-%H%M%S).XXXXXXXX'")"
	ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
		"umask 077; tar -czf '$REMOTE_BACKUP/theme.tar.gz' -C '$(dirname "$REMOTE_THEME_PATH")' construction"
	printf 'Private theme rollback archive: %s/theme.tar.gz\n' "$REMOTE_BACKUP"
	ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
		"umask 077; if [ -d '$REMOTE_MU_PATH' ]; then tar -czf '$REMOTE_BACKUP/mu-plugins.tar.gz' -C '$(dirname "$REMOTE_MU_PATH")' mu-plugins; fi"
	printf 'Private MU plugin rollback archive: %s/mu-plugins.tar.gz\n' "$REMOTE_BACKUP"
fi

rsync "${RSYNC_ARGS[@]}" -e "$RSYNC_SSH" \
	"$LOCAL_THEME_PATH/" \
	"$REMOTE_HOST:$REMOTE_THEME_PATH/"

# MU plugins: add/update the repo's files only (no --delete).
if [ -d "$LOCAL_MU_PATH" ]; then
	MU_RSYNC_ARGS=(--archive --no-owner --no-group --compress --checksum --itemize-changes --include='*.php' --exclude='*')
	if [ "$DRY_RUN" -eq 1 ]; then
		MU_RSYNC_ARGS+=(--dry-run)
	fi
	ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" "mkdir -p '$REMOTE_MU_PATH'"
	rsync "${MU_RSYNC_ARGS[@]}" -e "$RSYNC_SSH" \
		"$LOCAL_MU_PATH/" \
		"$REMOTE_HOST:$REMOTE_MU_PATH/"
fi

if [ "$DRY_RUN" -eq 1 ]; then
	exit 0
fi

ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" \
	"chown -R www-data:www-data '$REMOTE_THEME_PATH' '$REMOTE_MU_PATH' && wp --allow-root --path='$REMOTE_WP_PATH' cache flush && wp --allow-root --path='$REMOTE_WP_PATH' rewrite flush"

LOCAL_HASH="$(shasum -a 256 "$LOCAL_THEME_PATH/functions.php" | awk '{print $1}')"
REMOTE_HASH="$(ssh "${SSH_ARGS[@]}" "$REMOTE_HOST" "sha256sum '$REMOTE_THEME_PATH/functions.php'" | awk '{print $1}')"

[ "$LOCAL_HASH" = "$REMOTE_HASH" ] || die "Remote verification failed: functions.php hashes differ."

curl --fail --silent --show-error --location "$REMOTE_URL/" >/dev/null

printf 'Theme synchronized and verified at %s\n' "$REMOTE_URL"
