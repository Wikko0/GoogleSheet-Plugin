<?php

namespace Wikko\Googlesheet\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GoogleList extends Model
{
    use HasFactory;

    protected $table = 'google_sheet_lists';

    protected $fillable = [
        'uid',
        'list_name',
        'connection_name',
        'connection_type',
        'sheet_name',
        'google_sheet_id',
        'last_sync',
    ];

    public static function scopeSearch($query, $keyword)
    {
        // Keyword
        if (!empty(trim($keyword))) {
            $query = $query->where('list_name', 'like', '%'.$keyword.'%');
        }
    }

}