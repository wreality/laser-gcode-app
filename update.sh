#!/bin/bash
############################################
## LaserApp 
##
## Update Script
##
##
##
############################################

BLUE='\E[1;34m'                      
YELLOW='\E[1;33m'                    
RED='\E[1;31m'                       
GREEN='\E[1;32m'                     
RESET='\E[0m'                        
ORIG_IFS=${IFS}                      


check() {
    if [ $? -ne 0 ]; then
	echo -e $RED"[ERROR]"$RESET
	if [ -n "${1}" ]; then
	    echo -e $RED"${1}"$RESET
	fi
	exit 1
    else
	echo "ok."
    fi
}

echo -n "Checking for app directory..."
if [ -e app/Config/database.php ]; then
	echo "ok."
else
	echo "[ERROR]"
	exit 1
fi

echo -n "Stopping resque workers...."
app/Console/cake CakeResque.CakeResque stop --all > /dev/null
check

if [ -x pre-stash.sh ]; then
    echo -n "Executing Pre-Stash Hook..."
    source pre-stash.sh
    check 
fi

echo -n "Stashing local changes..."
git stash > /dev/null
check

echo -n "Pulling update..."
git pull > /dev/null
check

echo -n "Unstashing changes..."
git stash pop > /dev/null
check

if [ -x post-stash.sh ]; then
    echo -n "Executing post-stash hook..."
    source post-stash.sh
    check
fi

echo -n "Updating database [1/2]..."
app/Console/cake schema create --name laser > /dev/null &2>1 <<END
n
y
END
check

echo -n "Updating database [2/2]..."
app/Console/cake schema update --name laser > /dev/null <<END
y
END
check

echo -n "Running data upgrades..."
app/Console/cake update run > /dev/null
check

echo -n "Starting workers..."
app/Console/cake CakeResque.CakeResque load > /dev/null
check

