<?php namespace App\Http\Controllers;

use App\good\Core\CreatorListener;
use App\Http\Requests\TopicCreationForm;
use Carbon\Carbon;
use Illuminate\Http\Request as urlRequest;
use Config;
use Redirect;
use App\Topic;
use Intervention\Image\ImageManagerStatic as Image;
use Input;
use Auth;
use App\Link;
use App\SiteStatus;
use App\good\Markdown\Markdown;
use App\Append;
use App\Node;
use App\Notification;

class TopicsController extends Controller implements CreatorListener
{
    protected $topic;

    public function __construct(Topic $topic)
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'search']]);
        $this->topic = $topic;
    }

    public function index(urlRequest $request)
    {
        $search = $request->query('q');
        $filter = $this->topic->present()->getTopicFilter();
        $topics = $this->topic->getTopicsSearchWithFilter($filter)->search($search)->paginate(20);
        $nodes  = Node::allLevelUp();
        $links  = Link::all();
        $this->setupLayout();

        return view('topics.index', compact('topics', 'nodes', 'links'));
    }

    public function create(urlRequest $request)
    {
        $node =  Node::find($request->query('node_id'));
        $nodes = Node::allLevelUp();
        SiteStatus::newTopic();

        return view('topics.create_edit', compact('nodes', 'node'));
    }

    public function store(urlRequest $request)
    {
        return app('App\good\Creators\TopicCreator')->create($this, $request->except('_token'));
    }

    public function show($id)
    {
        $topic = \App\Topic::findOrFail($id);
        $replies = $topic->getRepliesWithLimit();
        $node = $topic->node;
        $nodeTopics = $topic->getSameNodeTopics();
        $this->setupLayout();
        $topic->increment('view_count', 1);

        return view('topics.show', compact('topic', 'replies', 'nodeTopics', 'node'));
    }

    public function edit($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $nodes = Node::allLevelUp();
        $node = $topic->node;

        $topic->body = $topic->body_original;

        return view('topics.create_edit', compact('topic', 'nodes', 'node'));
    }

    public function append($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);

        $markdown = new Markdown;
        $content = $markdown->convertMarkdownToHtml(Input::get('content'));

        $append = Append::create(['topic_id' => $topic->id, 'content' => $content]);

        app('App\good\Notification\Notifier')->newAppendNotify(Auth::user(), $topic, $append);

        flash()->success('Good Jobs!', lang('Operation succeeded.'));
        return Redirect::route('topics.show', $topic->id);
    }

    public function update($id)
    {
        $topic = Topic::findOrFail($id);
        $data = Input::only('title', 'body', 'node_id');

        $this->authorOrAdminPermissioinRequire($topic->user_id);

        $markdown = new Markdown;
        $data['body_original'] = $data['body'];
        $data['body'] = $markdown->convertMarkdownToHtml($data['body']);
        $data['excerpt'] = Topic::makeExcerpt($data['body']);

        // Validation
        app('App\Http\Requests\TopicCreationForm')->validate($data);

        $topic->update($data);

        flash()->success('Good jobs!', lang('Operation succeeded.'));
        return Redirect::route('topics.show', $topic->id);
    }

    /**
     * ----------------------------------------
     * User Topic Vote function
     * ----------------------------------------
     */

    public function upvote($id)
    {
        $topic = Topic::find($id);
        app('App\good\Vote\Voter')->topicUpVote($topic);
        return Redirect::route('topics.show', $topic->id);
    }

    public function downvote($id)
    {
        $topic = Topic::find($id);
        app('App\good\Vote\Voter')->topicDownVote($topic);
        return Redirect::route('topics.show', $topic->id);
    }

    /**
     * ----------------------------------------
     * Admin Topic Management//管理员
     * ----------------------------------------
     */

    public function recomend($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->is_excellent = (!$topic->is_excellent);
        $topic->save();
        flash()->success('Good Jobs!', lang('Operation succeeded.'));
        Notification::notify('topic_mark_excellent', Auth::user(), $topic->user, $topic);
        return Redirect::route('topics.show', $topic->id);
    }

    public function wiki($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->is_wiki = (!$topic->is_wiki);
        $topic->save();
        flash()->success('Good Jobs!', lang('Operation succeeded.'));
        Notification::notify('topic_mark_wiki', Auth::user(), $topic->user, $topic);
        return Redirect::route('topics.show', $topic->id);
    }

    public function pin($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        ($topic->order > 0) ? $topic->decrement('order', 1) : $topic->increment('order', 1);
        return Redirect::route('topics.show', $topic->id);
    }

    public function sink($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        ($topic->order >= 0) ? $topic->decrement('order', 1) : $topic->increment('order', 1);
        return Redirect::route('topics.show', $topic->id);
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->delete();
        Flash::success(lang('Operation succeeded.'));

        return Redirect::route('topics.index');
    }

    public function uploadImage()
    {
        if ($file = Input::file('file')) {
            $allowed_extensions = ["png", "jpg", "gif"];
            if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                return ['error' => 'You may only upload png, jpg or gif.'];
            }

            $fileName        = $file->getClientOriginalName();
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $folderName      = '/images/catalog/' . date("Ym", time()) .'/'.date("d", time()) .'/'. Auth::user()->id;
            $destinationPath = public_path() . '/' . $folderName;
            $safeName        = str_random(10).'.'.$extension;
            $file->move($destinationPath, $safeName);

            // If is not gif file, we will try to reduse the file size
            if ($file->getClientOriginalExtension() != 'gif') {
                // open an image file
                $img = Image::make($destinationPath . '/' . $safeName);
                // prevent possible upsizing
                $img->resize(1440, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                // finally we save the image as a new file
                $img->save();
            }

            $data['filename'] = getUserStaticDomain() . $folderName .'/'. $safeName;

            SiteStatus::newImage();
        } else {
            $data['error'] = 'Error while uploading file';
        }
        return $data;
    }

    /**
     * ----------------------------------------
     * CreatorListener Delegate
     * ----------------------------------------
     */

    public function creatorFailed($errors)
    {
        return Redirect::to('/');
    }

    public function creatorSucceed($topic)
    {
        flash()->success('恭喜', lang('Operation succeeded.'));
        return Redirect::route('topics.show', array($topic->id));
    }
}
