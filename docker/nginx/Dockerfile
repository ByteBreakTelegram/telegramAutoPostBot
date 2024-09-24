FROM nginx:1.21.0
# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
COPY logrotate.d/nginx /etc/logrotate.d/nginx
CMD ["nginx", "-g", "daemon off;"]
