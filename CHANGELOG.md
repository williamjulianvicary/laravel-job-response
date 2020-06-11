# Changelog

All notable changes to `laravel-job-response` will be documented in this file

## 0.1.1 - 0.1.2 - 2020-06-11

- Bug fixes and Travis CI setup.
- Altered functionality of Exception handling. Initially this serialized the exceptions and attached them unserialized
however this was not reliable and broke functionality with Monolog/casting exceptions to strings.

## 0.1.0 - 2020-06-11

- Initial release
