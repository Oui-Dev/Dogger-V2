# Dogger

School project with [Vue 3](https://vuejs.org/), [Laravel 10](https://laravel.com/) and [Inertia.js](https://inertiajs.com/).

![Dogger logo](/public/images/logo_full.png)

You can access to the project here [https://dogger.host](https://dogger.host).

## How to run the project

First you need to install all depedancies.

### `npm install`
### `composer install`

Next copy the '.env.example' file and rename it to '.env', edit them with your informations.
After setup the database (you need to create an empty database before run this command).

### `php artisan migrate:fresh --seed`

Next you need to generate the routing file.

### `php artisan ziggy:generate`

Finally build the app.

### `npm run build`

The page will reload when you make changes.\
You may also see any lint errors in the console.

### `npm run build`
### `php artisan serve`

Open [http://localhost:8000](http://localhost:8000) to view it in your browser.

## How to connect Dogger to your projects with NPM package

### `npm install dogger-npm`

```js
import { dogger_catch }  from 'dogger-npm';

try {
  // Your code here
} catch (error) {
  dogger_catch(yourProjectKey, error)
}
```

or

```js
import { dogger }  from 'dogger-npm';

dogger(yourProjectKey, "message", "code", "path", "line", "timestamp")
```

## How to connect Dogger to your projects without NPM package

Axios example :

```js
axios.post("https://dogger.host/api/errors/new", {
  message: "Error message", // optional
  code: 500, // optional
  path: "/", // optional
  line: 1, // optional
  timestamp: "2023-01-01 00:00:00" // optional (default: now)
}, { Authorization: `Bearer yourProjectKey` })
```
