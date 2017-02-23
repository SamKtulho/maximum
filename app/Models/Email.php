<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    const STATUS_NOT_VALID = 0;
    const STATUS_VALID = 1;
    
    private $stopWords = [
        'domain@',
    ];

    /**
     * @return array
     */
    public function getStopWords()
    {
        return $this->stopWords;
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }
}
