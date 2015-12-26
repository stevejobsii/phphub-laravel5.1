<?php
namespace App\Http\Controllers;

use App\good\Core\CreatorListener;
use App\good\Creators\ReplyCreator;
use Input;
use Redirect;
use App\Reply;

class RepliesController extends Controller implements CreatorListener
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function store()
    {
        return app('App\good\Creators\ReplyCreator')->create($this, Input::except('_token'));
    }

    public function vote($id)
    {
        $reply = Reply::find($id);
        app('App\good\Vote\Voter')->replyUpVote($reply);
        return Redirect::route('topics.show', [$reply->topic_id, '#reply'.$reply->id]);
    }

    public function destroy($id)
    {
        $reply = Reply::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($reply->user_id);
        $reply->delete();

        $reply->topic->decrement('reply_count', 1);

        Flash::success(lang('Operation succeeded.'));

        $reply->topic->generateLastReplyUserInfo();

        return Redirect::route('topics.show', $reply->topic_id);
    }

    /**
     * ----------------------------------------
     * CreatorListener Delegate
     * ----------------------------------------
     */

    public function creatorFailed($errors)
    {
        flash()->success(lang('Operation failed.'),'失败发布');
        return Redirect::back();
    }

    public function creatorSucceed($reply)
    {
        flash()->success(lang('Operation succeeded.'),'成功发布');
        return Redirect::route('topics.show', array(Input::get('topic_id'), '#reply'.$reply->id));
    }
}
