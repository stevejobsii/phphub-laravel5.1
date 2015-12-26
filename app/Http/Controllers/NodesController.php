<?php
namespace App\Http\Controllers;

use App\Topic;
use App\Node;
use App\Tip;
use Illuminate\Http\Request as urlRequest;

class NodesController extends Controller
{

    protected $topic;

    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function show($id, urlRequest $request)
    {
        $node = Node::findOrFail($id);
        $search = $request->query('q');
        
        $filter = $this->topic->present()->getTopicFilter();
        $topics = $this->topic->getNodeTopicsWithFilter($filter, $id)->search($search)->paginate(20);
        $this->setupLayout();

        return view('topics.index', compact('topics', 'node'));
    }
}
