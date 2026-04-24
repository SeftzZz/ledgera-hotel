<?php

namespace App\Controllers\Api;

use App\Models\ChatModel;
use App\Models\ChatMessageModel;
use Config\Database;

use App\Services\WsEmitter;

class Chat extends BaseApiController
{

    protected $chatModel;
    protected $messageModel;

    public function __construct()
    {
        $this->chatModel = new ChatModel();
        $this->messageModel = new ChatMessageModel();
    }

    /*
    =========================
    CREATE CHAT
    =========================
    POST /chat/create
    */

    public function create()
    {

        $data = $this->request->getJSON(true);

        $userId = $this->request->user->id;
        $branchId = $data['branch_id'] ?? null;

        if(!$branchId){
            return $this->error('branch_id required');
        }

        $chatId = $this->chatModel->insert([
            'user_id'=>$userId,
            'branch_id'=>$branchId
        ]);

        return $this->success([
            'chat_id'=>$chatId
        ], 'Chat created');

    }

    /*
    =========================
    LIST USER CHATS
    =========================
    GET /chat/{userId}
    */

    public function list($userId)
    {

        $db = Database::connect();

        $chats = $db->table('chats')
            ->where('user_id',$userId)
            ->where('branch_id', session('branch_id'))
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success($chats);

    }

    /*
    =========================
    CHAT MESSAGES
    =========================
    GET /chat/messages/{chatId}
    */

    public function messages($chatId)
    {

        $db = Database::connect();

        $messages = $db->table('chat_messages')
            ->where('branch_id', session('branch_id'))
            ->where('chat_id',$chatId)
            ->orderBy('id','ASC')
            ->get()
            ->getResultArray();

        return $this->success($messages);

    }

    /*
    =========================
    SEND MESSAGE
    =========================
    POST /chat/send
    */

    public function send()
    {

        $data = $this->request->getJSON(true);

        $userId = $this->request->user->id;

        if(empty($data['chat_id']) || empty($data['message'])){
            return $this->error('chat_id and message required');
        }

        $this->messageModel->insert([
            'chat_id'=>$data['chat_id'],
            'sender_type'=>$data['sender_type'],
            'message'=>$data['message']
        ]);

        $ws = new WsEmitter();

        $ws->emit([
            'type' => 'chat_message',
            'chat_id' => $data['chat_id'],
            'message' => $data['message'],
            'sender' => 'admin'
        ]);

        return $this->success([], 'Message sent');

    }

    public function adminChats()
    {

        $db = \Config\Database::connect();

        $rows = $db->table('chats c')
            ->select('
                c.id,
                c.branch_id,
                c.created_at,
                u.id as user_id,
                u.name,
                u.photo,

                (
                    SELECT message
                    FROM chat_messages
                    WHERE chat_id = c.id
                    ORDER BY id DESC
                    LIMIT 1
                ) as last_message,

                (
                    SELECT created_at
                    FROM chat_messages
                    WHERE chat_id = c.id
                    ORDER BY id DESC
                    LIMIT 1
                ) as last_time
            ')
            ->join('users u','u.id = c.user_id','left')
            ->where('branch_id', session('branch_id'))
            ->orderBy('c.id','DESC')
            ->get()
            ->getResultArray();

        return $this->success($rows);

    }

    public function adminCreate()
    {

        $data = $this->request->getJSON(true);

        $userId   = $data['user_id'] ?? null;
        $branchId = $data['branch_id'] ?? null;

        if (!$userId) {
            return $this->error('user_id required');
        }

        if (!$branchId) {
            return $this->error('branch_id required');
        }

        /*
        =========================
        CEK CHAT SUDAH ADA
        =========================
        */

        $existing = $this->chatModel
            ->where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->first();

        if ($existing) {

            return $this->success([
                'chat_id' => $existing['id']
            ], 'Chat already exists');

        }

        /*
        =========================
        CREATE CHAT BARU
        =========================
        */

        $chatId = $this->chatModel->insert([
            'user_id'   => $userId,
            'branch_id' => $branchId
        ]);

        return $this->success([
            'chat_id' => $chatId
        ], 'Chat created');

    }
}