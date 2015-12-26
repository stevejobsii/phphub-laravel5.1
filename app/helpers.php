<?php
/**
 * Helper for a message system using SweetAlert
 *
 * @param null $title
 * @param null $message
 *
 * @return \Illuminate\Foundation\Application|mixed
 */
function flash($title = null, $message = null)
{
    $flash = app('App\Http\Flash'); // Holt das Objekt aus dem IOC.
    if (func_num_args() == 0) {
        return $flash;
    }
    return $flash->info($title, $message);
}

function lang($text)
{
    return str_replace('phphub.', '', trans('phphub.'.$text));
}

function cdn($filepath)
{
    if (Config::get('app.url_static')) {
        return Config::get('app.url_static') . $filepath;
    } else {
        return Config::get('app.url') . $filepath;
    }
}

function getCdnDomain()
{
    return Config::get('app.url_static') ?: Config::get('app.url');
}

function getUserStaticDomain()
{
    return Config::get('app.user_static') ?: Config::get('app.url');
}