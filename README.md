# Example Application

Contributors: Lisa Bernhardt  
Requires at least: CodeIgniter 3.1.5  
Tested up to: CodeIgniter 3.1.5  
Version: 1.0  
License: GNU General Public License v2 or later  
License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

---

## Description

This is a example application that I am building in order to learn CodeIgniter. This project will not include the CodeIgniter standard files and CodeIgniter application directory areas that are not touched by this project.

## Features

Includes the following features:

* User Registration
* User Authentication
* User Sessions
* User Profile page where a user can change their information (when logged in)
* User Settings page where a user can change their settings (when logged in)
* A specific start page and navigation when a user is not logged into the application
* A specific start page and navigation when a user logs into the application

## Future Features on Roadmap

* User Ability to change their password while logged in
* User Admin functionality where users can be soft deleted and restored. Admin user's action is logged.
* Add Email Verification to User Registration
* Internal scripts that clean up database entries for password reset tokens
* Internal scripts that clean up database entries for soft deleted users
* Add functionality for more robust translations for general page/email content (e.g. home pages, forgot password email)

## Installation

1. Install Codeigniter on your server
2. Place code files from this project within the Codeigniter's location on your server.
3. Create the database, the database user, and execute the initialization script
(e.g. assets/queries/create_user_tables.sql)
4. Modify the application/config/database.php file to utilize the database and database user created in step 2.
5. Modify the application/config/config.php file to utilize an email server. This is used for actions such as
'Forgot Password'.  
5a. Until an email server is configured, the system dumps important information on the screen.
Search for "TODO" comments and remove the relevant var_dump call(s).  
5b. More information on configuring Codeigniter's Email.
Source: https://www.codeigniter.com/user_guide/libraries/email.html#sending-email
6. Modify the application/config/config.php file in the following sections:
6a. $config['base_url'] - The base URL for the application  
6b. $config['application_name'] - The name of the application  
6c. $config['default_email'] - The default email address used when sending out emails.

## Important Files

The following files are included in this project. If they are not available in the repository, please let us know as the repository would need to be corrected.

```
.
+-- application
|   +-- config
|   |   +-- autoload.php                The CodeIgniter autoload file.
|   |   +-- config.php                  The CodeIgniter configuration file.
|   +-- controllers
|   |   +-- User.php                    The User Controller - builds the home, login, and registration pages.
|   |   +-- Settings.php                The Settings Controller - builds the profile/setting pages.
|   +-- core
|   |   +-- MY_Model.php                The Parent Model - contains general model/database functions
|   |   +-- MY_Controller.php           The Parent Controller - contains general controller/view functions
|   +-- language
|   |   +-- english
|   |   |   +--  en-US_lang.php         The language file for English.
|   +-- libraries
|   |   +-- Template.php                The Template class - builds application view (e.g. header, content, footer).
|   +-- models
|   |   +-- User_Auth_model.php         The User Authentication Model for the user_auth table
|   |   +-- User_Meta_model.php         The User Meta Model for the user_meta table
|   |   +-- User_model.php              The User Model that utilizes the User_Auth, User_Meta, User_Reset_Tokens,
                                        and User_Settings models.
|   |   +-- User_Reset_Tokens_model.php The User Reset Tokens Model for the user_reset_tokens table
|   |   +-- User_Settings_model.php     The User Settings Model for the user_settings table
|   +-- views
|   |   +-- alerts                      Contains any partial HTMLs that are specific to alerts.
|   |   +-- app                         Contains any content HTML for pages where a user must be logged in.
|   |   +-- forms                       Contains any content HTML for forms.
|   |   +-- layouts                     Contains any HTML for pages that are used by the Template class.
|   |   |   +-- default.php             The default application HTML template
|   |   +-- nav                         Contains any partial HTMLs that are specific to the navigation bar.
|   |   |   +-- default.php             The navigation when users are not logged in
|   |   |   +-- user.php                The navigation where a user must be logged in.
|   |   +-- forgot.php                  The forgot password page
|   |   +-- home.php                    The main content page when users are not logged in
|   |   +-- login.php                   The login page
|   |   +-- register.php                The registration page
+-- assets
|   +-- queries
|   |   +-- create_user_tables.sql      The initialization of the database tables.
|   +-- third_party
|   |   +-- bootstrap                   Contains the Bootstrap files
|   |   +-- jquery                      Contains the JQuery files
```

## Copyright

Honeylizard CodeIgniter Example, Copyright 2017 Honeylizard  
Honeylizard CodeIgniter Example is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

## Third Party Resources

Honeylizard CodeIgniter Example Application bundles the following third-party resources:

Bootstrap  
Licenses: Creative Commons Attribution 3.0 Unported (CC BY 3.0)  
Source: http://getbootstrap.com/

Glyphicons, Jan Kovarik  
Licenses: Creative Commons Attribution 3.0 Unported (CC BY 3.0)  
Source: http://glyphicons.com/

## Changelog

### 1.0

* Initial release
* Released: July 12, 2017


