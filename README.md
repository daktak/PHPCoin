
Requires bitcoind/litecoind, php with mysqli, mysql and a web server.

Allows multiple users to manage multiple wallets and payments.

Add something like the following to your crontab
    0 */4 * * * /usr/bin/php /var/www/localhost/htdocs/PHPCoin/phpcoin-cron/cron.php >> /var/log/phpcoin.log

![ScreenShot](https://raw.github.com/daktak/PHPCoin/master/screenshots/main.png)

![ScreenShot2](https://raw.github.com/daktak/PHPCoin/master/screenshots/accounts.png)
