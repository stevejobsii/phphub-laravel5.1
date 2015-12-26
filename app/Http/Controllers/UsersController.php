<?php
namespace App\Http\Controllers;

use App\Topic;
use App\User;
use App\Reply;
use Auth;
use Input;
use Redirect;
use App\Http\Requests\AvatarRequest;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Requests\PasswordresetRequest;
use Hash;

class UsersController extends Controller
{
    public function __construct(Topic $topic)
    {
        $this->middleware('auth', ['only' => ['edit', 'update', 'destroy']]);
        $this->topic = $topic;
    }
    

    public function index()
    {
        $users = User::recent()->take(48)->get();

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $topics = Topic::whose($user->id)->recent()->limit(10)->get();
        $replies = Reply::whose($user->id)->recent()->limit(10)->get();

        return view('users.show', compact('user', 'topics', 'replies'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($user->id);

        return view('users.edit', compact('user', 'topics', 'replies'));
    }

    public function update($id)
    {
        $user = User::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($user->id);
        $data = Input::only('real_name', 'city', 'company', 'weibo_account', 'personal_website', 'signature', 'introduction');
        app('App\Http\Requests\UserUpdateForm')->validate($data);

        $user->update($data);

        flash()->success('Good jobs!', lang('Operation succeeded.'));

        return Redirect::route('users.show', $id);
    }

    public function destroy($id)
    {
        $this->authorOrAdminPermissioinRequire($topic->user_id);
    }

    public function replies($id)
    {
        $user = User::findOrFail($id);
        $replies = Reply::whose($user->id)->recent()->paginate(15);

        return view('users.replies', compact('user', 'replies'));
    }

    public function topics($id)
    {
        $user = User::findOrFail($id);
        $topics = Topic::whose($user->id)->recent()->paginate(15);

        return view('users.topics', compact('user', 'topics'));
    }

    public function favorites($id)
    {
        $user = User::findOrFail($id);
        $topics = $user->favoriteTopics()->paginate(15);

        return view('users.favorites', compact('user', 'topics'));
    }

    public function accessTokens($id)
    {
        if(!Auth::check() || Auth::id() != $id){
            return Redirect::route('users.show', $id);
        }
        $user = User::findOrFail($id);
        $sessions = OAuthSession::where([
            'owner_type' => 'user',
            'owner_id' => Auth::id(),
            ])
            ->with('token')
            ->lists('id') ?: [];
    
        $tokens = AccessToken::whereIn('session_id', $sessions)->get();

        return view('users.access_tokens', compact('user', 'tokens'));
    }

    public function revokeAccessToken($token)
    {
        $access_token = AccessToken::with('session')->find($token);
        
        if(!$access_token || !Auth::check() || $access_token->session->owner_id != Auth::id()){
            Flash::error(lang('Revoke Failed'));
        }else{
            $access_token->delete();
            Flash::success(lang('Revoke success'));
        }

        return Redirect::route('users.access_tokens', Auth::id());
    }

    public function blocking($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = (!$user->is_banned);
        $user->save();

        return Redirect::route('users.show', $id);
    }

    public function avatarupdate(AvatarRequest $request)
    {
        Image::make($request->file('avatar'))
            ->resize(196, 180)
            ->encode('jpg')
            ->save(base_path() . '/public/images/avatar/avatar' . Auth::id() . '.jpg');
        Image::make($request->file('avatar'))
            ->resize(40, 40)
            ->encode('jpg')
            ->save(base_path() . '/public/images/avatar/30avatar' . Auth::id() . '.jpg');
        $user = Auth::user();
        $user->avatar = '/images/avatar/avatar' . Auth::id() . '.jpg';
        $user->avatar_30 = '/images/avatar/30avatar' . Auth::id() . '.jpg';
        $user->save();
        return Redirect::back();
    }

    protected function resetPassword(PasswordresetRequest $request)
    {
        $user = Auth::user();
      
        if(Hash::check($request->only('old_password')['old_password'], $user->password)){  
        
            $password = $request->only('password')['password'];

            $password_confirmation = $request->only('password_confirmation')['password_confirmation'];

            if(!($password == $password_confirmation)){
                   flash()->info('当前输入新密码与错密码不一致!', '请重新输入');
                   return Redirect::back();
            } else {

            $user->password = Hash::make($request->only('password')['password']);

            $user->save();

            flash()->success('密码修改成功!', 'Have a good time!');

            return Redirect::back();}

        } else {
            
            flash()->error('输入当前密码输入错误!', '请重新输入');

            return Redirect::back();
        }
    }
}
