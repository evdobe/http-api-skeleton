#!/bin/bash
PARENT_PATH=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

AUTHORS="["
readarray -t AUTHOR_LINES <<<"$PROJECT_AUTHORS"
for AUTHOR_STR in "${AUTHOR_LINES[@]}"; do
    IFS=';' read -ra AUTHOR <<< "$AUTHOR_STR"
    if [ -n "$AUTHOR" ]; then
        AUTHORS="${AUTHORS}{"
        AUTHOR_NAME=`echo ${AUTHOR[0]} | sed 's/ *$//g'`
        AUTHOR_EMAIL=`echo ${AUTHOR[1]} | sed 's/ *$//g'`
        if [ -n "$AUTHOR_NAME" ]; then
            AUTHORS="${AUTHORS}\"name\":\"${AUTHOR_NAME}\""
        fi
        if [ -n "$AUTHOR_EMAIL" ]; then
            AUTHORS="${AUTHORS}, \"email\":\"${AUTHOR_EMAIL}\""
        fi
        AUTHORS="${AUTHORS}},"
    fi
done;
AUTHORS="${AUTHORS::-1}]"

echo $AUTHORS


if [ ! -f composer.json ]; then
    runuser -l hostuser -c "cp $PARENT_PATH/assets/composer.json composer.json"
    runuser -l hostuser -c "sed -i 's/\${PROJECT_NAME}/$PROJECT_ORG\/$PROJECT_NAME/g; s/\${PROJECT_DESCRIPTION}/$PROJECT_DESCRIPTION/g; s/\"\${PROJECT_AUTHORS}\"/$AUTHORS/g' composer.json"
    runuser -l hostuser -c "composer require --dev phpunit/phpunit behat/behat"
    runuser -l hostuser -c "composer require mezzio/mezzio-swoole"
    # runuser -l hostuser -c "composer require php-di/php-di doctrine/annotations"
fi
runuser -l hostuser -c "composer install"