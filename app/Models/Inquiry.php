<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = ['property_id', 'name', 'phone', 'email', 'required_area', 'activity', 'message', 'locale', 'status'];
}
