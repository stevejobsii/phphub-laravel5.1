<?php namespace App\good\Creators;

use App\Http\Requests\ReplyCreationForm;
use App\good\Core\CreatorListener;
use App\good\Core\Robot;
use App\good\Notification\Mention;
use App\good\Notification\Notifier;
use App\Reply;
use Auth;
use App\Topic;
use App\Notification;
use Carbon\Carbon;
use App;
use App\good\Markdown\Markdown;
use Slack;

class ReplyCreator
{
    protected $form;
    protected $mentionParser;

    public function __construct(ReplyCreationForm $form, Mention $mentionParser)
    {
        $this->form = $form;
        $this->mentionParser = $mentionParser;
    }

    public function create(CreatorListener $observer, $data)
    {
        $data['user_id'] = Auth::id();
        $data['body'] = $this->mentionParser->parse($data['body']);

        $markdown = new Markdown;
        $data['body_original'] = $data['body'];
        $data['body'] = $markdown->convertMarkdownToHtml($data['body']);

        // Validation
        $this->form->validate($data);

        $reply = Reply::create($data);
        if (! $reply) {
            return $observer->creatorFailed($reply->getErrors());
        }

        // Add the reply user
        $topic = Topic::find($data['topic_id']);
        $topic->last_reply_user_id = Auth::id();
        $topic->reply_count++;
        $topic->updated_at = Carbon::now()->toDateTimeString();
        $topic->save();

        Auth::user()->increment('reply_count', 1);

        App::make('App\good\Notification\Notifier')->newReplyNotify(Auth::user(), $this->mentionParser, $topic, $reply);

        Robot::notify($data['body_original'], 'Reply', $topic, Auth::user());

        return $observer->creatorSucceed($reply);
    }
}
