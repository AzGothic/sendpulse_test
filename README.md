# SendPulse / Test

Simple TODO Application
* registration/login
* tasks + sub tasks create/edit/close/delete/list

## Technologies

* Docker + Docker-Compose
* PHP 7.3
* MySQL 5.7
* Composer
* PHPUnit 9

## Install

* clone git repository
* run docker-compose build\
` $ docker-compose up --build [-d] `
* run db migration\
` $ docker-compose exec php php app migrate `

## Usage

* project will be on host:port = http://localhost:8000
* phpmyadmin will be on host:port = http://localhost:8001
* for run tests use command\
` $ docker-compose exec php sh test `

### Details about task and my comments [RU]

* can be found by link https://github.com/AzGothic/sendpulse_test/blob/master/COMMENTS.md