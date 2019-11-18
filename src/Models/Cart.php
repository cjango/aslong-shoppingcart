<?php

namespace AsLong\Cart\Models;

class Cart extends Model
{

    protected $primaryKey = 'row_id';

    /**
     * @var array
     */
    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(config('cart.user_model'));
    }

    function setContentAttribute($content)
    {
        $this->attributes['content'] = serialize($content);
    }

    function getContentAttribute($content)
    {
        return unserialize($content);
    }

}
