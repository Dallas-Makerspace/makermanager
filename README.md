# Maker Manager [![Build Status](https://travis-ci.org/Dallas-Makerspace/makermanager.svg?branch=master)](https://travis-ci.org/Dallas-Makerspace/makermanager) [![License](https://img.shields.io/github/license/Dallas-Makerspace/makermanager.svg?style=flat-square)](https://github.com/Dallas-Makerspace/makermanager/blob/master/LICENCE) [![Coverage Status](https://coveralls.io/repos/github/Dallas-Makerspace/makermanager/badge.svg?branch=master)](https://coveralls.io/github/Dallas-Makerspace/makermanager?branch=master)
[![Release](https://img.shields.io/github/tag/Dallas-Makerspace/makermanager.svg?style=flat-square)](https://github.com/Dallas-Makerspace/makermanager/tags)

Find a copy of the latest build at [Docker Hub](https://hub.docker.com/r/dallasmakerspace/makermanager/).

## Installation

1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run
```bash
composer create-project --prefer-dist cakephp/app [app_name]
```

You should now be able to visit the path to where you installed the app and see
the setup traffic lights.

## Configuration

Read and edit `config/app.php` and setup the 'Datasources' and any other
configuration relevant for your application.
