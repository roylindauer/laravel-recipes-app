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

- **Create `.env` file** (you will need the encryption key)
  ```shell
  php artisan env:decrypt --key=`cat config/master.key`
  ```

- **Setup Database**
  ```shell
  php artisan migrate
  php artisan db:seed
  ```

- **Setup Storage**
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


# Things To Do Next

- [ ] Add Docker based setup
- [ ] Use schema.org parser - https://packagist.org/packages/spatie/schema-org 
- [ ] Add tests
- [ ] Deploy to VM in homelab 
