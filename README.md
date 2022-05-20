# Snowtricks
This site was created for a PHP / Symfony developer training project OpenClassrooms

## Description
SnowTricks is a community website for snowboarders.

* Trick list and description are visible for all visitors
* Registered users are allowed to comment tricks and add/edit their own tricks, and edit their profile
* Moderators and admin are allowed to administrate all tricks and comments
* Admin are allowed to administrate users and especially user roles

## Requirement
Symfony 5.4 / Bootstrap 4 project
Installation
1 - Git clone the project

    https://github.com/MikaidouMB/SnowTricks.git
2 - Install libraries

    php bin/console composer install
3 - Create database

a) Update DATABASE_URL .env file with your database configuration.
            DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
        
b) Create database:
            php bin/console doctrine:database:create
        
c) Create database structure:
            php bin/console make:migration
        
d) Insert fictive data (optional)
            php bin/console doctrine:fixtures:load
        
4 - Configure MAILER_DSN of Symfony mailer in .env file

## Usage
For administrator access :

- if you used fictive data (see 3-d)), you can login with following password :
        password : azerty123456
    
- if you did not use fictive data:
    - create a user account with sign up form
    - activate your account by following the activation link
    - to have admin role, go to your database and replace ["ROLE_USER"] with ["ROLE_ADMIN"]
    
### __Server__
You need a web server with PHP8 and MySQL DBMS.  
Versions used in this project:
* Apache 2.4.46
* PHP ≥ 8.0
* MySQL 5.7.31
* Symfony 5.4.6

### __Languages and libraries__
This project is coded in __PHP8__, __HTML5__, __CSS3__ and __JS__.  
Dependencies manager: __Composer__  
PHP packages, included via Composer:
* Symfony/Dotenv ^5.4 ([more info](https://github.com/symfony/dotenv)) 
* "LiipImagineBundle"  
  CSS/JS libraries, included via CDN links:
* Bootstrap 4
* Font-awesome ^5.15.1
* "twig/twig": "^3.0",
* Jquery 3.6
_NB: If you want to customize Bootstrap, install it in your project instead of using CDN links ([more info](https://getbootstrap.com/))._

---
## Installation

### __Configure environment variables__
1.  Open the ___.env.example___ file
2.  Replace the example values with your own values (Database, SMTP, and default Admin info)
3.  Rename the file ___.env___
```env
# .env
DB_HOST=host_name
DB_NAME=db_name
DB_CHARSET=utf8
DB_USERNAME=username
DB_PASSWORD=password
SMTP_HOST=host
SMTP_USERNAME=username
SMTP_PASSWORD=password
ADMIN_EMAIL=your@email.com
ADMIN_NAME=yourname
```

#### ___DB schema___
![Templates tree](diagrammes/data model.png)
![Templates tree](diagrammes/Ajout_figure_et_commentaire.png)
![Templates tree](diagrammes/cas_utilisations.png)
![Templates tree](diagrammes/classes.png)
![Templates tree](diagrammes/Validation_commentaire_et_assignation_role.png)

* Clone :
```bash
git clone https://github.com/MikaidouMB/BlogProject.git
```
---  
### __Install Composer__
1.  Install __Composer__ by following [the official instructions].(https://getcomposer.org/download/).
2.  Go to the project directory in your cmd:
```
$ cd some\directory
```
3.  Install dependencies with the following command:
```
$ composer install
```
Dependencies should be installed in your project (check _vendor_ directory).

---
## Vendor
* [Twig](https://twig.symfony.com/doc/2.x/tags/if.html)
* [Font Awesome](https://fontawesome.com/)

## Améliorations possibles
* Write Services
* Notification developpement
* Medias(photo) management
