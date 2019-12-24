# code-ruu

Working examples of various technologies.

## Setup

### Docker

* [Why use docker?](Why-use-docker.md)
* [Install](https://github.com/janis-rullis/dev/blob/master/Docker/README.md#install)

### `.env`

- Copy the `.env.example` to `.env`.
- Open `.env` and fill values in `FILL_THIS`.

### `setup.sh`

```shell
chmod a+x setup.sh
./setup.sh
```

### Add these to Your `hosts` file

```
172.60.2.10     ruu.local
172.60.2.11     api.ruu.local
172.60.2.14     pma.ruu.local
```

### Open

* [ruu.local](http://ruu.local)
* [api.ruu.local](http://api.ruu.local)
* [PhpMyAdmin pma.ruu.local](http://pma.ruu.local)

## FAQ

### Why wasn't used a specifc framework method?

* [Before starting a project / feature avoid conflicts in the team by discussing and agreeing on standards.](Project-and-company-wide-practices.md)

#### Easier to migrate the project

By using less framework specific methods and more language-wide features makes a migration to other language, framework or version, less complex.

### Why migrations are made using raw SQLs and not framework tools?

> like schemor builders, seeders, ORM, or self-defined logic inside a Model.

* [Before starting a project / feature avoid conflicts in the team by discussing and agreeing on standards.](Project-and-company-wide-practices.md)

#### Guaranteed result - What You See Is What Will Be Imported

Less space for side-effects.

#### Easier to migrate the project

if the project is moved to other language, framework or version.

### What's wrong with using ORM or self defined PHP methods?

The choise to use RAW queries comes from a harsh experience.

#### Refactoring

Once worked in a project where migrations were written  in the regular way - using ORM and methods defined in database models.
Problems arise after a long code refactoring. **Migration didn't work because the logic in the code has changed!**

#### Migration to a different language

The migrations needs to be made incrementaly - step by step.
It is easier to copy RAW queries in specific steps, than trying to convert the code to the specific SQL.

In raw queries it will happen as defined and the refactor migrations will incrementaly updated databases.

### Why table names are called in a singular and not plurar manner?

> Up to you. Just be consistent though.

* [Before starting a project / feature avoid conflicts in the team by discussing and agreeing on standards.](Project-and-company-wide-practices.md)

#### References

* [Table Naming Dilemma: Singular vs. Plural Names [closed] (stackoverflow.com](https://stackoverflow.com/a/5841297)
* [Plural vs Singular Table Name (dba.stackexchange.com)](https://dba.stackexchange.com/a/13737)
* [The table naming dilemma: singular vs. plural (medium.com/@fbnlsr)](https://medium.com/@fbnlsr/the-table-naming-dilemma-singular-vs-plural-dc260d90aaff#1231)
* [Use singular nouns for database table names (teamten.com)](https://www.teamten.com/lawrence/programming/use-singular-nouns-for-database-table-names.html)
* [dzhim-cms/fmw/core/Autoload.php](https://github.com/janis-rullis/dzhim-cms/blob/665e359748f4b0f64412bf3f88fa43aa0503301c/fmw/core/Autoload.php#L7)

#### Personal reason

- This practice comes from a practice when an object files, classes, tables needs to be generated from a variable. It is easier to handle a singular word than plural. Example, man -> mans (incorrect but works), men on the other hand is harder to convert to singular without adding a new value.
- Mostly table names are shorter. Good when have a lot of relation tables.

### Why in some Model methods there is used a raw query?

#### Use case #1 `Product::updatePrice(int $productId)` - faster and more reliable

```php
public static function updatePrice(int $productId)
	{
		return \DB::statement("
			UPDATE `product`
			SET `price` = (SELECT SUM(price) FROM ingredient WHERE `product_id` = ? AND deleted_at IS NULL)
			WHERE `id` = ?
        ", [$productId, $productId]);
	}
```

This query's execution is way faster and stable when called in SQL rather than in PHP (collect items, calculate and set).

#### Use case #2 `updateSeq(int $productId)` - faster and more reliable

```php
	public static function updateSeq(int $productId)
	{
		return \DB::statement("
			UPDATE `ingredient`
			SET `seq` = (@i := @i+1)
			WHERE `product_id` = ?
			AND `deleted_at` IS NULL
			ORDER BY `seq` ASC
			(SELECT @i := 1)
        ", [$productId]);
	}
```

This query's execution is way faster and stable when called in SQL rather than in PHP (collect items, calculate and set).

#### Use case #2 - not in this project - limitations, control and easier to maintain

Sometimes the abstraction just can't exactly implement the desired outcome which could lead to silly workarounds and make the code less maintainable.