# [SEACMS](https://seacms.com) - Simple Ecological and Accessible CMS

[![jQuery](https://img.shields.io/badge/licence-%20WTFPL-green)](http://www.wtfpl.net)
[![jQuery](https://img.shields.io/badge/PHP-7.2-lightgrey.svg?colorB=8892bf)](http://php.net/)
[![jQuery](https://img.shields.io/badge/MariaDB-10.4-lightgrey.svg?colorB=f29111)](https://mariadb.org/)
[![Knacss](https://img.shields.io/badge/knacss-8.2%20reborn-blue)](https://github.com/alsacreations/KNACSS/tree/master/css/knacss-full)
[![jQuery](https://img.shields.io/badge/jQuery-3.3.1-blue.svg?colorB=78cff5)](https://jquery.com/)
[![jQuery](https://img.shields.io/badge/jQuery_UI-1.12.1-yellow.svg?colorB=faa523)](http://jqueryui.com/)
[![jQuery](https://img.shields.io/badge/icons-IcoMoon%20App-44ac9b)](https://icomoon.io/)

SEACMS is a CMS (Simple Ecological and Accessible) that allows to create and generate eco-responsible websites.

- **As simple as a snap of the fingers** : Content management system is easy to install, upgrade and use and without any databases to configure.
- **Flat-file content management system** : All data is stored in txt files: this avoids sending queries and makes it easier to save or export data.
- **Editing simplified from your page** : The administration interface has been removed to simplify the user experience, editing content is done directly online.
- **Lightweight for low environmental impact** : All resources are optimized to keep only the essential: codes minified, images compressed and server requests reduced.
- **Multi-user management** : Possibility to assign different profiles to website users: administrators, editors or readers.
- **Accessibility of content** : The CMS leverages the [Knacss Reborn](https://www.knacss.com/) framework to facilitate some accessibility best practices (e.g. [WAI](https://w3.org/WAI/fundamentals/) by [W3C](https://w3.orgw3c/)).
- **Multi-Language Support** : The CMS is translated into several languages and allows the development of a multi-language site.
- **Completely open-source** : SEACMS is an open-source project licensed under the WTFPL for unrestricted redistribution and modification.


## Installation
- Unzip the site files on your FTP and with your browser go to the address where `index.php` is located (Check that you have the hidden files)
- Copy and paste the theme 'default' folder and rename it
- Select your site template (or leave the default one) and enter the email and password that will be used to administer the site.


After that, you can edit the generated `config.php` file. It contains the information necessary for the functioning of the CMS:
- change the value of `$GLOBALS['theme']` to the name of your theme
- change the values of `$GLOBALS['domain']` to the names of your local and online sites
- uncomment the values of `$GLOBALS['add_content']` that you will need (product, article, event, video, media, page). They correspond to the types of content you will publish. You can add other custom ones. Be careful, you must also activate them by selecting them in the concerned administration profile(s) in edit mode (will be visible after reconnection).
- uncomment the values of `$GLOBALS['toolbox']` that you will need (titles, formatting...). They will appear in a toolbar when editing a content.

### PHP configuration requirements
In PHP configuration short_open_tag must be set to On `short_open_tag = On`. The following PHP extensions must be installed: php-mbstring, php-mysql, php-curl and php-gd.

### Specific configurations
#### Colors on texts
In the `config.php` file, you can activate the colors in the editing tools (variable `$GLOBALS['toolbox']`), then you have to tell how many classes you have in your `style.css` in the variable `$GLOBALS['Nbcolor'] = 2;`
For 2 classes like in the example you need in your `style.css` file :
~~~~
.color-1 { color: #38547a; /*blue*/}
.color-2 { color: #49d7ac; /*green*/}
~~~~
