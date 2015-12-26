<?php

# ------------------ Route patterns---------------------
Route::pattern('id', '[0-9]+');

# ------------------ Page Route ------------------------

Route::get('/', [
    'as' => 'home',
    'uses' => 'PagesController@home',
]);

Route::get('/about', [
    'as' => 'about',
    'uses' => 'PagesController@about',
]);

Route::get('/wiki', [
    'as' => 'wiki',
    'uses' => 'PagesController@wiki',
]);

Route::get('/feed', [
    'as' => 'feed',
    'uses' => 'PagesController@feed',
]);
# ------------------ User stuff ------------------------

Route::get('/users/{id}/replies', [
    'as' => 'users.replies',
    'uses' => 'UsersController@replies',
]);

Route::get('/users/{id}/topics', [
    'as' => 'users.topics',
    'uses' => 'UsersController@topics',
]);

Route::get('/users/{id}/favorites', [
    'as' => 'users.favorites',
    'uses' => 'UsersController@favorites',
]);

Route::get('/users/{id}/refresh_cache', [
    'as' => 'users.refresh_cache',
    'uses' => 'UsersController@refreshCache',
]);

Route::get('/users/{id}/access_tokens', [
    'as' => 'users.access_tokens',
    'uses' => 'UsersController@accessTokens',
]);

Route::get('/access_token/{token}/revoke', [
    'as' => 'users.access_tokens.revoke',
    'uses' => 'UsersController@revokeAccessToken',
]);

Route::get('users/regenerate_login_token', [
    'as' => 'users.regenerate_login_token',
    'uses' => 'UsersController@regenerateLoginToken',
]);

Route::post('/favorites/{id}', [
    'as' => 'favorites.createOrDelete',
    'uses' => 'FavoritesController@createOrDelete',
    'before' => 'auth',
]);
//通知中心
Route::get('/notifications', [
    'as' => 'notifications.index',
    'uses' => 'NotificationsController@index',
    'before' => 'auth',
]);
//获取通知数
Route::get('/notifications/count', [
    'as' => 'notifications.count',
    'uses' => 'NotificationsController@count',
    'before' => 'auth',
]);

Route::post('/attentions/{id}', [
    'as' => 'attentions.createOrDelete',
    'uses' => 'AttentionsController@createOrDelete',
    'before' => 'auth',
]);
//上传avatar
Route::post('/settings/update-avatar', [
    'as' => 'users.avatarupdate',
    'uses' => 'UsersController@avatarupdate',
]);
//上传修改秘密
Route::post('/settings/resetPassword',[
    'as' => 'users.resetPassword',
    'uses' => 'UsersController@resetPassword',
]);
# ------------------ Authentication ------------------------

Route::get('login', [
    'as' => 'login',
    'uses' => 'AuthController@login',
]);

Route::get('signup', [
    'as' => 'signup',
    'uses' => 'AuthController@create',
]);

Route::post('signup',  [
    'as' => 'signup',
    'uses' => 'AuthController@store',
]);

Route::get('logout', [
    'as' => 'logout',
    'uses' => 'AuthController@logout',
]);

Route::get('oauth', 'AuthController@getOauth');

# ------------------ Resource Route ------------------------

Route::resource('nodes', 'NodesController', ['except' => ['index', 'edit']]);
Route::resource('topics', 'TopicsController');
Route::resource('votes', 'VotesController');
Route::resource('users', 'UsersController');

# ------------------ Replies ------------------------

Route::resource('replies', 'RepliesController', ['only' => ['store']]);
Route::delete('replies/delete/{id}',  [
    'as' => 'replies.destroy',
    'uses' => 'RepliesController@destroy',
    'before' => 'auth',
]);

# ------------------ Votes ------------------------

Route::group(['before' => 'auth'], function () {
    Route::post('/topics/{id}/upvote', [
        'as' => 'topics.upvote',
        'uses' => 'TopicsController@upvote',
    ]);

    Route::post('/topics/{id}/downvote', [
        'as' => 'topics.downvote',
        'uses' => 'TopicsController@downvote',
    ]);

    Route::post('/replies/{id}/vote', [
        'as' => 'replies.vote',
        'uses' => 'RepliesController@vote',
    ]);

    Route::post('/topics/{id}/append', [
        'as' => 'topics.append',
        'uses' => 'TopicsController@append',
    ]);
});

# ------------------ Admin Route ------------------------

Route::group(['before' => 'manage_topics'], function () {
    //推荐
    Route::post('topics/recomend/{id}',  [
        'as' => 'topics.recomend',
        'uses' => 'TopicsController@recomend',
    ]);
    //维基
    Route::post('topics/wiki/{id}',  [
        'as' => 'topics.wiki',
        'uses' => 'TopicsController@wiki',
    ]);
    //置顶
    Route::post('topics/pin/{id}',  [
        'as' => 'topics.pin',
        'uses' => 'TopicsController@pin',
    ]);
    //删除
    Route::delete('topics/delete/{id}',  [
        'as' => 'topics.destroy',
        'uses' => 'TopicsController@destroy',
    ]);
    //下沉
    Route::post('topics/sink/{id}',  [
        'as' => 'topics.sink',
        'uses' => 'TopicsController@sink',
    ]);
});

//管理员屏蔽用户
Route::group(['before' => 'manage_users'], function () {
    Route::post('users/blocking/{id}',  [
        'as' => 'users.blocking',
        'uses' => 'UsersController@blocking',
    ]);
});

Route::post('upload_image', [
    'as' => 'upload_image',
    'uses' => 'TopicsController@uploadImage',
    'before' => 'auth',
]);

// Health Checking
Route::get('heartbeat', function () {
    return \App\Node::first();
});

# ------------------ Password reset stuff ------------------------
// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::get('auth/qq','Auth\AuthController@qq');
Route::get('auth/weixin','Auth\AuthController@weixin');
Route::get('auth/weibo','Auth\AuthController@weibo');
Route::get('auth/{provider}/callback', 'Auth\AuthController@callback');

Route::get('auth/login-required', 'Auth\AuthController@loginRequired');
Route::get('auth/admin-required', 'Auth\AuthController@adminRequired');
Route::get('auth/user-banned',  'Auth\AuthController@userBanned');

Route::controllers([
            'auth'=>'Auth\AuthController',
            'password'=>'Auth\PasswordController'
        ]);

