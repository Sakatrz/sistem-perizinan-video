<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'description', 'file_path', 'mime_type'];

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

    public function accesses()
    {
        return $this->hasMany(VideoAccess::class);
    }
}
