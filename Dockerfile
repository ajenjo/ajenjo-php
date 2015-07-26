# with php 5.4
FROM jondotsoy/phpdev

EXPOSE 80

COPY composer.json /project/
COPY docs/ /project/
COPY src/ /project/
COPY tests/ /project/

# Install requerimients
RUN php /usr/local/bin/composer install

CMD ["php", "-S", "0.0.0.0:80"]
