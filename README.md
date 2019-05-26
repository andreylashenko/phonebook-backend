# Телефонный справочник

Тестовое задание компании ProfitClick. Backend часть.

Перед разверткой проекта убедитесь, что у вас установлен docker

docker: https://docs.docker.com/install/linux/docker-ce/ubuntu/

docker compose: https://docs.docker.com/compose/install/

Перед разверткой проекта остновите следующие сервисы

``` bash
# остновка nginx
> sudo service nginx stop
# остановка mysql
> sudo service mysql stop

Развертка проекта: в корне проекта выполнить следующие команды

``` bash
# сборка проекта
> make docker-build
# стартуем контейнеры
> make docker-up
# загрузка дампа базы данных
> make docker-dump
```
