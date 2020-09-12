# Readme

**Light MVC** is a project template/framework to help you quickly start building your MVC based application/website using php.

## Installation!
  - **Git:**
    ```
    > git clone https://github.com/smfcoder17/light-mvc.git
    ```
  - **You can also:**
    Download and save files from GitHub.

## Configuration
  - **Development**
    - Configure a virtualhost to start in the **_<path>/www/<project-name>/public_** folder
  - **Production**
    - Copy your project to the server in the **_<path>/<project-name>/_** folder
    - Rename the folder **_public/_** to **_public_html/_** since it's the default name for it on the server.

## Getting Started
  After installing/clonning **light-mvc**, these are some things you should know or do before you begin to code:
  - **Installing dependencies:**
    You have to run the command: `composer install` to install all dependencies. Read more about composer [here](https://getcomposer.org/)

  - **Project architecture:**
    - You shoudn't modify files in the `Core/` folder unless you are completly sure of what you're doing.
    - The file `App/routes.php` contains by default your application routes.
    - The `App\App.php` class entirely initialize, configure and dispatch your application. Don't hesitate to change it as you please.
    - you can create a `.env` file at the **root**(same level as App, Core, etc...) folder to store sensitives informations like *database infos, api keys, credentials, etc...*

## Security/Issues
  If you discover a security vulnerability or any issue within this framework, please send an email to SmfCoder at contact@smfcoder.com.
