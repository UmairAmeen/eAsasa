# Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

Inventory ENVs:

1.	SHOW_SMS_CREDENTIALS=true (to make SMS API credentials visible in the settings  profile menu. Credentials contain SMS-URL, SMS-MASK, SMS-USER-NAME, and SMS-PASSWORD).
2.	MERGE_CUSTOMER_SUPPLIER=true (this check is for ALI SHAN(shan_spec) in which customers and suppliers are same)
3.	AREA_ENABLE=true (this is a check for the clients which use area (length * width) along with length and width).
4.	DELIVERY_DATE=false (this check is for clients who don’t want to see the delivery date on sales order page and on invoice as well)
5.	RELATED_TO_SOURCE=true (this check is for clients who don’t want to see the RELATED TO and SOURCE on sales order page and on invoice as well)
6.	QTY_TO_DELIVERED=false (this check is for clients who don’t want to see the delivered products details on sales order show page)
7.	SALES_PERSON=true (this check is for clients who want to manually write name of SALES PERSON on sales order creation page and on invoice as well. Set it to false if you want use system generated SALES PERSON).
8.	MANUAL_MODE=true (this check is for clients who want to set a manual TOTAL price on sales order page regardless of the (PRODUCTS_PRICE * QTY) and on invoice as well)
9.	UUID=false (this check is for clients who don’t want to put a note on each product row on sales order page and on invoice as well)
10.	HR_ENABLE=true (this check is for clients who want to use HR system in eASASA as well) 
11.	RAZA=false (this check is for client named RAZA TRACTOR(SERVER_INSTANCE_NAME  MUHAMMAD ALI INVENTORY) who rejected most of the updated features of eAsasa)
12.	SALEPRICE_EDITABLE=true (this check is for clients who want to restrict their employees from editing the sale price on the time of sale to avoid corruption)
13.	ORDER_BY_NAME='products.name' (this check is for downloading products excel file with the sorting of name. it can be change to sort by ID, BARCODE or BRAND, as per client's choice)
14.	SHOES_COMPANY=true (this check is for SHOE CLIENTS who want to get urdu/english language along with mandatory color and size on invoice)
15.	DOT_COM=true (this check is for DOT COM(shoes) client who want to see price of a product(dozen of shoe pair) and  rate per pair as well on invoice)
16.	POSID=146177 (this variable is for  FBR POSID, but this has been moved to SETTINGS FBR INTEGRATION)
17.	TEST_POSID=987240 (this variable is for TEST FBR POSID, but this has been moved to SETTINGS  FBR INTEGRATION)
18.	FBR_PRODUCTION='https://gw.fbr.gov.pk/imsp/v1/api/Live/PostData' (this variable is for  FBR POSID, but this has been moved to SETTINGS  FBR INTEGRATION)
19.	FBR_SANDBOX='https://esp.fbr.gov.pk:8244/FBR/v1/api/Live/PostData' (this variable is for TEST FBR URL, but this has been moved to SETTINGS FBR INTEGRATION)
20. FURNITURE_GALLERY=false (this variable is for FURNITURE GALLERIA client, because he is adding quantity of products via INVENTORY IN procedure instead of making purchases, due to which sale order profit report is not getting the purchase value. as a solution we have added this check and get the purchase price from supplier price records to facilitate the client)
# eAsasa
