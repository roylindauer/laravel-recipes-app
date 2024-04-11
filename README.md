# Just recipes

> Learning Laravel by building a simple CRUD app for recipes

## Requirements

- **PHP** >= 8.4
- **NodeJS** >= 18
- [**Overmind**](https://github.com/DarthSim/overmind)
- [**ASDF**](https://asdf-vm.com) w/PHP and NodeJS plugins

## Setup

- **Install Overmind** (Assume you have Homebrew. If not, you can install it [here](https://brew.sh)
  ```shell
  brew install overmind
  ```

- **Install PHP and NodeJS** (ASDF is required for this project, you can install it [here](https://asdf-vm.com)
  ```shell
  asdf install
  ```

- **Install dependencies**
  ```shell
  composer install
  npm install
  ```

- **Create `.env` file**
  ```shell
  cp .env.example .env
  ```

- **You need the APP_KEY**. Generate one if you don't have it.
  ```shell
  php artisan key:generate
  ```

- **Setup Database**
  ```shell
  php artisan migrate
  php artisan db:seed --class=RecipeSeeder
  ```

- **Setup Storage Links** (I am pretty sure this is required for the uploaded images to work properly)
  ```shell
  php artisan storage:link
  ```
- **Start the server**
  ```shell
  ./bin/dev
  ```

## Docker Based Setup (WIP)

- **Setup**
  ```shell
  docker compose build
  docker compose run --rm app bin/setup
  docker compose up 
  ```
