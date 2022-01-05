## Contributing

 Contributions are most welcome, and the more eyes on the code the better.

 Install phing on your development machine - it's used for tests, checking/fixing code style,
 and anything else you care to use it for!

### Check list
  * Write tests - pull requests should come with full coverage
  * Check the code style

### To get started:
  * Fork this library
  * Check out the code:
    - `git clone git@github.com:yourfork/ios-receipt-parser && cd ios-receipt-parser`
  * Start your own branch:
    - `git checkout -b your-feature-branch`
  * Check your work:
    - Run the tests: `./vendor/bin/phpunit` or `make tests`
    - Generate/update the documentation (requires [phpDocumentor](https://www.phpdoc.org/)): `phpDocumentor -d ./src -t ./docs` or `make docs`
    - Generate/update the code coverage (requires [php-xdebug](https://xdebug.org/)): `./vendor/bin/phpunit --coverage-html coverage` or `make coverage`
  * Commit your work: `git commit ... `
  * Push your work:
    - `git push origin your-feature-branch`
  * And open a pull request!

 Please GPG sign your commits if possible: `git commit -S ...`
