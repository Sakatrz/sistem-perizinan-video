<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAccess extends Model
{
    protected $fillable = ['access_request_id', 'user_id', 'video_id', 'starts_at', 'expires_at'];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function isActive(): bool
    {
        return now()->between($this->starts_at, $this->expires_at);
    }
}
