<h4>Prerequisites:</h4>
You must have PHP > 7.0, Laravel > 5.0, Web server (Apache/Ngnix), Node.js and npm installed on your machine

<h4>Please follow below steps to install the package to a new or an existing Laravel site:</h4>

1. Run `php artisan make:auth` to scaffold basic login and registration views and routes

2. Install package using `composer require xparthxvorax/larachat` command

3. Run `php artisan larachat:install` command

4. Setup front-end:
   - Run `npm install -g laravel-echo-server` command
   - Add below dependencies to the `package.json` file:
   ```
   "dependencies": {
      "laravel-echo": "^1.4.1",
      "socket.io-client": "^2.1.1"
   }
   ```
   - Run `npm install` command
   - Add below lines to `resources/js/bootstrap.js`
   ```
   import Echo from "laravel-echo"
   window.io = require('socket.io-client');
   window.Echo = new Echo({
       broadcaster: 'socket.io',
       host: window.location.hostname + ':6001'
   });
   ```
   - Add below line to `resources/js/app.js`
   ```
   Vue.component('larachat-component', require('./components/LarachatComponent.vue'));
   ```
   - To display a user list, add `<larachat-component></larachat-component>` in `resources/views/home.blade.php` like:
   ```
   <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
                <larachat-component></larachat-component>
               </div>
           </div>
       </div>
   </div>
   ```
      Or you may put `<larachat-component></larachat-component>` to any other view wherever it fits best as per your site design layout.       Also you may modify Vue component's default HTML and CSS into `resources\js\components\LarachatComponent.vue` as per your               requirement.

5. Set `BROADCAST_DRIVER=redis` and add `LARAVEL_ECHO_SERVER_AUTH_HOST=your-site-url`

6. Runn `npm run dev` command to compile front-end assets 

7. Run `laravel-echo-server start` to start socket server. Keep the command running or you may use supervisor for that(https://laravel.com/docs/master/queues#supervisor-configuration)

**Demo video:** https://www.youtube.com/watch?v=GKeMBxQWOyw

**Live demo site:** http://52.66.210.249/larachat/public/

1. Account 1:

      Username: kylie71@example.org

      Password: secret

2. Account 2:

      Username: pfannerstill.eloise@example.org

      Password: secret

3. Account 3:

      Username: tremaine.kiehn@example.com

      Password: secret
