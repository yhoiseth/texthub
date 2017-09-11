<?php
namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'texthub');

// Project repository
set('repository', 'https://github.com/yhoiseth/texthub.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', [
    'var/collections',
]);

// Writable dirs by web server 
add('writable_dirs', [
    'var/collections',
]);


// Hosts

host('texthub@139.59.161.132')
    ->stage('stage')
    ->set('deploy_path', '~');
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('deploy:assets:upload', function () {
    runLocally('yarn run encore production');
    runLocally('rsync -avzp web/build/ texthub@stage.texthub.io:release/web/build');
});

/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    'deploy:assets',
    'deploy:vendors',
    'deploy:assets:install',
    'deploy:assetic:dump',
    'deploy:assets:upload',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

