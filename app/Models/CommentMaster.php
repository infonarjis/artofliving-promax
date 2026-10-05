<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentMaster extends Model
{
    use SoftDeletes;

    // Specify the table name since it doesn't follow Laravel's pluralization convention
    protected $table = 'comment_master';

    // Primary key
    protected $primaryKey = 'id';

    // Auto-incrementing ID
    public $incrementing = true;

    // Data type of the primary key
    protected $keyType = 'int';

    // Timestamps (already handled in DB, but Laravel can manage them too)
    public $timestamps = true;

    // Fields that can be mass-assigned
    protected $fillable = [
        'member_id',
        'posted_user_type',
        'posted_by',
        'comment',
        'next_followup_date',
        'follow_up_status'
    ];

    // Casts for fields
    protected $casts = [
        'member_id' => 'integer',
        'posted_by' => 'integer',
        'next_followup_date' => 'datetime:Y-m-d H:i:s',
        'follow_up_status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation to Register (member)
    public function register()
    {
        return $this->belongsTo(Register::class, 'member_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'posted_by', 'id');
    }
}
