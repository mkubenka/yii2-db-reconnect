PLATFORM?=Docker

ifeq ($(PLATFORM), Docker)
	DOCKER_PHP=docker-compose run --rm php
else ifeq ($(PLATFORM), CI)
	DOCKER_PHP=docker-compose run php
else
	DOCKER_PHP=
endif

test:
	$(DOCKER_PHP) ./vendor/bin/phpunit $(TEST)
