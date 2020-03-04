The module integrates the [inkifi.com](https://inkifi.com) website with the [mediaclip.ca](https://www.mediaclip.ca) service.  

## How to install
```
bin/magento maintenance:enable      
rm -rf composer.lock
php -d memory_limit=-1 /usr/bin/composer clear-cache
php -d memory_limit=-1 /usr/bin/composer require inkifi/mediaclip:*
rm -rf var/di var/generation generated/code
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme Infortis/ultimo \
	-f en_US en_GB
bin/magento maintenance:disable
```

## How to upgrade
```
bin/magento maintenance:enable      
php -d memory_limit=-1 /usr/bin/composer remove inkifi/mediaclip-legacy
rm -rf composer.lock
php -d memory_limit=-1 /usr/bin/composer clear-cache
php -d memory_limit=-1 /usr/bin/composer require inkifi/mediaclip-legacy:*
rm -rf var/di var/generation generated/code
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme Infortis/ultimo \
	-f en_US en_GB
bin/magento maintenance:disable
```