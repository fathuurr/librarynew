<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'stock'
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isCurrentlyBorrowed()
    {
        return $this->borrowings()
            ->where('is_returned', false)
            ->exists();
    }

    
    public function hasBeenBorrowed()
    {
        return $this->borrowings()->exists();
    }
}
