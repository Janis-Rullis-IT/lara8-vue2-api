# ruu-laravel5

## Launch PhpUnit tests

```shell
docker exec -it ruu-laravel5 bash
vendor/bin/phpunit tests/
```

## FAQ

### Why wasn't used a specifc framework method?

#### Easier to migrate the project

By using less framework specific methods and more language-wide features makes a migration to other language, framework or version, less complex.