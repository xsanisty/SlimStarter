<?php

/**
 * Hook, filter, etc should goes here
 */

/**
 * error handling sample
 *
 * $app->error(function() use ($app){
 *     $app->render('error.html');
 * });
 */


/**
 * Boot up Eloquent
 */

use Illuminate\Database\Capsule\Manager as Capsule;
$app->hook('slim.before', function() use ($app, $config){
    try{
        $capsule = new Capsule;
        $app->container->singleton('db',function() use ($capsule){
            return $capsule;
        });

        $app->db->addConnection($config['database']);
        $app->db->setAsGlobal();
        $app->db->bootEloquent();

        /**
         * Setting up Sentry
         */
        Sentry::setupDatabaseResolver($app->db->connection()->getPdo());
    }catch(PDOException $e){
        if(file_exists(PUBLIC_PATH.'install.php') && defined('INSTALL')){

            $publicPath  = dirname($_SERVER['SCRIPT_NAME']).'/';
            $installPath = Request::getUrl().$publicPath.'install.php';
            Response::redirect($installPath);
        }else{
            $app->error();
        }
    }catch(Exception $e){
        $app->error();
    }
});