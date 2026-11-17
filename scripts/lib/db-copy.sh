# Shared helpers for pull-db-from-droplet.sh and push-db-to-droplet.sh.
# Source this file; it defines functions only.

# Print the posts that copying SOURCE over TARGET would lose. Both arguments
# are files written by lib/content-inventory.php (ID, type, slug, status,
# modified GMT). Prints nothing when the copy loses no work.
find_overwrites() {
	awk -F '\t' '
		NR == FNR { type[$1] = $2; name[$1] = $3; mod[$1] = $5; next }
		!($1 in type)                    { printf "  only on this side:  #%s %s \"%s\" (%s)\n", $1, $2, $3, $5; next }
		type[$1] != $2 || name[$1] != $3 { printf "  different post:     #%s %s \"%s\" (%s)\n", $1, $2, $3, $5; next }
		$5 > mod[$1]                     { printf "  edited later here:  #%s %s \"%s\" (%s, other side %s)\n", $1, $2, $3, $5, mod[$1] }
	' "$1" "$2"
}

# Comma-separated WP Mail SMTP log tables for a wp-cli runner function name.
# Usage: mail_log_tables local_wp
mail_log_tables() {
	"$1" --skip-plugins --skip-themes db tables '*wpmailsmtp_*' --all-tables-with-prefix --format=csv 2>/dev/null || true
}
