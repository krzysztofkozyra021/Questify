## Questify

Transform your daily tasks into a harmonious adventure. Questify combines RPG elements and AI that helps maintain a healthy life rhythm, increasing productivity while caring for your wellbeing.

### Local development
```
cp .env.example .env
make init
make dev
```
Application will be running under [localhost:63851](localhost:63851) and [http://Questify.localhost/](http://Questify.blumilk.localhost/)

#### Commands
Before running any of the commands below, you must run shell:
```
make shell
```

| Command                 | Task                                        |
|:------------------------|:--------------------------------------------|
| `composer <command>`    | Composer                                    |
| `composer test`         | Runs backend tests                          |
| `composer analyse`      | Runs Larastan analyse for backend files     |
| `composer cs`           | Lints backend files                         |
| `composer csf`          | Lints and fixes backend files               |
| `php artisan <command>` | Artisan commands                            |
| `npm run dev`           | Compiles and hot-reloads for development    |
| `npm run build`         | Compiles and minifies for production        |
| `npm run lint`          | Lints frontend files                        |
| `npm run lintf`         | Lints and fixes frontend files              |
| `npm run tsc`           | Runs TypeScript checker                     |


#### Containers

| service    | container name            | default host port               |
|:-----------|:--------------------------|:--------------------------------|
| `app`      | `Questify-app-dev`     | [63851](http://localhost:63851) |
| `database` | `Questify-db-dev`      | 63853                           |
| `redis`    | `Questify-redis-dev`   | 63852                           |
| `mailpit`  | `Questify-mailpit-dev` | 63854                           |
