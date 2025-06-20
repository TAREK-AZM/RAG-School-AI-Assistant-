<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationLine extends Model
{
    use HasFactory;
    protected $fillable = [
        'question',
        'answer',
        'conversation_id',
    ];

    public function conversation ():BelongsTo{
        return $this->belongTo(Conversation::class);
    }
}
