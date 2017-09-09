<?php
namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'my_project');

// Project repository
set('repository', 'https://github.com/yhoiseth/texthub.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('texthub@139.59.161.132')
    ->stage('stage')
    ->set('deploy_path', '~/deployer-test');
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

