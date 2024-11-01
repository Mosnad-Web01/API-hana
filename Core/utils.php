<?php
function dd($value){
    var_dump($value);
    die();
};

function isUrl($url){
    return $_SERVER['REQUEST_URI'] === $url;
}

function base_path($path='')
{
    return  BASE_PATH.$path;
}
function view($path='', $vars=[])
{
    extract($vars);
    return require base_path("views/$path");

}

function authorize($condition)
{
    if(!$condition){
        die('Unauthorized');
    }

}