<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{

    protected $fillable = [
        'body',
        'user_id',
        'topic_id',
        'body_original',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($topic) {
            SiteStatus::newReply();
        });
    }

    public function votes()
    {
        return $this->morphMany('\App\Vote', 'votable');
    }

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function topic()
    {
        return $this->belongsTo('\App\Topic');
    }

    public function scopeWhose($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id)->with('topic');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
