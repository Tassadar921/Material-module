# Small Symfony material project

Symfony 7.1.5, Php 8.3.6, PostgreSQL 16.4

This project is a small project to manage materials. It is a simple project to show some Symfony features.

## Installation

1. Clone the repository
2. Create .env.local file and add missing values from .env file
2. Run `composer install`
3. Run `php bin/console doctrine:database:create`
4. Run `php bin/console doctrine:migrations:migrate`
5. Run `php bin/console doctrine:fixtures:load`
6. Run `npm install`

## Usage
1. Run `symfony server:start`
2. In another terminal run `npx vite`
3. Open `http://localhost:3000` in your browser

## Features

### Datatable of materials
- List of materials with pagination
- Sorting by id to avoid default sorting by updated_at
- Filtering by name
- Decrementing of incrementing quantity of material (if quantity is 0, it will disappear from default display and send an email to admin)
- PDF visualization of material
- Possibility to show out of stock materials
- Possibility to show a specific material informations into a modal

### Material creation, Material edition
- Form to create a material
- Validation of form
- Price taxes automatically calculated
- Price tx-included is not editable as it is calculated from price tax-free and vat rate

### Material edition
- Possibility to hard delete a material
- Possibility to go back to material index