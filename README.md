# Covid-Screening
Screening form for Covid 19 symtoms.
![Login Screen](https://covid.lkgeorge.org/images/loginscreen.png)

# Requirements
Covid Screening was built on a server running Ubuntu 20.04 Server, Apache 2.4.41, PHP 7.4.3, and MariaDB 15.1.  In order to ensure compatability, create a server running Ubuntu 20.04 Server with a static IP address, Internet access, and ssh access.  If you want to access it from the web you'll need to open port 80 and optionally port 443 if you choose to add a certificate.  You'll also need to create DNS entries for the server.

# Step 1 - Install Apache

Update all existing packages then install Apache.
```bash
sudo apt update; sudo apt -y upgrade
sudo apt install -y apache2
```

Ubuntu's UncomplicatedFirewall may be enabled.  If so we need allow Apache through the firewall.  You can see if it's is enabled using the following command.

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

We're going to run an sql command that will allow the root user to authenticate using a password.  By defualt it uses the auth_socket plugin which uses the linux user's credentials.  We want to switch to mysql_native_password for the root user.  Make sure to change your_password to the password you want for the root user.

```bash
sudo mysql

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
EXIT;
```