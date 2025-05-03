<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = ['title', 'content', 'category_id', 'user_id', 'featured_image'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
