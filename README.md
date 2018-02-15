💎 EOS Explorer API
========================

The "EOS Explorer API" is a PHP Backend based on Symfony3 that connects to a MongoDB database.

📌 Requirements
------------

  * PHP 5 or higher;
  * Mongodb PHP extension enabled;
  * Apcu extension suggested
  * and the [usual Symfony application requirements][1].
  
📌 Installing Composer
------------

Composer is the dependency manager used by modern PHP applications and it can also be used to create new applications.

Download the installer from [getcomposer.org/download](https://getcomposer.org/download/), execute it and follow the instructions.


📌 Installation
------------

Execute this command to install the project:

```bash
$ composer install
```

📌 Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd eos-explorer-api/
$ php bin/console server:run
```

Alternatively, you can [configure a fully-featured web server][2] like Nginx
or Apache to run the application.

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html