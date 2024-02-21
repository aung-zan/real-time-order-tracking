## About this app

This app is made on laravel framework(v10) and use Pusher for tracking real time orders progress.

### Requirement

- php (8.3.3)
- mysql (8.3.0)
- composer (2.6.5)

### Project Setup

- After all the requirements are installed, go to project folder and run ```composer install``` to install necessary packages.
- we can start the project by running ```php artisan server``` command.
- To add database and data, run ```php artisan migrate --seed```. [There is a seeder file for drivers table.]
- There are two commands, ``AssignFreeDriver`` and ``ChangeOrderProgress``, which will need to run every minute.
- So, we need to add ```php artisan schedule:run``` command to cron job.
- For example, ```* * * * * /opt/homebrew/bin/php ~/Develop/php/api-1/artisan schedule:run >> /dev/null 2>&1```
- channel name is ```order.order_id``` and event name is ```OrderStatusUpdated```.

### Additional Package
- pusher/pusher-php-server

### Client Side
- Simple client side is added in client folder.

### If there is technical or project setup error, please feel free to contact me.