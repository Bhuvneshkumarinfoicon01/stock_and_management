@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper chat-page-wrapper">
    <div class="content">
        <!-- Sidebar for User List -->
        <div class="sidebar-group left-sidebar chat_sidebar">
            <div id="chats" class="left-sidebar-wrap sidebar active slimscroll">
                <div class="slimscroll-active-sidebar">
                    <div class="left-chat-title all-chats">
                        <h4>All Chats</h4>
                        <div class="user-chat-search">
                            <form>
                                <input
                                    type="text"
                                    name="chat-search"
                                    id="chat-search"
                                    placeholder="Search"
                                    class="form-control"
                                    autocomplete="off"
                                />
                                <span class="form-control-feedback">
                                    <i class="bx bx-search"></i>
                                </span>
                            </form>
                        </div>
                    </div>
                    <ul class="user-list">
                        @foreach ($users as $user)
                            <li class="user-item" data-user-id="{{ $user->id }}" data-user-image="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin\assets\img\avatar\default-avatar.png') }}">
                                <div class="user-details d-flex align-items-center my-2">
                                    <figure class="avatar me-3">
                                        <img
                                        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin\assets\img\avatar\default-avatar.png') }}"
                                            class="rounded-circle"
                                            alt="{{ $user->name }}"
                                            width="40"
                                            height="40"
                                        />
                                    </figure>
                                    <div class="user-info">
                                        <h5 class="user-name" style="cursor: pointer;">{{ $user->name }}</h5>
                                        <span>({{$user->role->name}})</span>
                                        <span class="unread-dot" style="display: none; color: red; font-size: 20px; padding-left: 20px;">●</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Chat Section -->
        <div class="chat chat-messages" id="middle">
            <div class="slimscroll">
                <div class="chat-header fixed-header">
                    <div class="user-details d-flex align-items-center">
                        <figure class="avatar me-3">
                            <img
                                src="\admin\assets\img\avatar\default-avatar.png"
                                class="rounded-circle chat-header-avatar"
                                alt="User Image"
                                width="40"
                                height="40"
                            />
                        </figure>
                        <div>
                            <h5 class="chat-header-name">Select a user to start conversation</h5>
                        </div>
                    </div>
                </div>
                <div class="chat-body">
                    <div class="messages">
                        <div class="no-messages text-center">
                            <p>Select a user to start conversation</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <form id="sendMessageForm">
                    <input
                        type="text"
                        id="messageInput"
                        class="form-control chat_form"
                        placeholder="Type your message here..."
                        disabled
                    />
                    <div class="form-buttons">
                        <button class="btn send-btn" type="submit" disabled>
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
<script>
    window.NOTIFICATION_PORT = '{{ env('NOTIFICATION_PORT', 3050) }}';
    window.NOTIFICATION_SERVER_URL = '{{ config("services.notification_url") }}';
    window.USER_ID = {{ auth()->id() }};
