Steps:
Update Composer
$ sudo /usr/local/bin/composer self-update

Install Laravel to Project Folder
$ composer create-project laravel/laravel {directory} 4.2 --prefer-dist

Enter Database details in app/config/database.php
Make the files writeable : sudo chmod 775 /Path-to-app/app/config/database.php

Create a Migration
$ sudo php artisan migrate:make create_sents_table --create=sents

Create Sent Model to interact with DB

For Now lets Seed our Database

Call this Seeder file from Database Seeder - app/database/seeds/DatabaseSeeder.php

Run DB Seed
$ sudo php artisan db:seed

Create the Sent Resource Controller
php artisan controller:make CommentController --only=index,store,destroy

Create the routes at app/routes.php

Test your routes with 
$ sudo php artisan routes


====
Angular files are placed in public --> airtime/public

in public/js/services write the Angular $http service
in public/js/controllers write the SentController

in public/js/ write the Angular app.js

===
Write the index.php in app/views

===
Run 
$php artisan serve