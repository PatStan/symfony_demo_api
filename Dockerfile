FROM public.ecr.aws/propeller/symfony:8.4

ARG APP_ENV
ENV APP_ENV "$APP_ENV"
ENV PROPCOM_PHP_OPCACHE_PRELOAD "/app/config/preload.php"

# copy entrypoint actions
COPY docker/entrypoint-actions/ /etc/docker-entrypoint/actions.d/

# copy application files
COPY .env /app/.env
COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock
COPY symfony.lock /app/symfony.lock
COPY bin /app/bin
COPY config /app/config
COPY migrations /app/migrations
COPY public /app/public
COPY src /app/src
COPY vendor /app/vendor

# create the /app/var folder & set appropriate permissions
RUN mkdir /app/var && chown -Rf www-data:www-data /app/var && chmod -Rf 777 /app/var