</script>
<script>
        const socketUrl = window.NOTIFICATION_SERVER_URL || `http://localhost:${window.NOTIFICATION_PORT}`;
        const socket = io(socketUrl, {
        // const socket = io(`https://auction.itexpertiseindia.com:8082`, {
        query: {
            user_id: window.USER_ID, // Pass the logged-in user's ID dynamically
        },
    }); // Connect to the Socket.IO server

    $(document).ready(function () {
        // Show the placeholder message by default
        $('.chat-body .no-messages').show();

        // Fetch unread messages
        function fetchUnreadMessages() {
            $.ajax({
                url: "{{ route('admin.fetchUnreadMessages') }}",
                type: 'GET',
                success: function (response) {
                    const unreadMessages = response.unreadMessages;

                    // Clear all red dots
                    $('.unread-dot').hide();

                    // Show red dots for users with unread messages
                    unreadMessages.forEach((message) => {
                        const userItem = $(`.user-item[data-user-id="${message.sender_id}"]`);
                        userItem.find('.unread-dot').show();
                    });
                },
                error: function (xhr) {
                    console.error('Error fetching unread messages:', xhr.responseText);
                },
            });
        }

        // Fetch unread messages on page load
        fetchUnreadMessages();

        // Poll for unread messages every 5 seconds
        setInterval(fetchUnreadMessages, 5000);

        // Handle user click to load messages
        $(document).on('click', '.user-item', function () {
            const userId = $(this).data('user-id'); // Get the clicked user's ID
            const currentUserId = {{ auth()->id() }}; // Get the logged-in user's ID

            // Generate a unique room name based on the two user IDs
            const roomName = currentUserId < userId ? `room-${currentUserId}-${userId}` : `room-${userId}-${currentUserId}`;

            // Emit an event to join the room
            socket.emit('joinRoom', { room: roomName });

            // Update the chat header with the selected user's name and image
            const userName = $(this).find('.user-name').text();
            const userImage = $(this).data('user-image');
            $('.chat-header-name').text(userName);
            $('.chat-header-avatar').attr('src', userImage);

            // Clear the chat body and enable the input field
            $('.chat-body .messages').html('');
            $('#messageInput').prop('disabled', false);
            $('.send-btn').prop('disabled', false);

            // Fetch previous messages for the selected user
            fetchMessages(userId);

            // Store the selected user ID for sending messages
            $('#middle').data('chat-user-id', userId);

            // Mark messages as read when opening the chat
            $.ajax({
                url: "{{ route('admin.markMessagesAsRead') }}",
                type: 'POST',
                data: {
                    user_id: userId,
                    _token: "{{ csrf_token() }}",
                },
                success: function () {
                    console.log('Messages marked as read for user:', userId);

                    // Hide the red dot for this user
                    $(`.user-item[data-user-id="${userId}"]`).find('.unread-dot').hide();
                },
                error: function (xhr) {
                    console.error('Error marking messages as read:', xhr.responseText);
                },
            });
        });

        // Function to fetch messages for a user
        function fetchMessages(userId) {
            $.ajax({
                url: "{{ route('admin.fetchMessages') }}", // Use the universal fetchMessages route
                type: 'GET', // Use GET to match the route
                data: {
                    user_id: userId, // Pass the user ID as a query parameter
                },
                success: function (response) {
                    console.log('Messages fetched:', response.messages);
                    $('.chat-body .messages').html(''); // Clear existing messages
                    response.messages.forEach((message) => {
                        appendMessage({
                            sender_id: message.sender_id,
                            message: message.content,
                            time: new Date(message.created_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true }),
                        });
                    });
                    // Ensure scroll after DOM update
                    setTimeout(scrollToBottom, 100);
                },
                error: function (xhr) {
                    console.error('Error fetching messages:', xhr.responseText);
                },
            });
        }

        // Function to append a message to the chat body
        function appendMessage(data) {
            const isSender = parseInt(data.sender_id) === parseInt({{ auth()->id() }});
            const chatAlignment = isSender ? 'chats chats-right' : 'chats';
            const chatName = isSender ? 'You' : $('.chat-header .chat-header-name').text();
            const chatImage = isSender
                ? "{{auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('admin/assets/img/avatar/default-avatar.png')}}"
                : $('.chat-header .chat-header-avatar').attr('src');

            const chatHTML = `
                <div class="${chatAlignment}">
                    <div class="chat-avatar">
                        <img
                            src="${chatImage}"
                            class="rounded-circle dreams_chat"
                            alt="image"
                        />
                    </div>
                    <div class="chat-content">
                        <div class="chat-profile-name">
                            <h6>${chatName}<span>${data.time}</span></h6>
                        </div>
                        <div class="message-content">
                            ${data.message}
                        </div>
                    </div>
                </div>
            `;
            $('.chat-body .messages').append(chatHTML);

            console.log('data',data);
        }

        // Handle sending a message
        $('#sendMessageForm').on('submit', function (e) {
            e.preventDefault();

            const message = $('#messageInput').val();
            const userId = $('#middle').data('chat-user-id');
            const currentUserId = {{ auth()->id() }};

            // Generate the room name
            const roomName = currentUserId < userId ? `room-${currentUserId}-${userId}` : `room-${userId}-${currentUserId}`;

            if (message.trim() !== '') {
                console.log('Sending message:', { to: userId, message: message, sender_id: currentUserId, room: roomName });
                

                // Emit the message via Socket.IO
                socket.emit('sendMessage', {
                    to: userId,
                    message: message,
                    sender_id: currentUserId,
                    room: roomName,
                });

                // Append the sent message to the chat body immediately
                appendMessage({
                    sender_id: currentUserId,
                    message: message,
                    time: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true }),

                });

                // Clear the input field
                $('#messageInput').val('');

                // Store the message in the database
                $.ajax({
                    url: "{{ route('admin.messages.store') }}",
                    type: 'POST',
                    data: {
                        receiver_id: userId,
                        message: message,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        console.log('Message stored:', response.message);
                    },
                    error: function (xhr) {
                        console.error('Error storing message:', xhr.responseText);
                    },
                });
            }
        });

        // Listen for incoming messages
        socket.on('receiveMessage', (data) => {
            console.log('Message received:', data);

            const currentUserId = {{ auth()->id() }}; // Logged-in user's ID
            const selectedUserId = $('#middle').data('chat-user-id'); // Currently selected user's ID

            // Check if the message is relevant to the current chat
            if (
                (data.sender_id === selectedUserId && data.receiver_id === currentUserId) || // Sent by the selected user to the logged-in user
                (data.sender_id === currentUserId && data.receiver_id === selectedUserId)    // Sent by the logged-in user to the selected user
            ) {
                // Skip appending the message if it was sent by the logged-in user
                if (data.sender_id === currentUserId) {
                    console.log('Message ignored: Already appended for the sender.');
                    return;
                }

                // Append the message to the chat body
                appendMessage({
                    sender_id: data.sender_id,
                    message: data.message,
                    time: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true }),


                });

                // Scroll to the bottom after receiving the message
                scrollToBottom();
            } else {
                console.log('Message ignored: Not relevant to the current chat.');
            }
        });

        // Search functionality
        $('#chat-search').on('keyup', function () {
            var search = $(this).val().toLowerCase();
            $('.user-list .user-item').each(function () {
                var userName = $(this).find('.user-name').text().toLowerCase();
                var userRole = $(this).find('span').first().text().toLowerCase();
                if (userName.includes(search) || userRole.includes(search)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });

    // Function to scroll to the bottom of the chat
    function scrollToBottom() {
        const chatBody = $('.chat-body');
        console.log('scrollToBottom called');
        if (chatBody.length === 0) {
            console.warn('No .chat-body element found!');
            return;
        }
        console.log('.chat-body found:', chatBody);
        console.log('Current scrollTop:', chatBody.scrollTop());
        console.log('ScrollHeight:', chatBody[0].scrollHeight);
        chatBody.scrollTop(chatBody[0].scrollHeight);
        // Check if scrollTop was set
        setTimeout(() => {
            console.log('After scroll, scrollTop:', chatBody.scrollTop());
        }, 100);
    }
</script>
@endsection
