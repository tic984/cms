README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/Volumes/Dati/Siti/cms/public"
   ServerName cms.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "/Volumes/Dati/Siti/cms/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

1) create project %projectname%
2) cd %projectname%/library/
3) cp -r /Volumes/Dati/Siti/ZendFramework/library/ .
4) Create virtual host with mamp: Actual dir MUST BE "public" dir, not the main dir! :)
5) Mamp: SetEnv APPLICATION_ENV "development" under "Customized Virtual Host general Setting"
6) Mamp: Directory Index: index.php
7) add in application.ini:
    autoloaderNamespaces[] = "WeDo_"
    autoloaderNamespaces[] = "Project_"
    autoloaderNamespaces[] = "Shanty_"

    resources.view[] = ""
    resources.view.helperPath.Application_View_Helper = APPLICATION_PATH "/views/helpers"
    resources.modules[] = ""
8) run zf enable layout
9) add "/application/views/helpers/LoggedUser.php"
10) copy:
    public/css
    public/img
    public/js/admin
11) add etc dir along with app, reldefs, typedefs xmls
12) add navigation.xml under configs