laser-gcode-app
===============

Lansing Makers Network
----------------------

A simple webapp to help members generate Gcode for Lansing Makers Network's
buildlog.net laser cutter.

[http://www.lansingmakersnetwork.org/]


Installation
------------

1.  Install Dependancies
	a. pstoedit
		1. Clone/download timschmidt's fork of the pstoedit repo: https://github.com/timschmidt/pstoedit-lmn-laser
		2. Inside the downloaded/cloned directory run: ./configure && make && sudo make install && sudo ldconfig
	b. Imagick
		1. Run: `sudo apt-get install php5-imagick`
	c. Redis (while not required, redis is strongly recommended for production installed
		1. Run: `sudo apt-get install redis-server`
	d. PhpRedis (same as above, not required, but recommended)
		1. Follow the instructions at: https://github.com/nicolasff/phpredis#installation

2.  Clone the laser-gcode-app repo: `git clone --recursive git@github.com:wreality/laser-gcode-app.git`
3.  Make the cake console executable: `chown 770 app/Console/cake`
4.  If using Redis:
	a. Run: `cd app/Plugin/CakeResque`
	b. Run: `curl -s  https://getcomposer.org/installer | php`
	c. Run: `php composer.phar install`
	d. Head back up to the project root: `cd ../../../`
	d. Copy the default redis-config and edit to your liking: `cp app/Config/resque-config.php.default app/Config/resque-config.php`
5.  Configure Application Defaults
	a. Copy default config: `cp app/Config/config.php.default app/Config/config.php`
	b. Edit to your liking: `nano app/Config/config.php`
	c. *Pay special attention to the baseUrl parameter!*
5.  Configure Database:
	a. Copy default database file: `cp app/Config/database.php.default app/Config/database.php`
	b. Edit with your database details: `nano app/Config/database.php`
	c. Import database tables: `app/Console/cake schema create --name laser`
6.  Point your webserver to serve up `app/webroot`!
