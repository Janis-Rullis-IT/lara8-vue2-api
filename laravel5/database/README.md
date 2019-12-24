# Database strucuture, migrations

## Why migrations are made using RAW SQLs and not used ORM?

### Guaranteed result - What You See Is What Will Be Imported

Less space for side-effects.

### Easier to migrate the project

if the project is moved to other language, framework or version.

### What's wrong with using ORM or self defined PHP methods?

The choise to use RAW queries comes from a harsh experience.

#### Refactoring

Once worked in a project where migrations were written  in the regular way -using ORM and methods defined in database models.
Problems arise after a long code refactoring. **Migration didn't work because the logic in the code has changed!**

#### Migration to a different language

The migrations needs to be made incrementaly - step by step.
It is easier to copy RAW queries in specific steps, than trying to convert the code to the specific SQL.

In raw queries it will happen as defined and the refactor migrations will incrementaly updated databases.

## Why table names are called in a singular and not plurar manner?

> Up to you. Just be consistent though.

* [Table Naming Dilemma: Singular vs. Plural Names [closed] (stackoverflow.com](https://stackoverflow.com/a/5841297)
* [Plural vs Singular Table Name (dba.stackexchange.com)](https://dba.stackexchange.com/a/13737)
* [The table naming dilemma: singular vs. plural (medium.com/@fbnlsr)](https://medium.com/@fbnlsr/the-table-naming-dilemma-singular-vs-plural-dc260d90aaff#1231)
* [Use singular nouns for database table names (teamten.com)](https://www.teamten.com/lawrence/programming/use-singular-nouns-for-database-table-names.html)
* [dzhim-cms/fmw/core/Autoload.php](https://github.com/janis-rullis/dzhim-cms/blob/665e359748f4b0f64412bf3f88fa43aa0503301c/fmw/core/Autoload.php#L7)

- This practice comes from a practice when an object files, classes, tables needs to be generated from a variable. It is easier to handle a singular word than plural. Example, man -> mans (incorrect but works), men on the other hand is harder to convert to singular without adding a new value.
- Mostly table names are shorter. Good when have a lot of relation tables.