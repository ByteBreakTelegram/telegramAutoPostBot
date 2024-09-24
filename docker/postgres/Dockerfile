FROM postgres:15.1

COPY initdb.d/ /docker-entrypoint-initdb.d/

# Set Workdir
WORKDIR /app

COPY postgresql.conf.tpl /etc/postgresql/postgresql.conf

CMD ["-c", "config_file=/etc/postgresql/postgresql.conf"]
