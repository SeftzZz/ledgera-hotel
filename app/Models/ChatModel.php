<?php

namespace App\Models;

class ChatModel extends BaseModel
{
    protected $table = 'chats';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'branch_id'
    ];
}