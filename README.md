# Gain.Booking
Laravel based booking application to be sold on marketplace


## Server Requirements

* PHP >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Ctype PHP Extension
* JSON PHP Extension
* ZIP PHP Extension

## How to run the application

* Rename .env.example file to .env inside your project root or (command mac terminal cd your project root directory and run - `cp .env.example .env` you get .env file) and fill the database information.
* Open the console and cd your project root directory
* Run `composer install` or `php composer.phar install` (if vendor directory not available )
* Run `php artisan key:generate`
* Now open project directory in root find the `.env` file. In the .env file fill in the `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` options to match the credentials of the database you just created. This will allow us to run migrations and seed the database in the next step.
* Run `php artisan migrate`
* Run `php artisan db:seed` to run seeders.

##Login admin user/password:
* login url: https://booking.dev.gainhq.com/login
* Email: admin@demo.com
* Password: 123456

## Complete settings
* Got to setting manu
* Application setting: Set all the fields as your choice.
* Email setting: Set application name as your choice.
  
  - Set your Email address as email [ N.B: access your email address for this application from your email if needed.].
  
  - Select email driver. If smtp:- 
  
    - Type email host name.[ example : for gmail `smtp.gmail.com` ]
  
    - Type the port number :- [ example : for gmail port `587` ]
    
    - Type password :- password of that email with is used as the email address.

    - Select Encryption type :- Port `465` (SSL required), Port `587` (TLS required).
                                                
**N.B:-** Be carefull about `Email Setting` which is must needed for sending auto email notification. If you don't complete the `Email Setting` some functionalities will not working properly.

Other settings set as you need.

## If no email validation needed for clients registrations
* Just create a variable in .env
  AUTO_VALIDATE_CLIENT_EMAIL = true
 
**Note: It is important for demo version.

## Pull in old project
* After pull, you need to migrate your project database. When you run migrate command, you may face some errors. Becuse of your `composer` and `Vendor` is older.
* Update composer. Run this command  `composer update` or run `composer dump-autoload`
* Before run migration, backup your old database. After new migrattion you lost old database record. Run this command `php artisan migrate:refresh --seed` this command refresh db & seeding demo data.
* You need to clear cache. Run this command `php artisan cache:clear`

