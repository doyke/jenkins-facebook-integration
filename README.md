jenkins-facebook-integration
============================

Post jenkins build status data to Facebook. Licensed under the GPLv3.

Installation instructions
-------------------------

* Clone this repository and execute [Composer](https://getcomposer.org/doc/00-intro.md#installation-nix) with `php composer.phar update`
* Open the file `resources/config/prod.php` and modify the settings (especially the database config and facebook app settings)
	* Optionally copy the the file `dev.php.dist` to `dev.php` to enable the development front-controller
* In a console, execute `php ./console doctrine:database:create` to insert the needed schema into the database.
* In your webserver configuration, set the web root directory to the `web` subdirectory.
