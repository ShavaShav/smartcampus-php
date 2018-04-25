<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'title', 
    	'time', 
    	'location', 
    	'link', 
    	'body', 
    	'author_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['author_id'];

    /**
     * Get the user that posted the event
     */
    public function author()
    {
        // Return minimal user details
        return $this->belongsTo('App\User')
                    ->select(array('id', 'username', 'email'));
    }
}
