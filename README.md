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
you can load your static files with this command:```LoadStatic()```. then can you search your file like this ``` GetStaticFile('subfolder','filename')``` 
it can also be used as  ```GetStaticFile('filename')``` if you dont use sub folders in your static folder. If you want to load templates then you need to use this command: 
``` LoadTemplates()```. Then you can load your file like this: ```GetTemplate(sub folder,filename)``` it also can used as this: ```GetTemplate('filename')``` if you dont use subfolders

