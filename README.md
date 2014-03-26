laser-gcode-app
===============
[![Build Status](https://travis-ci.org/wreality/laser-gcode-app.svg?branch=develop)](https://travis-ci.org/wreality/laser-gcode-app)

Lansing Makers Network
----------------------

A simple webapp to help members generate Gcode for Lansing Makers Network's
buildlog.net laser cutter.

http://www.lansingmakersnetwork.org/


Installation
------------

1.  Install Dependencies
	* pstoedit
		1. Clone/download timschmidt's fork of the pstoedit repo: https://github.com/timschmidt/pstoedit-lmn-laser
		2. Inside the downloaded/cloned directory run: ./configure && make && sudo make install && sudo ldconfig
	* Imagick
		1. Run: `sudo apt-get install php5-imagick`
	* Redis (while not required, redis is strongly recommended for production installed
		1. Run: `sudo apt-get install redis-server`
	* PhpRedis (same as above, not required, but recommended)
		1. Follow the instructions at: https://github.com/nicolasff/phpredis#installation
2.  Clone the laser-gcode-app repo: `git clone --recursive git@github.com:wreality/laser-gcode-app.git`
3.  Make the cake console executable: `chown 770 app/Console/cake`
4.  If using Redis:
	1. Run: `cd app/Plugin/CakeResque`
	2. Run: `curl -s  https://getcomposer.org/installer | php`
	3. Run: `php composer.phar install`
	4. Head back up to the project root: `cd ../../../`
	5. Copy the default redis-config and edit to your liking: `cp app/Config/resque-config.php.default app/Config/resque-config.php`
5.  Configure Application Defaults
	1. Copy default config: `cp app/Config/config.php.default app/Config/config.php`
	2. Edit to your liking: `nano app/Config/config.php`
	3. *Pay special attention to the baseUrl parameter!*
5.  Configure Database:
	1. Copy default database file: `cp app/Config/database.php.default app/Config/database.php`
	2. Edit with your database details: `nano app/Config/database.php`
	3. Import database tables: `app/Console/cake schema create --name laser`
6.  Point your webserver to serve up `app/webroot`!
