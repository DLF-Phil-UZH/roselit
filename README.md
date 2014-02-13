RoSeLit
=======

Literature management for the "Romanisches Seminar" of the University of Zurich

# Installation
This installation manual describes how RoSeLit can be installed on a standard webhosting environment at the University of Zurich.

## 1 Setup a database
Create a new MySQL-database and use the SQL-File 'create_db.sql' to create the tables for RoSeLit.
In this installation guide, the following database properties are used as an example:

	hostname: mysql.example.com
	username: demo
	password: demo
	database: demo

## 2 Create a directory for the pdf files
Create a new directory where for the pdf files that are being upload via RoSeLit. This directory should not be in your document root and not accessible via the web, but must be writable by the php process. At the University of Zurich create this directory in the same location where the directory "public_html' is located (not inside 'public_html'!). Set the rights for this directory to 777.

In this installation guide, the following directory is created:

	/usr/local/ftp/test/roselitfiles


## 3 Upload files to the server
Create a new directory for RoSeLit on your webserver or just upload the files to your document root, if you only install RoSeLit.

In this guide, the files are being uploaded to:

	/usr/local/ftp/test/public_html/demo/roselit

## 4 Configure Apache Webserver

### 4.1 Edit .htaccess in the base directory of roselit
- Set the RewriteBase to the full filesystem path to the directory in which you uploaded the roselit files (where your index.php and the .htaccess are located) (no trailing slash):

	RewriteBase /demo/roselit

- Add the URL under which the RoSeLit installation can be accessed to the RewriteRule. This makes sure, that all Requests are redirected to https:

	RewriteRule ^(.*) https://www.example.com/demo/roselit/$1 [L]

### 4.2 Edit .htaccess in the subdirectory api/
Set the RewriteBase to the url path to the directory in which you uploaded the roselit files (where your index.php and the .htaccess are located) (no trailing slash):

	RewriteBase /demo/roselit/api

Add the full URL under which the RoSeLit installation can be accessed to the RewriteRule. This makes sure, that all Requests are redirected to https:

	RewriteRule ^(.*) https://www.example.com/demo/roselit/api/$1 [L]

Both settings should be the same as in step 4.1 with "/api" added at the end.

Set AuthUserFile to the full filesystem path to your .htpasswd file.

	AuthUserFile /usr/local/ftp/test/public_html/demo/roselit/api/.htpasswd';

### 4.3 Create a file .htpasswd in the subdirectory api/

Choose a username and generate a password for the access via OLAT. Go to [http://www.htaccesstools.com/htpasswd-generator/]  to generate a password. Copy the result into '.htpasswd'. Also note the username and the password you chose, you will need them in step 5.2.

For example, for the username 'roselit' and the password 'test1234', you get:

	roselit:$apr1$vCpsiJwc$ABMcRlTHAKaK5bMGKzA/R.

## 5 Configure roselit

### 5.1 Edit application/config/config.php

Set the 'base_url' to the same as the URL used in step 4.1 for the RewriteRule. Don't forget the trailing slash:

	$config['base_url']	= 'https://www.example.com/demo/roselit/';

Set the 'pdf_folder' to the full filesystem path (with trailing slash) of the directory you created in step 2.

	$config['pdf_folder'] = '/usr/local/ftp/test/roselitfiles/'; 

Fill in the credentials from step 4.3 as 'api_username' and 'api_password'. The password must be in plain text here. Using the same credentials as above:

	$config['api_username'] = 'roselit';
	$config['api_password'] = 'test1234';

Additionally set a different citation style by setting the option 'citation_style':

	$config['citation_style'] = 'ROSE';

### 5.2 Edit application/config/upload.php

Set "upload_path" to the same as "pdf_folder" in step 5.1.

	$config['upload_path'] = '/usr/local/ftp/test/roselitfiles/';

### 5.3 Edit application/config/database.php

Fill in the details for the database you created in step 1.

	$db['default']['hostname'] = 'mysql.example.com';
	$db['default']['username'] = 'demo';
	$db['default']['password'] = 'test';
	$db['default']['database'] = 'demo';

### 5.4 Edit api/index.php

Set 'system_path' to the full filesystem path of the system folder of your RoSeLit installation:

	$system_path = '/usr/local/ftp/test/public_html/demo/roselit/system';

Set 'application_folder' to the full filesystem path of the application folder of your RoSeLit installation:

	$application_folder = '/usr/local/ftp/test/public_html/demo/roselit/application';

Set 'base_url' to the full url to the folder 'api':

	$assign_to_config['base_url'] = 'https://www.example.com/demo/roselit/api/';

**Thats it, RoSeLit is set up and running!** Test, if it works and login:

https://www.example.com/demo/roselit/

Now there are 2 more steps:

### 6 Edit index.php in the base directory of roselit and in the subdirectory api/

Set the 'ENVIRONMENT' to 'production' in both files:

	define('ENVIRONMENT', 'production');

## 7 Create admin account

One more thing to do, to get administrative rights, you have to log in using Shibboleth and afterwards change the account type in the database directly:

1. Connect to your database.
2. Open the table "users".
3. Search the user you would like to give administrative rights.
4. Set the field "role" to "admin".

Or you can just execute the following SQL statement, replacing [userid] with the id of the user:

	UPDATE `users` SET `role` = `admin` WHERE `id` = `[userid]`;

From now on, you can give administrative rights directly using your admin account.