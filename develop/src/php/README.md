# CodeIgniter

## Installation

1. Download [CodeIgniter](https://codeigniter.com/) (version >= 3.1.5)
2. Unzip and upload all files to webspace
3. Read [CodeIgniter's User Guide](https://www.codeigniter.com/user_guide/)

# Example application

A example application is provided in the `/app` folder. Just upload all
the files via FTP and apply the following steps.

You need to [configure your database connection](https://www.codeigniter.com/user_guide/database/configuration.html) by editing
`/app/application/config/database.php` and your base site URL in
`/app/application/config/config.php`.

In this example the used database table is called 'sensor' and
looks like this:
```
+-----------+
| id | name |
+-----------+
|  1 |  pH  |
| .. |  ..  |
+-----------+
```

So be sure to provide such a table to make the example work.
