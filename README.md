# Colloquium nomination and voting system

**Current maintainer:** Michael Yoshitaka Erlewine <mitcho@mitcho.com>

## The MIT License (MIT)

Copyright (c) Omer Preminger and contributors

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

**Code contributors:** Michael Yoshitaka Erlewine

## Installation

**Prerequisites:** a LAMP (Linux Apache MySQL PHP) server environment is expected. The current release has been tested on Apache 2.2, PHP 5.6, MySQL 5.0.11. (Python is also currently required for vote tabulation.)

1.	Copy `colloq` into your web directory.
	Some Apache details in case there is trouble: the relevant directory should be given `AuthOverride All` so that the `htaccess` file will work properly, and `DirectoryIndex` should include `index.php`.
2.	Set up authentication.
	- Authentication is done via [Apache HTTP basic password authentication](https://wiki.apache.org/httpd/PasswordBasicAuth). Following the instructions there, use the `htpasswd` utility to create a passwords file in a directory which is readable by the server but not by the world. Register all your users and passwords there. Usernames should be email addresses.
	- Edit the `.htaccess` file so that the `AuthUserFile` directive points towards your passwords file.
3.	Create a MySQL database and load the code `SCHEMA.sql` which will set up the requisite tables.
4.	Edit the `config.php` file. Some highlights:
	- the variable `$phase` is used to switch between different phases of the nomination and voting process
	- the variables `$voting_list` and `$nomination_list` are arrays including all the usernames who can participate in each round. These have to match the usernames that are used for Apache basic auth (above) and should be emails. `$superusers` is an array of usernames that additionally have admin priviledges.
	- the variable `$db_settings` should be set with your database details.
5.	Finally, when the votes are ready to be tabulated, the python script in `scripts/tally.py` is used. The database credentials have to be reentered in that script as well. (In the future, this vote tabulation script should be ported into PHP as well -- Issue #3.)
