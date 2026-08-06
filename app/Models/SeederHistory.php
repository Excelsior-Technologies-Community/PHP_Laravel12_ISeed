<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeederHistory extends Model
{

    protected $fillable = [

        'table_name',
        'seeder_name',
        'records',
        'status'

    ];

}