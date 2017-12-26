<?php
namespace App;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = ['created_at'];

    public function user() {
        return $this->belongsTo('App\User', 'cid', 'cid');
    }

    public function setUpdatedAtAttribute($value) {
        // to Disable updated_at
    }
}