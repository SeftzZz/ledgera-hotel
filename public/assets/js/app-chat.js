/**
 * App Chat
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    (function () {

        const chatContactsBody = document.querySelector('.app-chat-contacts .sidebar-body'),
            chatHistoryBody = document.querySelector('.chat-history-body'),
            chatSidebarLeftBody = document.querySelector('.app-chat-sidebar-left .sidebar-body'),
            chatSidebarRightBody = document.querySelector('.app-chat-sidebar-right .sidebar-body'),
            formSendMessage = document.querySelector('.form-send-message'),
            messageInput = document.querySelector('.message-input'),
            searchInput = document.querySelector('.chat-search-input'),
            chatList = document.getElementById('chat-list'),
            contactList = document.getElementById('contact-list'),
            speechToText = $('.speech-to-text');

        let activeChatId = null;

        /*
        ========================
        PERFECT SCROLLBAR
        ========================
        */

        if (chatContactsBody) {
            new PerfectScrollbar(chatContactsBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        if (chatHistoryBody) {
            new PerfectScrollbar(chatHistoryBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        if (chatSidebarLeftBody) {
            new PerfectScrollbar(chatSidebarLeftBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        if (chatSidebarRightBody) {
            new PerfectScrollbar(chatSidebarRightBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        function scrollToBottom() {
            chatHistoryBody.scrollTo(0, chatHistoryBody.scrollHeight);
        }

        /*
        ========================
        LOAD CHAT LIST
        ========================
        */

        function loadChats() {

            fetch('/api/chat/admin', {
                headers: {
                    Authorization: 'Bearer ' + window.jwtToken
                }
            })
            .then(res => res.json())
            .then(res => {

                chatList.innerHTML = '';

                if (!res.data.length) {
                    document.querySelector('.chat-list-item-0').classList.remove('d-none');
                    return;
                }

                res.data.forEach(chat => {

                    const avatar = chat.avatar
                        ? `/uploads/avatars/${chat.avatar}`
                        : '/assets/img/avatars/2.png';

                    const time = formatTime(chat.last_time ?? chat.created_at);

                    const li = document.createElement('li');
                    li.className = 'chat-contact-list-item';

                    li.innerHTML = `
                        <a class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar avatar-online">
                                <img src="${avatar}" class="rounded-circle">
                            </div>

                            <div class="chat-contact-info flex-grow-1 ms-2">
                                <h6 class="chat-contact-name text-truncate m-0">${chat.name}</h6>
                                <p class="chat-contact-status text-muted text-truncate mb-0">
                                    ${chat.last_message ?? 'Start conversation'}
                                </p>
                            </div>

                            <small class="text-muted mb-auto">${time}</small>
                        </a>
                    `;

                    li.addEventListener('click', function () {

                        document.querySelectorAll('.chat-contact-list-item')
                            .forEach(el => el.classList.remove('active'));

                        li.classList.add('active');

                        openChat(chat.id);

                    });

                    chatList.appendChild(li);

                });

            });

        }

        /*
        ========================
        LOAD CONTACTS
        ========================
        */

        function loadContacts() {

            fetch('/api/customers', {
                headers: {
                    Authorization: 'Bearer ' + window.jwtToken
                },
            })
            .then(res => res.json())
            .then(res => {

                const empty = document.querySelector('.contact-list-item-0');

                document.querySelectorAll('#contact-list .contact-item')
                    .forEach(el => el.remove());

                if (!res.data.length) {
                    empty.classList.remove('d-none');
                    return;
                }

                empty.classList.add('d-none');

                res.data.forEach(user => {

                    const avatar = user.avatar
                        ? `/uploads/avatars/${user.avatar}`
                        : '/assets/img/avatars/4.png';

                    const li = document.createElement('li');
                    li.className = 'chat-contact-list-item contact-item';

                    li.innerHTML = `
                        <a class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar avatar-offline">
                                <img src="${avatar}" class="rounded-circle">
                            </div>

                            <div class="chat-contact-info flex-grow-1 ms-2">
                                <h6 class="chat-contact-name text-truncate m-0">${user.name}</h6>
                                <p class="chat-contact-status text-muted text-truncate mb-0">
                                    ${user.email ?? ''}
                                </p>
                            </div>
                        </a>
                    `;

                    li.addEventListener('click', function () {

                        createChat(branchId, user.id);

                    });

                    contactList.appendChild(li);

                });

            });

        }

        /*
        ========================
        OPEN CHAT
        ========================
        */

        function openChat(chatId) {

            activeChatId = chatId;

            fetch('/api/chat/messages/' + chatId, {
                headers: {
                    Authorization: 'Bearer ' + window.jwtToken
                }
            })
            .then(res => res.json())
            .then(res => {

                const ul = document.getElementById('chat-history');
                ul.innerHTML = '';

                res.data.forEach(msg => {
                    renderMessage(msg);
                });

                scrollToBottom();

            });

        }

        /*
        ========================
        RENDER MESSAGE
        ========================
        */

        function renderMessage(msg) {

          const ul = document.querySelector('.chat-history');
          const li = document.createElement('li');

          if (msg.sender_type === 'user') {

            li.className = 'chat-message chat-message-right';

            li.innerHTML = `
              <div class="d-flex overflow-hidden">
                <div class="chat-message-wrapper flex-grow-1">
                  <div class="chat-message-text">
                    <p class="mb-0">${msg.message}</p>
                  </div>
                  <div class="text-end text-muted mt-1">
                    <small>${formatTime(msg.created_at)}</small>
                  </div>
                </div>
              </div>
            `;

          } else {

            li.className = 'chat-message';

            li.innerHTML = `
              <div class="d-flex overflow-hidden">
                <div class="chat-message-wrapper flex-grow-1">
                  <div class="chat-message-text">
                    <p class="mb-0">${msg.message}</p>
                  </div>
                  <div class="text-muted mt-1">
                    <small>${formatTime(msg.created_at)}</small>
                  </div>
                </div>
              </div>
            `;

          }

          ul.appendChild(li);

        }

        /*
        ========================
        SEND MESSAGE
        ========================
        */

        formSendMessage.addEventListener('submit', function (e) {

            e.preventDefault();

            if (!messageInput.value || !activeChatId) return;

            const message = messageInput.value;

            fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: 'Bearer ' + window.jwtToken
                },
                body: JSON.stringify({
                    chat_id: activeChatId,
                    message: message,
                    sender_type: 'admin'
                })
            })
            .then(res => res.json())
            .then(() => {

                renderMessage({
                    sender_type: 'admin',
                    message: message,
                    created_at: new Date()
                });

                messageInput.value = '';

                scrollToBottom();

            });

        });

        /*
        ========================
        SEARCH CHAT
        ========================
        */

        if (searchInput) {

            searchInput.addEventListener('keyup', function () {

                const searchValue = this.value.toLowerCase();

                document.querySelectorAll('#chat-list li').forEach(li => {

                    const text = li.textContent.toLowerCase();

                    if (text.indexOf(searchValue) > -1) {
                        li.classList.remove('d-none');
                    } else {
                        li.classList.add('d-none');
                    }

                });

            });

        }

        /*
        ========================
        CREATE CHAT
        ========================
        */

        function createChat(branchId, userId) {

            fetch('/api/chat/create-admin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: 'Bearer ' + window.jwtToken
                },
                body: JSON.stringify({
                    branch_id: branchId,
                    user_id: userId,
                })
            })
            .then(res => res.json())
            .then(res => {

                openChat(res.data.chat_id);
                loadChats();

            });

        }

        /*
        ========================
        SIDEBAR OVERLAY FIX
        ========================
        */

        const chatHistoryHeaderMenu = document.querySelector(".chat-history-header [data-target='#app-chat-contacts']");
        const chatSidebarLeftClose = document.querySelector('.app-chat-sidebar-left .close-sidebar');

        if (chatHistoryHeaderMenu && chatSidebarLeftClose) {

            chatHistoryHeaderMenu.addEventListener('click', function () {
                chatSidebarLeftClose.removeAttribute('data-overlay');
            });

        }

        /*
        ========================
        SPEECH TO TEXT
        ========================
        */

        if (speechToText.length) {

            var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;

            if (SpeechRecognition) {

                var recognition = new SpeechRecognition(),
                    listening = false;

                speechToText.on('click', function () {

                    const $this = $(this);

                    recognition.onspeechstart = function () {
                        listening = true;
                    };

                    if (!listening) {
                        recognition.start();
                    }

                    recognition.onerror = function () {
                        listening = false;
                    };

                    recognition.onresult = function (event) {
                        $this.closest('.form-send-message')
                            .find('.message-input')
                            .val(event.results[0][0].transcript);
                    };

                    recognition.onspeechend = function () {
                        listening = false;
                        recognition.stop();
                    };

                });

            }

        }

        /*
        ========================
        TIME FORMAT
        ========================
        */

        function formatTime(date) {

          const d = new Date(date);

          return d.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
          });

        }

        /*
        ========================
        INIT
        ========================
        */

        loadChats();
        loadContacts();
    })();
});