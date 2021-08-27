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

function stopDocker(){
	echo "Stop any running container from this project";
	docker-compose down

# TODO: Wrap this into a testing condition. 
#	echo "Remove any dangling part."
#	echo y | docker network prune
#	echo y | docker image prune
#	echo y | docker volume prune
}

function initDb(){
	docker-compose up -d ruu-mysql
}

function readEnvVariables(){
	echo "Reading .env variables...";
	FILE=`cat .env`
	DB_USER=`echo $FILE | grep MYSQL_USER= | cut -d '=' -f2`;
	DB_PW=`echo $FILE | grep MYSQL_PASSWORD= | cut -d '=' -f2`;
}

function setLaravelEnv(){
	echo "Setting up the 'ruu-laravel8' container."
	echo "Go into 'laravel8' direcotry...";
	cd laravel8
	echo "Copying '.env.example' to '.env'...";
	cp .env.example .env

	echo "Fill variables collected from the master '.env'...";

	sed -i -e "s/DB_PASSWORD=FILL_THIS/DB_PASSWORD=$DB_PW/g" .env  
	sed -i -e "s/TESTS_DB_PASSWORD=FILL_THIS/DB_PASSWORD=$DB_PW/g" .env  

	docker-compose build --no-cache ruu-laravel8

	echo "Generating the 'APP_KEY'...";
	docker-compose run --no-deps ruu-laravel8 bash -c "php artisan key:generate"

  echo "Setting up the '.env.testing'...";
  cp .env .env.testing
  sed -i -e "s/DB_DATABASE=ruu/DB_DATABASE=ruu_testing/g" .env.testing
  sed -i -e "s/TESTS_DB_DATABASE=ruu_testing_testing/TESTS_DB_DATABASE=ruu_testing/g" .env.testing
  sed -i -e "s/DB_CONNECTION=mysql/DB_CONNECTION=testing/g" .env.testing
  sed -i -e "s/APP_ENV=local/APP_ENV=testing/g" .env.testing

	docker-compose down

	cd $DIR;
	echo "'.env' is ready.";
}

init
initDb
stopDocker
readEnvVariables
setLaravelEnv
echo "Setup is completed."
echo "Starting the project.."
echo "If this is the first time then it will download and setup Docker containers."
chmod a+x start.sh
./start.sh
