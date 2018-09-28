bangumi-data to mysql
==========================
框架: [CodeIgniter](https://github.com/bcit-ci/CodeIgniter)

基础模型: [CodeIgniter-MY_Model](https://github.com/avenirer/CodeIgniter-MY_Model)

数据源: [bangumi-data](https://github.com/bangumi-data/bangumi-data/)


## 前期工作

#### 设置url_rewrite

apache可以直接使用.htaccess, nginx请自行设置

#### CodeIgniter

我只上传了application部分的文件, CodeIgniter的主文件请自行下载

#### 建立表结构

导入 sql/table.sql


## 设置

#### 设置application/config/database.php

```php
$db['default'] = array(
    'dsn'	=> '',
	'hostname' => 'localhost', // MySQL地址
	'username' => 'root',      // MySQL用户
	'password' => '12345678',  // MySQl密码
	'database' => 'bangumi',   // MySQL数据库
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
```

#### 设置application/config/github.php

```php
$config["github_user"]  = ""; // github用户
$config["github_token"] = ""; // github token
```
