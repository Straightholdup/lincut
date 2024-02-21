# Lincut

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## Requirements

- PHP
- Composer
- Docker
- Docker Compose

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/Straightholdup/lincut.git
    ```

2. Navigate to the project directory:

    ```bash
    cd lincut
    ```

3. Install PHP dependencies using Composer:

    ```bash
    composer install
    ```

4. Copy the `.env.example` file to `.env` and configure it according to your environment:

    ```bash
    cp .env.example .env
    ```

5. Generate application key:

    ```bash
    php artisan key:generate
    ```

6. Start Laravel Sail containers:

    ```bash
    ./vendor/bin/sail up -d
    ```

7. Run Migrations:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

## Usage

Swagger documentation is available for the API endpoints at `http://localhost:8000/api/documentation`. You can use this
URL to explore the API endpoints and interact with them using Swagger UI.
