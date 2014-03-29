jenkins-facebook-integration
============================

Post jenkins build status data to Facebook. Licensed under the GPLv3.

Installation instructions
-------------------------

* Clone this repository and execute [Composer](https://getcomposer.org/doc/00-intro.md#installation-nix) with `php composer.phar update`
* Open the file `resources/config/prod.php` and modify the settings (especially the database config and Facebook app settings)
	* Optionally copy the the file `dev.php.dist` to `dev.php` to enable the development front-controller
* In a console, execute `php ./console doctrine:schema:load` to insert the needed schema into the database. The database may be created using `php ./console doctrine:database:create` commmand previously.
* In your webserver configuration, set the web root directory to the `web` subdirectory
	* To call the application using the development front-controller explicitly, append the filename of the dev-controller to the URL. Example: `http://localhost/jenkins-facebook-integration/index_dev.php/`
* Add a cron job to run the command `php ./console social:accesstokens:update` task periodically (like once per month). This command updates all the Facebook access tokens. Otherwise tokens lose their validity after 60 days.

Usage instructions
------------------

* Configure your Facebook app (domain settings, oauth settings, permitted users)
* Open this web application and login using Facebook with the account that should be used for status postings
    * Pay attention: The first user that logs into the web application automatically gets admin permissions 
* Create a project (you need to be member of a Facebook group for this to succeed)
* After creating the project you are able to see a notification url in the project properties
* Using this url you are able to submit build status changes to the application using the [Jenkins notification plugin](https://wiki.jenkins-ci.org/display/JENKINS/Notification+Plugin) via HTTP
