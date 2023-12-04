FROM php:8.2-cli
RUN pear install PHP_CodeSniffer
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

