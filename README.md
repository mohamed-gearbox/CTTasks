
# About CTTasks

A very simple task manager scaffolding to create, edit and prioritize task entries using Laravel 8 and VUE3

Task drag&drop feature is powered by vuedraggable, you can read more about it here:
[Vue.Draggable](https://github.com/SortableJS/Vue.Draggable)

Laravel is accessible, powerful, and provides tools required for large, robust applications, this is a very simple test app.

## Setup

Run `composer install` to install any Laravel packages in composer.lock

Run `npm install` to install package-lock npm packages especially vue, vue-loader and vuedraggable if you want to recompile vue components, js and css files as part of laravel-mix.

## Configure and initialize the database

This app uses MySQL database driver, you need to configure your database connection and access credentials in the .env file.

Run `php artisan migrate --seed` on a fresh install, or `php artisan migrate:refresh --seed` to initialize or re-initialize the database tables and seed test data.

## Features

- Task filtering by Project selection
- Drag & Drop tasks to change their priority in the queue
- Simple CRUD functionality for managing tasks

