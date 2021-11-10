#!/bin/sh
set +e;

HTPASSWD_GREETING="${HTPASSWD_GREETING:-Sie greifen auf ein Testsystem der DDB zu. Bitte geben als Benutzer 'testsystem' und als Passwort ebenfalls 'testsystem' ein.}";
# HTPASSWD_USER -> HTTP Basic Auth User
# HTPASSWD_PWD -> HTTP Basic Auth Password

if [ -n "${HTPASSWD_USER}" ] && [ -n "${HTPASSWD_PWD}" ];
then
  echo "Setting HTTP Auth for nginx...";
  printf '%s:%s\n' "${HTPASSWD_USER}" "$(openssl passwd -crypt "${HTPASSWD_PWD}")" > /etc/nginx/.authpasswd;
  {
    echo "# configuration file /etc/nginx/auth.conf:";
    echo "";
    echo "auth_basic \"${HTPASSWD_GREETING}\";";
    echo "auth_basic_user_file /etc/nginx/.authpasswd;";
  } > /etc/nginx/auth.conf;
else
  echo "Removing HTTP Auth for nginx...";
  {
    echo "# do not delete";
  } > /etc/nginx/.authpasswd;
  {
    echo "# configuration file /etc/nginx/auth.conf:";
    echo "# do not delete";
  } > /etc/nginx/auth.conf;
fi;

set -e;

docker-php-entrypoint;

exec "$@";
