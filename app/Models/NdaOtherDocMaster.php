<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NdaOtherDocMaster extends Model
{
    use SoftDeletes;

    protected $table = 'nda_other_doc_masters';

    protected $fillable = [
        'title',
        'doc_image',
        'staff_id',
        'type',
        'status',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Staff relation
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}