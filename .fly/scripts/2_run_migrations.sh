
if [ -z "$RELEASE_COMMAND" ]; then
    echo "not release"
else
    php artisan migrate
fi

