# Board Games Application
Board Games is the repository to manage multiples board games, like Checkers, Chess, TicTacToe, etc, using PHP and Symfony 5.4
Currently, just the Checkers rules was created but the code use Strategy pattern to be easy to extends to other board games.
All the routes, except `/ping` and `/v1/game` (POST method) must have an access-token header. The API will return status code 403 (forbidden) if no access token is provided. The access token will be automagically injected into all queries (using a Doctrine filter on a pre-Request listener) to ensure that only requests with a valid access token receive data. If an invalid token is provided, the API will throw an exception (GAME_NOT_FOUND or PIECE_NOT_FOUND) with status code 404 (not found).

---
### Require
- Docker
- Docker-compose

---
### Installation
1. Clone the repository:
```
gh repo clone rpetermann/board-games-php
```
2. Finally run your local server _(on the project root!)_:
```
./compose-command up
```
3. Access the container:
```
docker exec -it board-games-php bash
```
4. Run composer:
```
composer install
```
5. Run doctrine command to create your local database:
```
php bin/console doctrine:database:create
```
6. Run doctrine command migration:
```
php bin/console doctrine:migrations:migrate
```
7. Run doctrine command to create the tests database in your local:
```
php bin/console doctrine:database:create --env=test
```
8. Run doctrine command to update the tests database schema:
```
php bin/console doctrine:schema:update --env=test --force
```
9. Verify the code running the phpunit command _(code coverage is stored in src/Tests/\_reports/coverage/index.html)_:
```
vendor/bin/phpunit
```
10. Optionally, add the following on your hosts file:

```
echo "172.33.0.11 local-board-games.com
172.33.0.61 local-db-board-games.com
172.33.0.111 local-php-nginx-board-games.com" | sudo tee -a /etc/hosts
```
---
### To-do (improvements)
- Automatically run php-cs on pre-commit script to ensure the code quality
- Migrate to NoSQL database
- Improve/Refactor piece validation method to avoid too many if statements.
- Improve/Refactor getAllowedPieceMoves() method to remove try-catch-continue
- create a rule to "upgrade" a piece to dame when it reaches the last slot on the board