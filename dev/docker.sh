#!/usr/bin/env bash
# Throwaway WordPress for developing the theme. Nothing here touches any
# other WordPress install: one network, one database, one site, all named
# gexis-dev-*, with this theme mounted read-only.
#
#   dev/docker.sh up      start, install, activate the theme, seed sample content
#   dev/docker.sh down    remove everything it created
#
# Site: http://localhost:8088  (admin / admin)
set -euo pipefail
cd "$(dirname "$0")/.."
THEME="$PWD"
NET=gexis-dev
PORT="${GEXIS_DEV_PORT:-8088}"

wp() {
	docker run --rm --network "$NET" --user 33:33 \
		--volumes-from gexis-dev-wp \
		-e WORDPRESS_DB_HOST=gexis-dev-db -e WORDPRESS_DB_USER=wp -e WORDPRESS_DB_PASSWORD=wp -e WORDPRESS_DB_NAME=wp \
		wordpress:cli-php8.3 wp "$@"
}

case "${1:-}" in
up)
	docker network inspect "$NET" >/dev/null 2>&1 || docker network create "$NET" >/dev/null
	docker run -d --name gexis-dev-db --network "$NET" \
		-e MARIADB_DATABASE=wp -e MARIADB_USER=wp -e MARIADB_PASSWORD=wp -e MARIADB_RANDOM_ROOT_PASSWORD=1 \
		mariadb:11 >/dev/null
	docker run -d --name gexis-dev-wp --network "$NET" -p "127.0.0.1:$PORT:80" \
		-e WORDPRESS_DB_HOST=gexis-dev-db -e WORDPRESS_DB_USER=wp -e WORDPRESS_DB_PASSWORD=wp -e WORDPRESS_DB_NAME=wp \
		-e WORDPRESS_DEBUG=1 \
		-v "$THEME:/var/www/html/wp-content/themes/gexis:ro" \
		wordpress:php8.3-apache >/dev/null
	echo "waiting for the database…"
	for _ in $(seq 1 60); do
		docker exec gexis-dev-db healthcheck.sh --connect >/dev/null 2>&1 && break
		sleep 2
	done
	until docker exec gexis-dev-wp test -f /var/www/html/wp-config.php; do sleep 1; done
	sleep 2
	wp core install --url="http://localhost:$PORT" --title=gexis --admin_user=admin --admin_password=admin --admin_email=admin@example.invalid --skip-email
	wp theme activate gexis
	wp rewrite structure '/%year%/%monthnum%/%postname%/'
	wp eval-file wp-content/themes/gexis/dev/seed.php
	wp rewrite flush
	echo "http://localhost:$PORT  (admin / admin)"
	;;
down)
	docker rm -f gexis-dev-wp gexis-dev-db >/dev/null 2>&1 || true
	docker network rm "$NET" >/dev/null 2>&1 || true
	;;
wp)
	shift
	wp "$@"
	;;
*)
	echo "usage: dev/docker.sh up|down|wp <args>" >&2
	exit 1
	;;
esac
