# Getting Started

### Prerequisites

Make sure you have PHP>=8.1 and composer2 (dependency manager for php) installed.

### Installation

1. `git clone https://github.com/yinchyy/php-gold-prices.git`
2. `cd php-gold-prices`
3. `composer install`

In current state, application fetches data about gold prices from api in every request.
To prevent this I've been trying to implement caching in two approaches, first using CacheInterface,
latter using sqlite3.
I would go with second option, because it would be more flexible, but I've encountered troubles during setup.

The second option would be helpful with unit tests, as those could be improved as well, as they rely on data from API, so false negatives are possible.

I wonder if storing received invalid JSON in logs is safe, as it might contain malicious code, so I didn't attach that data to that log message.

I've had plenty of fun with this project and I'm glad I've had an opportunity to take on this challenge.

Feedback is appreciated.
