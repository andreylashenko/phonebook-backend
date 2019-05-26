
docker-build:
	docker-compose up --build -d

docker-up:
	docker-compose up -d

docker-dump:
	docker exec -i mysql-container mysql -u root --password=root -e \
	"ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; \
	 ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';"
	docker exec -i mysql-container mysql -uroot -proot phonebook < phonebook.sql

