<?php

namespace Wikko\Googlesheet\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GoogleSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_sheet_client',
        'google_sheet_secret',

    ];


}