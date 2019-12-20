#!/bin/bash
# https://github.com/janis-rullis/shell-scripts

function init(){
	echo "Define error reporting level, file seperator, and init direcotry.";
	set -e	; # set -o xtrace;
	# https://unix.stackexchange.com/a/164548 You can preserve newlines in the .env.
	IFS=$''
	DIR=$PWD;
	ROOT_DIR="$(dirname "${DIR}")";
}

function readEnvVariables(){
	echo "Reading .env variables...";
	FILE=`cat .env`
	DB_USER=`echo $FILE | grep MYSQL_USER= | cut -d '=' -f2`;
	DB_PW=`echo $FILE | grep MYSQL_PASSWORD= | cut -d '=' -f2`;
}

function initDb(){
	echo "Wake-up the 'ruu-mysql' container in the background...";
	docker-compose up ruu-mysql  > /dev/null 2>&1 &

	echo "Let the database container set-up...Grab a coffee. This will take 60s.";
	sleep 60s

	echo "Connect and create the DB 'ruu'...";
	docker exec -i ruu-mysql mysql -u${DB_USER} -p${DB_PW}  <<< "CREATE DATABASE IF NOT EXISTS ruu"
  docker exec -i ruu-mysql mysql -u${DB_USER} -p${DB_PW}  <<< "CREATE DATABASE IF NOT EXISTS ruu_testing"

	docker-compose down
	echo "DB 'ruu' is created and the container is shut-down...";
}

function setLaravelEnv(){
	echo "Setting up the 'ruu-laravel5' container."
	echo "Go into 'laravel5' direcotry...";
	cd laravel5
	echo "Copying '.env.example' to '.env'...";
	cp .env.example .env

	echo "Fill variables collected from the master '.env'...";
	sed -i -e "s/DB_USERNAME=FILL_THIS/DB_USERNAME=$DB_USER/g" .env
  sed -i -e "s/TESTS_DB_USERNAME=FILL_THIS/DB_USERNAME=$DB_USER/g" .env

	sed -i -e "s/DB_PASSWORD=FILL_THIS/DB_PASSWORD=$DB_PW/g" .env  
	sed -i -e "s/TESTS_DB_PASSWORD=FILL_THIS/DB_PASSWORD=$DB_PW/g" .env  

	docker-compose build --no-cache ruu-laravel5

	echo "Configuring...";	
	docker-compose up ruu-laravel5  > /dev/null 2>&1 &
	sleep 40s

	echo "Generating the 'APP_KEY'...";
	docker exec -i ruu-laravel5 bash -c "php artisan key:generate"

  echo "Setting up the '.env.testing'...";
  cp .env .env.testing
  sed -i -e "s/DB_DATABASE=ruu/DB_DATABASE=ruu_testing/g" .env.testing
  sed -i -e "s/DB_CONNECTION=mysql/DB_CONNECTION=testing/g" .env.testing
  sed -i -e "s/APP_ENV=local/APP_ENV=testing/g" .env.testing

	docker-compose down

	cd $DIR;
	echo "'.env' is ready.";
}

init
readEnvVariables
initDb
setLaravelEnv
echo "Setup is completed."
echo "Starting the project.."
echo "If this is the first time then it will download and setup Docker containers."
chmod a+x start.sh
./start.sh
