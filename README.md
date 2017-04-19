# php-framework
Django inspired framework in php

# Get Started
copy the configtemplate.php in the settings folder and rename it to config.php.
copy the templateapp and rename whatever you want. finaly you need to add a item to the array in settings/urls.php to redirect to the just maded app.
and you're done with setting everything up

# How does it work
You make apps and you connect it together with the urls.php. You can add your function in the settings/urls.php but when you add a urls.php in your app and add the path to the urls.php in the settings folder
then i search further in that url file to get the functions to load the page.
Every app can have a static folder and a template folder for his stylesheet and his views
you can load your static files with this command:```LoadStatic()```. then can you search your file like this ``` GetStaticFile('subfolder','filename')``` . If you want to load templates then you need to use this command: 
``` LoadTemplates()```. Then you can load your file like this: ```GetTemplate(sub folder,filename)``` it also can used as this: ```GetTemplate('filename')``` if you dont use subfolders

# admin page
You can go to the admin page by typing this in your webbrowser http://yourwebsite.com/admin. There can you add users and edit users

# Functions

## load templates
```php
<?php
// load templates
LoadTemplates()
// use template
GetTemplate(sub folder,filename)
// or just the filename if you doesn't use subfolders
GetTemplate('filename') ?>
```
## load static files
```php
// load templates
<?php LoadStatic();
// use template
GetStaticFile('subfolder','filename'); ?>
```
## Database
```php
<?php
$db = new Model();
// prepare sql query
$db->prepare("SELECT * FROM Users");
// bind parameters to query
$db->bind(":id",$id);
// execute query
$db->execute();
// fetch one row
$db->fetch();
// get all rows
$result = $db->GetAll();
// debug query
$db->debug();
?>
```
## User function
```php
<?php
// creates a user
// array keyname is column name value is value of column
User::createUser($array);
// Login function and creates a session when is successfull
User::Login($user,$pass);
// checks if the user has the specific permission
User::RoleExist($username,$permissionname);
?>
```
 
