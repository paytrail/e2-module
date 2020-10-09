# Changelog

All notable changes to this project will be documented in this file.

## `1.2.0`

### Feature
Add getPaymentData() method to allow custom forms (@tuutti #22)

## `1.1.2`

### Fixed
* Calculate return authcode only from expected parameters (@Jipsukka, #19)

## `1.1.1`

### Fixed
* Allow item titles to have double quotes (@Jipsukka, #17)

## `1.1.0`

### Improvements
* Allow payment URL to be changed for internal testing (@Jipsukka, #15)

### Fixed

* Use correct payment data keys for messages (@Jipsukka, #15)

## `1.0.5`

### Fixed

* Fix template path case (@Astyk #14)

## `1.0.4`

### Fixed

* Rename app folder accordingly to to match PSR-4 standard (@Jipsukka, #9)

### Dependencies

* Update PHPUnit (#7)

## `1.0.3`

### Fixed

* Use `POST` method for sending forms (@Jipsukka, #6)

## `1.0.2`

### Improvements

* Don't override return URLs with default ones (@Jipsukka, #5)
* Bump phpunit/phpunit from 8.5.3 to 8.5.4 (#4)

## `1.0.1`

This is a general maintenance release containing enhancements to library development.

### Improvements

* Add parallel Github Actions builds with different PHP versions. (@nikoheikkila, #2)

### Documentation

* Add GitHub recommended Markdown files (@nikoheikkila, #3)

## `1.0.0`

### New

* Finished E2 payment interface implementation. (@Jipsukka, #1)
