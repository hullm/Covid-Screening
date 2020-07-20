# Covid-Screening
Screening form for Covid 19 symptoms.  
![Login Screen](https://covid.lkgeorge.org/images/loginscreen.png)
Created by Matt Hull and Dane Davis, this is still in development.  We currently have the user portion working so people can check in using the system.  The administrative side needs to be built next.

# Requirements
Covid Screening was built on a server running Ubuntu 20.04 Server, Apache 2.4.41, PHP 7.4.3, and MariaDB 15.1.  In order to ensure compatibility, create a server running Ubuntu 20.04 Server with a static IP address, Internet access, and ssh access.  If you want to access it from the web you'll need to open port 80 and optionally port 443 if you choose to add a certificate.  You'll also need to create DNS entries for the server.

# Step 1 - Install Apache

Update all existing packages then install Apache.
```bash
sudo apt update; sudo apt -y upgrade
sudo apt install -y apache2
```

Ubuntu's UncomplicatedFirewall may be enabled.  If so we need allow Apache through the firewall.  You can see if it's enabled using the following command.

```bash
sudo ufw status verbose
```

If it's disabled you can continue, but if you want to enable the UncomplicatedFirewall and allow Apache use the commands below.  Make sure you also allow ssh.

```bash
echo y | sudo ufw enable
sudo ufw allow 'Apache Full'
sudo ufw allow ssh
```

Test your Apache install by opening the site in a web browser.  You should see the default page. (http://*servername*)
![Login Screen](https://covid.lkgeorge.org/images/apacheinstalled.png)

# Step 2 - Install PHP
Install PHP and the modules for Apache.
```bash
sudo apt -y install php libapache2-mod-php
````
After installing PHP you'll need to restart Apache.
```bash
sudo systemctl restart apache2
```
We'll test the PHP install by creating a php file that will output information about PHP.
```bash
sudo echo "<?php phpinfo();?>" > ~/test.php
sudo mv ~/test.php /var/www/html/test.php
```
Test your PHP install by opening the test.php page in a web browser.  You should see the PHP info page. (http://*servername*/test.php)
![Login Screen](https://covid.lkgeorge.org/images/phpinstalled.png)

After testing PHP delete the test.php file.
```bash
sudo rm /var/www/html/test.php
```

# Step 3 - Install MariaDB
MariaDB is a fork of mysql, we'll install it here.
```bash
sudo apt -y install mysql-server
```
After it's installed you'll want to set a password and secure the installation.  The following command will start a wizard that will ask you some questions to help secure the installation.
```bash
sudo mysql_secure_installation
```

Output from the command:
```terminal
Securing the MySQL server deployment.

Connecting to MySQL using a blank password.

VALIDATE PASSWORD COMPONENT can be used to test passwords
and improve security. It checks the strength of password
and allows the users to set only those passwords which are
secure enough. Would you like to setup VALIDATE PASSWORD component?

Press y|Y for Yes, any other key for No: Y

There are three levels of password validation policy:

LOW    Length >= 8
MEDIUM Length >= 8, numeric, mixed case, and special characters
STRONG Length >= 8, numeric, mixed case, special characters and dictionary 
file

Please enter 0 = LOW, 1 = MEDIUM and 2 = STRONG: 2
Please set the password for root here.

New password: <type your password here>

Re-enter new password: <re-type your password here>

Estimated strength of the password:  
Do you wish to continue with the password provided?(Press y|Y for Yes, any other key for No) : Y
By default, a MySQL installation has an anonymous user,
allowing anyone to log into MySQL without having to have
a user account created for them. This is intended only for
testing, and to make the installation go a bit smoother.
You should remove them before moving into a production
environment.

Remove anonymous users? (Press y|Y for Yes, any other key for No) : Y
Success.


Normally, root should only be allowed to connect from
'localhost'. This ensures that someone cannot guess at
the root password from the network.

Disallow root login remotely? (Press y|Y for Yes, any other key for No) : N

 ... skipping.
By default, MySQL comes with a database named 'test' that
anyone can access. This is also intended only for testing,
and should be removed before moving into a production
environment.


Remove test database and access to it? (Press y|Y for Yes, any other key for No) : Y
 - Dropping test database...
Success.

 - Removing privileges on test database...
Success.

Reloading the privilege tables will ensure that all changes
made so far will take effect immediately.

Reload privilege tables now? (Press y|Y for Yes, any other key for No) : Y 
Success.

All done!
```

We're going to run an sql command that will allow the root user to authenticate using a password.  By default it uses the auth_socket plugin which uses the linux user's credentials.  We want to switch to mysql_native_password for the root user.  Make sure to change your_password to the password you want for the root user.

```bash
sudo mysql

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
EXIT;
```

After you set the password test it by seeing if you can sign in as root with your password.
```bash
mysql -u root -p

Enter password: 
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 11
Server version: 8.0.20-0ubuntu0.20.04.1 (Ubuntu)

Copyright (c) 2000, 2020, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql>EXIT;
Bye
```

# Step 4 - Install PHP LDAP Tools
In order to authenticate using Active Directory we need to install the PHP LDAP tools.  After the tools are installed we need to restart Apache.
```bash
sudo apt -y install php-ldap
sudo systemctl restart apache2
```

# Step 5 - Verify Your Timezone
When people submit the form it will record the time they submitted.  You'll want to make sure the server's time zone is set properly so the times are correct.  First verify the time zone.
```bash
timedatectl
```

If the timezone is properly set then you're good to go.  If not you need to set the timezone.  
```bash
sudo timedatectl set-timezone America/New_York
```

If you're not in New York you can search timezones with the list-timezones option.  The command below shows all American timezones.
```bash
timedatectl list-timezones | grep America
```

# Step 6 - Install Covid Screening

We're going to install the Covid Screening site to the root of the web server.  Before we can do that we need to remove the default index.html file
```bash
sudo rm /var/www/html/index.html
```

Now we need to change to the correct directory, clone the repository and move the files to the web root.
```bash
cd /var/www/html
sudo git clone https://github.com/hullm/Covid-Screening
sudo cp -r Covid-Screening/. .
sudo rm -r Covid-Screening/
```

After the site is downloaded we're going to move the config file out of the website so we can set some settings.
```bash
sudo mv config.ini.example ../config.ini
```

Let's open the config file so we can setup the site.
```bash
sudo nano ../config.ini
```
Set the values in the config file.
* **servername**: localhost will work fine for this setting, but if you have the database on another server you can put it's address here.
* **username**: This will probably be root, but if you created another account enter the username here.
* **password**: The password for the SQL account being used.
* **dbname**: The name of the database that will be created for this site.
* **DC**: The FQDN or IP of a domain controller in your environment.
* **netbios**: The NETBIOS name of your domain.
* **rootDN**: The RootDN of your domain.
* **studentOU**: The root OU that contains your students.
* **sites**: The sites that people will check in to.
* **title**: The title of the webpage.
* **logintext**: The message tha appears on the login screen

When you're done press control+x to exit, answer y to same, and enter to accept the file name.
![Config File](https://covid.lkgeorge.org/images/config.png)

If you chose to store config.ini in a different location you need to edit config.php to tell it where the config file is located.  Open includes/config.php and set the path to the config file.
```bash
sudo nano includes/config.php
```
Set $configFile to the path of the config file.  When you're done press control+x to exit, answer y to same, and enter to accept the file name.

After the config files are setup open the site in a web browser. (http://*servername*/)
![Login Screen](https://covid.lkgeorge.org/images/loginscreen.png)

If everyting is setup properly the first time you log in it will redirect you to the setup page which will create the database.  If everything went well click View the site.

![Database Created](https://covid.lkgeorge.org/images/dbcreated.png)

At this point the site is up and running, but you can install a couple other optional things that might help.

# Optional Step - Install phpMyAdmin
You can optionally install phpMyAdmin.  It will give you web access to manage the database.
```bash
sudo apt -y install phpmyadmin
```

During the install you'll be asked to select a web server, use the space bar to select apache2 then tab to the ok button and hit enter.

![phpMyAdmin Install](https://covid.lkgeorge.org/images/phpmyadmininstall.png)

After that you'll be asked if you want to configure a database for phpmyadmin, answer yes.  After that provide a password for the database and confirm the password.

Test your phpMyAdmin install by opening it in a web browser.  You can sign in using root as the username and the password you set earlier. (http://*servername*/phpmyadmin)
![phpMyAdmin Install](https://covid.lkgeorge.org/images/phpmyadmin.png)

# Optional Step - Install Let's Encrypt Certificate
If your server has a public address, an external DNS entry and port 80 and 443 open you can use Let's Encrypt to install a free certificate.  The certificate will renew automatically using a program called certbot.  We need to install certbot and run it.
```bash
sudo apt -y install certbot python3-certbot-apache
sudo certbot --apache
```
This will start a wizard where you'll be asked a few question.  You'll be asked to enter an email address, to agree to the terms, if you want to share your email, and asked for the site's name.  After that it will verify you have ownership of the domain by placing some test files on the site.  If it can then browse to those files it knows you have ownership.  After a clean up you'll be asked if you want to redirect all requests to HTTPS.  When done you're site will be secured.

# Updating the Covid Screening Site
This is still under development.  After installing you can use git pull to update the site.
```code
cd /var/www/html
sudo git pull