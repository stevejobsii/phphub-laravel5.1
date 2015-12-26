<?php 
namespace App\Http\Controllers;

use App\Topic;
use App\Attention;
use App\Notification;
use Auth;
use Redirect;

class AttentionsController extends Controller {
    public function createOrDelete($id)
    {
        $topic = Topic::find($id);

        if (Attention::isUserAttentedTopic(Auth::user(), $topic)) {
            $message = lang('Successfully remove attention.');
            Auth::user()->attentTopics()->detach($topic->id);
        } else {
            $message = lang('Successfully_attention');
            Auth::user()->attentTopics()->attach($topic->id);
            Notification::notify('topic_attent', Auth::user(), $topic->user, $topic);
        }
        flash()->success('hello!', $message);
        return Redirect::route('topics.show', $topic->id);
    }
}
