<?php
return  array(
    'DB_TYPE'   =>  'mysql',
    'DB_HOST'   =>  '127.0.0.1',//数据库连接，这里不要写错了，虚拟主机用户注意，还有阿里云RDS数据要用阿里云的RDS提供的连接
    'DB_NAME'   =>  'nihaopl',//数据库名字，不要用记事本修改，用nopad++
    'DB_USER'   =>  'root',//数据库用户名，不要用记事本修改，用nopad++
    'DB_PWD'    =>  '',//数据库密码，不要用记事本修改，用nopad++
    'DB_PORT'   =>   3306 ,
    'DB_CHARSET'=>  'utf8',
    'DB_PREFIX' =>  'bao_',//表前缀，不懂技术的不要修改
    'AUTH_KEY'  =>  '520efebc109577cc0a86de013d0164ac', //这个KEY只是保证部分表单在没有SESSION 的情况下判断用户本人操作的作用
    'BAO_KEY'   => '',
);