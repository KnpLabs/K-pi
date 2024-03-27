PWD := $(shell pwd)
UID := $(shell id -u)

.PHONY: phpspec
phpspec:
	@docker compose -f tests/compose.yaml run --rm --build  --user=$(UID) --volume="$(PWD)/coverage/phpspec:/K-pi/coverage/phpspec" --entrypoint=php php vendor/bin/phpspec --config=phpspec.yaml.dist run --no-interaction

.PHONY: prettier
prettier:
	@docker compose -f tests/compose.yaml run --rm --build prettier

.PHONY: php-cs-fixer
php-cs-fixer:
	@docker compose -f tests/compose.yaml run --rm --build  --user=$(UID) --volume="$(PWD):/K-pi" --workdir=/K-pi composer install
	@docker compose -f tests/compose.yaml run --rm --build  --user=$(UID) --volume="$(PWD):/K-pi" --entrypoint=php php vendor/bin/php-cs-fixer fix -vvv --diff

.PHONY: autoformat
autoformat: prettier php-cs-fixer
