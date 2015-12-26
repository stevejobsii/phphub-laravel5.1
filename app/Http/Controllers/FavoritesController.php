<?php
namespace App\Http\Controllers;

use App\Topic;
use App\Favorite;
use App\Notification;
use Auth;
use Redirect;

class FavoritesController extends Controller
{
    public function createOrDelete($id)
    {
        $topic = Topic::find($id);

        if (Favorite::isUserFavoritedTopic(Auth::user(), $topic)) {
            Auth::user()->favoriteTopics()->detach($topic->id);
        } else {
            Auth::user()->favoriteTopics()->attach($topic->id);
            Notification::notify('topic_favorite', Auth::user(), $topic->user, $topic);
        }
        flash()->success('hello!', lang('Operation succeeded.'));
        return Redirect::route('topics.show', $topic->id);
    }
}
