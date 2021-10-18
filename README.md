# Fullstack Test

### Development Process

1. Initiate a new Laravel app by running `laravel new periodic-tasks`.
2. You may then follow the git commits in the [r-kujawa/periodic-tasks](https://github.com/r-kujawa/periodic-tasks) repo.

### How to test

:exclamation: This is supposing that you have the essential software installed in order to run a docker container.

1. `git clone git@github.com:r-kujawa/periodic-tasks.git`
2. `cd periodic-tasks`
3. Open your editor and copy `.env.example` to `.env` and fill in the missing data as desired.
4. `composer install`
5. `vendor/bin/sail up` (You may also have a `sail` alias for this)
6. Open a new terminal window and run the following commands:
    1. `sail artisan key:generate`
    2. `sail artisan migrate`
    3. `sail npm install`
    4. `sail npm run dev` (Some bootstrap warnings might be thrown, but you may ignore)
7. Add `127.0.0.1 periodic-tasks.test` your `hosts` file.
8. Go to [periodic-tasks.test](Http://periodic-tasks.test)
9. Register to access the dashboard. ([periodic-tasks.test/register](http://periodic-tasks.test/register))
10. Once logged in go to [periodic-tasks.test/tasks](http://periodic-tasks.test/tasks) to test out the periodic tasking module.

### Considerations

- Supports periodic tasks daily, weekly (by week day), monthly & yearly.
- You may view your tasks by date range.
- You may show/hide completed tasks.
- You may mark any task as completed.

### Database

The database has the following structure:

![Database](https://user-images.githubusercontent.com/13485445/137765378-bb2011be-9960-4236-8d23-e5550dabcec3.png)
