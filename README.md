# Colloquium nomination and voting system

Copyright Omer Preminger and contributors
Current maintainer: Michael Yoshitaka Erlewine <mitcho@mitcho.com>

This software is provided AS IS. There is no active support or guarantee of future maintenance.

# Installation

**Prerequisite:** a LAMP (Linux Apache MySQL PHP) server environment is expected. The current release has been tested on Apache 2.2, PHP 5.6, MySQL 5.0.11.

1.	Copy `colloq` into your web directory.
	Some Apache details in case there is trouble: the relevant directory should be given `AuthOverride All` so that the `htaccess` file will work properly, and `DirectoryIndex` should include `index.php`.
2.	Set up authentication. Authentication is done via [Apache HTTP basic password authentication](https://wiki.apache.org/httpd/PasswordBasicAuth). Following the instructions there, use the `htpasswd` utility to create a passwords file in a directory which is readable by the server but not by the world. Register all your users and passwords there. *Usernames should be email addresses.*
3.	Create a MySQL database and load the code `SCHEMA.sql` which will set up the requisite tables.
4.	Edit the `config.php` file. Some highlights:
	- the variable `$phase` is used to switch between different phases of the nomination and voting process
	- the variables `$voting_list` and `$nomination_list` are arrays including all the usernames who can participate in each round. These have to match the usernames that are used for Apache basic auth (above) and should be emails. `$superusers` is an array of usernames that additionally have admin priviledges.
	- the variable `$db_settings` should be set with your database details.
