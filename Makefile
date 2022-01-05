docs:
	phpDocumentor -d ./src -t ./docs

tests:
	vendor/bin/phpunit

coverage:
	./vendor/bin/phpunit --coverage-html coverage

.PHONY: docs tests coverage
