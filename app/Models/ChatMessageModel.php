<?php

namespace App\Models;

class ChatMessageModel extends BaseModel
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'chat_id',
        'sender_type',
        'message'
    ];
}