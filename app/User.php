<?php namespace App;


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laracasts\Presenter\PresentableTrait;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements  AuthenticatableContract, CanResetPasswordContract
{
    // Using: $user->present()->anyMethodYourWant()
    use Authenticatable, CanResetPassword;
    use PresentableTrait;
    use EntrustUserTrait; 

    public  $presenter = '\App\good\Presenters\UserPresenter';

    // Enable hasRole( $name ), can( $permission ),
    //   and ability($roles, $permissions, $options)


    // Enable soft delete

    protected $dates = ['deleted_at'];

    protected $table      = 'users';
    protected $hidden     = ['github_id'];
    protected $guarded    = ['id', 'notifications', 'is_banned'];

    public function favoriteTopics()
    {
        return $this->belongsToMany('\App\Topic', 'favorites')->withTimestamps();
    }

    public function attentTopics()
    {
        return $this->belongsToMany('\App\Topic', 'attentions')->withTimestamps();
    }

    public function topics()
    {
        return $this->hasMany('\App\Topic');
    }

    public function replies()
    {
        return $this->hasMany('\App\Reply');
    }

    public function notifications()
    {
        return $this->hasMany('\App\Notification')->recent()->with('topic', 'fromUser')->paginate(20);
    }

    public function getByGithubId($id)
    {
        return $this->where('github_id', '=', $id)->first();
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * ----------------------------------------
     * UserInterface
     * ----------------------------------------
     */

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * ----------------------------------------
     * RemindableInterface
     * ----------------------------------------
     */

    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Cache github avatar to local
     * @author Xuan
     */
    public function cacheAvatar()
    {
        //Download Image
        $guzzle = new GuzzleHttp\Client();
        $response = $guzzle->get($this->image_url);

        //Get ext
        $content_type = explode('/', $response->getHeader('Content-Type'));
        $ext = array_pop($content_type);

        $avatar_name = $this->id . '_' . time() . '.' . $ext;
        $save_path = public_path('uploads/avatars/') . $avatar_name;

        //Save File
        $content = $response->getBody()->getContents();
        file_put_contents($save_path, $content);

        //Delete old file
        if ($this->avatar) {
            @unlink(public_path('uploads/avatars/') . $this->avatar);
        }

        //Save to database
        $this->avatar = $avatar_name;
        $this->save();
    }
}
