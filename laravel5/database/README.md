# Database strucuture, migrations

## Why migrations are made using RAW SQLs and not ORM?

### Guaranteed result - What You See Is What Will Be Imported

Less space for side-effects.

### Easier to migrate the project

if the project is moved to other language, framework or version.

### What's wrong with using ORM or self defined PHP methods?

The choise to use RAW queries comes from a harsh experience.

#### Refactoring

Once worked in a project where migrations were written  in the regular way -using ORM and methods defined in database models.
Problems arise after a long code refactoring. Migration didn't work because the logic in the code has changed!

#### Migration to a different language

The migrations needs to be made incrementaly - step by step.
It is easier to copy RAW queries in specific steps, than trying to convert the code to the specific SQL.

In raw queries it will happen as defined and the refactor migrations will incrementaly updated databases.
