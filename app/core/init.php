<?php


spl_autoload_register(function ($classname) {

    $modelFile = "../app/models/" . ucfirst($classname) . ".php";
    if (file_exists($modelFile)) {
        require $modelFile;
        return;
    }

    $controllerFile = "../app/controllers/" . ucfirst($classname) . ".php";
    if (file_exists($controllerFile)) {
        require $controllerFile;
        return;
    }

    $coreFile = "../app/core/" . ucfirst($classname) . ".php";
    if (file_exists($coreFile)) {
        require $coreFile;
        return;
    }
});
require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Controller.php';
require 'App.php';
