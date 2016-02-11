<?php
require 'recipe/common.php';
require '../vendor/deployphp/recipes/recipes/configure.php';


set('keep_releases', 5);

set('shared_files',
    [
        'src/Monitor/Config.json'
    ]
);


set('repository', 'https://github.com/phaniso/Multi-Server-Monitor.git');

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');
after('deploy', 'deploy:configure');
after('deploy', 'success');

serverList('config/servers.yml');
