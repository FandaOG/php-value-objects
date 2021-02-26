# php-value-objects

PHP Value object for storing valid data.

# Examples

- check `tests` dir

# TODO

- [x] add tests
- [x] ignored attributes
- [x] touched attributes
- [x] interface
- [ ] Auto-generated OpenAPI
- [ ] Auto-generated TypeScripts objects
- [ ] yaml definition of ValueObjects
- [ ] parent (possibility to get parent object)

# Changelog

- 2021-02-26
  - ignored attributes
  - touch (`setTouched`, `isTouched`, `getTouchedAll`)
  - add interface `ValueObjectInterface`
  - remove trait
  - min request PHP 7.4