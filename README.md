# Task - PHP - Refactoring

Around 3 hours have been used to finalize the task.

## Setup

PHP 8.2+ can be used to run the task, either locally or by running the docker container (`docker compose run --rm php`)  

* first, install the composer dependencies
* the exchange rate API needs an access key (tests excluded). You can provide it by creating a .env.local file and filling the `EXCHANGE_RATES_API_KEY` variable
* then, you can run the script by running `php console calculate data/input.txt`
* or the tests, by running `php vendor/bin/phpunit tests`


Cache has been implemented on all the external API calls, to prevent possible API calls throttling.   


In the context of this task, with our external APIs having only one route, a single service has been created. 
With more usage and additional use cases, more effort will be invested in separating the infrastructure part 
from the individual API responses as DTOs and the application parts that utilize them for different use cases.


Furthermore, in a production environment, more would be done to improve traceability / replayability 
(saving failing lines in a separate files, logging, etc.), but it seemed out of scope for this task.
