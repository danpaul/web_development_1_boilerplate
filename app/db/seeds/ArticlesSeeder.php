<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ArticlesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'Getting started with Docker Compose',
                'content' => 'Docker Compose lets you define all services of an application in one YAML file. With a single command you can start the web server, PHP and the database together. This makes it easy for every developer to run the same setup.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-01 09:00:00',
            ],
            [
                'title' => 'How nginx serves a PHP application',
                'content' => 'Nginx handles incoming HTTP requests and serves static files directly. Requests for PHP are passed to php-fpm over FastCGI. This split keeps the web server fast and the PHP processes isolated.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-02 09:00:00',
            ],
            [
                'title' => 'Understanding HTTP methods',
                'content' => 'HTTP methods tell the server what the client wants to do with a resource. GET reads data, POST creates data, and PUT and DELETE update or remove it. Choosing the right method makes your application easier to understand.',
                'author' => 'Chloe Peters',
                'created_at' => '2026-10-03 09:00:00',
            ],
            [
                'title' => 'Managing dependencies with Composer',
                'content' => 'Composer is the standard dependency manager for PHP. You list the packages you need in composer.json and Composer downloads them into the vendor folder. The composer.lock file makes sure everyone installs exactly the same versions.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-04 09:00:00',
            ],
            [
                'title' => 'PSR-4 autoloading explained',
                'content' => 'PSR-4 maps namespaces to folders so classes load automatically. With the App namespace mapped to the src folder, you never need to write require statements for your own classes. Remember to run dump-autoload after adding new namespaces.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-05 09:00:00',
            ],
            [
                'title' => 'Routing requests with FastRoute',
                'content' => 'A router decides which code handles each URL. FastRoute matches the HTTP method and path against a list of routes and returns the handler. Unknown paths give a 404 and wrong methods give a 405.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-06 09:00:00',
            ],
            [
                'title' => 'The front controller pattern',
                'content' => 'A front controller sends every request through a single entry point, usually index.php. That file sets up autoloading and routing before calling the right controller. This keeps shared setup code in one place.',
                'author' => 'Chloe Peters',
                'created_at' => '2026-10-07 09:00:00',
            ],
            [
                'title' => 'An introduction to MVC',
                'content' => 'MVC splits an application into models, views and controllers. Models hold the data logic, views render the output, and controllers connect the two. This separation makes code easier to test and change.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-08 09:00:00',
            ],
            [
                'title' => 'Connecting to MariaDB with PDO',
                'content' => 'PDO is the built-in PHP extension for talking to databases. You create a connection with a DSN, a username and a password. Setting the error mode to exceptions makes database problems much easier to spot.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-09 09:00:00',
            ],
            [
                'title' => 'Prepared statements prevent SQL injection',
                'content' => 'SQL injection happens when user input is pasted directly into a query. Prepared statements send the query and the values separately, so input can never change the SQL. Always use placeholders for values that come from users.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-10 09:00:00',
            ],
            [
                'title' => 'Why use database migrations',
                'content' => 'Migrations describe every schema change as a versioned PHP file. Everyone on the team can apply the same changes in the same order with one command. They replace manual changes in phpMyAdmin that are easy to forget.',
                'author' => 'Chloe Peters',
                'created_at' => '2026-10-11 09:00:00',
            ],
            [
                'title' => 'Seeding a database with sample data',
                'content' => 'Seeders fill tables with data you can use during development. Instead of typing rows by hand, you run one command and get a predictable dataset. This helps when building pages that list or search records.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-12 09:00:00',
            ],
            [
                'title' => 'Keeping secrets out of Git with .env files',
                'content' => 'Passwords and other secrets should never be committed to a repository. A .env file stores them locally and is listed in .gitignore. A tracked .env.example file shows which variables are needed.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-13 09:00:00',
            ],
            [
                'title' => 'Writing semantic HTML',
                'content' => 'Semantic HTML uses elements that describe their meaning, such as header, nav, article and footer. It helps screen readers and search engines understand your page. It also makes your markup easier to read.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-14 09:00:00',
            ],
            [
                'title' => 'CSS Flexbox basics',
                'content' => 'Flexbox is a CSS layout model for arranging items in a row or column. Properties like justify-content and align-items control spacing and alignment. It is a simple way to build navigation bars and card layouts.',
                'author' => 'Chloe Peters',
                'created_at' => '2026-10-15 09:00:00',
            ],
            [
                'title' => 'Escaping output to prevent XSS',
                'content' => 'Cross-site scripting happens when user input is shown on a page without escaping. An attacker can then run their own JavaScript in other users\' browsers. Use htmlspecialchars whenever you print data that came from users.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-16 09:00:00',
            ],
            [
                'title' => 'Storing passwords safely',
                'content' => 'Passwords must never be stored as plain text in a database. PHP provides password_hash to create a secure hash and password_verify to check it. These functions choose a strong algorithm and salt for you.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-17 09:00:00',
            ],
            [
                'title' => 'Working with Git branches',
                'content' => 'A Git branch lets you work on a feature without touching the main code. When the work is done, you merge the branch back or open a pull request. Small, focused branches are easier to review.',
                'author' => 'Bram de Vries',
                'created_at' => '2026-10-18 09:00:00',
            ],
            [
                'title' => 'Sessions and cookies in PHP',
                'content' => 'HTTP is stateless, so each request knows nothing about the previous one. PHP sessions store data on the server and use a cookie to link it to the browser. This is how login systems remember who you are.',
                'author' => 'Chloe Peters',
                'created_at' => '2026-10-19 09:00:00',
            ],
            [
                'title' => 'Returning JSON from a PHP API',
                'content' => 'Many front ends load data from an API instead of full HTML pages. In PHP you set the Content-Type header to application/json and print the result of json_encode. Clear status codes help the client handle errors.',
                'author' => 'Alice Jansen',
                'created_at' => '2026-10-20 09:00:00',
            ],
        ];

        $articles = $this->table('articles');
        $articles->truncate();
        $articles->insert($data)->saveData();
    }
}
