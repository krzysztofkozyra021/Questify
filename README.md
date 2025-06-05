# Questify

Transform your daily tasks into a harmonious adventure. Questify blends RPG game mechanics with AI to help maintain a healthy life rhythm—boosting productivity while supporting your well-being.

## Local development

### System requirements / Prerequisites
- Linux with installed Docker (or Windows with some additional configuration) [Docker Guide](https://docs.docker.com/engine/install/debian/)

- Bash shell

- GNU Make 4.x

- PHP 8.2 with modules: 
    - xml
    - curl
    - zip
    - cli
- Composer 2.5.x

- Node.js & npm

- Git installed

**To install all dependencies on Debian/Ubuntu with apt**
```bash
sudo apt update
sudo apt install -y make php php8.2-cli php8.2-xml php8.2-curl php8.2-zip composer npm git
```

### Configuration for local development
1. Add yourself to docker group, to run containers without sudo (you can follow guide linked above)
```bash 
sudo usermod -aG docker $USER
```
Logout and login to apply changes, or run:
```bash
newgrp docker
```
Now make sure you can run commands without sudo
```bash
docker run hello-world
```
2.Clone this repository and go to main directory:
```bash
git clone https://github.com/krzysztofkozyra021/Questify.git
```
3.Prepare app environment file:
```bash
cp .env.example .env
```
Or, if you have all dependencies and are using Linux:
```bash
make init
```
only if you have every dependency installed (and running Linux)
> [!NOTE] 
> This command builds Docker containers, starts them, and initializes the environment.
---
4.**After make init is completed, stop the containers with command:**
```bash
make stop
```
5.Switch to branch **develop**
```bash
git checkout develop
```
6.Install all frontend and backend dependencies with:
```bash
npm install
composer install
```
7.Open a shell in the app container:
```bash
make shell
```
8.Run migrations and seed the database:
```bash
php artisan migrate:fresh --seed
exit
```
9.Start the development server:
```bash
make dev
```
**App will be available at [localhost:63851](http://localhost:63851)**

---

### Commands
Before running any of the commands below, you must run shell:

```bash
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

### Make Commands

| Make                    | Task                                        |
|:------------------------|:--------------------------------------------|
| `init`                 | Initializes project by checking env file, building containers, running them and setting up test database |
| `build`                | Builds Docker containers                    |
| `run`                  | Starts Docker containers in detached mode   |
| `stop`                 | Stops running Docker containers            |
| `restart`              | Restarts Docker containers                 |
| `shell`                | Opens bash shell in app container as current user |
| `shell-root`           | Opens bash shell in app container as root  |
| `test`                 | Runs backend tests                         |
| `fix`                  | Lints and fixes backend files              |
| `analyse`              | Runs Larastan analysis for backend files   |
| `dev`                  | Runs frontend development server           |
| `queue`                | Starts Laravel queue worker                |
| `create-test-db`       | Creates test database                      |

Thanks to **[@blumilksoftware](https://github.com/blumilksoftware/boilerplate)** for their boilerplate laravel configuration


### Containers

| service    | container name            | default host port               |
|:-----------|:--------------------------|:--------------------------------|
| `app`      | `Questify-app-dev`     | [63851](http://localhost:63851) |
| `database` | `Questify-db-dev`      | [63853](http://localhost:63853) |
| `redis`    | `Questify-redis-dev`   | [63852](http://localhost:63852) |
| `mailpit`  | `Questify-mailpit-dev` | [63854](http://localhost:63854) |
