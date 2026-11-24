@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')


<div class="page-wrapper chat-page-wrapper">
    <div class="content">

        <!-- sidebar group -->
        <div class="sidebar-group left-sidebar chat_sidebar">

            <!-- Chats sidebar -->
            <div id="chats" class="left-sidebar-wrap sidebar active slimscroll">

                <div class="slimscroll-active-sidebar">

                    <!-- Left Chat Title -->
                    <div class="left-chat-title all-chats">
                        <div class="setting-title-head">
                            <h4> All Chats</h4>
                        </div>
                        <div class="add-section">
                            <div class="user-chat-search">
                                <form>
                                    <input type="text" name="chat-search" placeholder="Search" class="form-control">
                                    <span class="form-control-feedback"><i class="bx bx-search"></i></span>
                                </form>
                            </div>
                            <!-- /Chat Search -->
                        </div>
                    </div>
                    <!-- /Left Chat Title -->


                    <div class="sidebar-body chat-body" id="chatsidebar">

                        <!-- Left Chat Title -->
                        <div class="d-flex justify-content-between align-items-center ps-0 pe-0">
                            <div class="fav-title pin-chat">
                                <h6>Recent Chat</h6>
                            </div>
                        </div>
                        <!-- /Left Chat Title -->

                        <ul class="user-list">
                            <li class="user-list-item">
                                <a href="javascript:void(0);">
                                    <div class="avatar avatar-online">
                                        <img src="assets/img/avatar/avatar-2.jpg" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body">
                                        <div>
                                            <h5>Storename 1</h5>
                                            <p>Hello, How are you?</p>
                                        </div>
                                        <div class="last-chat-time">
                                            <small class="text-muted">10:20 PM</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="user-list-item">
                                <a href="javascript:void(0);">
                                    <div class="avatar avatar-online">
                                        <img src="assets/img/avatar/avatar-1.jpg" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body">
                                        <div>
                                            <h5>Storename 1</h5>
                                            <p>Hello, How are you?</p>
                                        </div>
                                        <div class="last-chat-time">
                                            <small class="text-muted">10:20 PM</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="user-list-item">
                                <a href="javascript:void(0);">
                                    <div class="avatar avatar-online">
                                        <img src="assets/img/avatar/avatar-3.jpg" class="rounded-circle" alt="image">
                                    </div>
                                    <div class="users-list-body">
                                        <div>
                                            <h5>Storename 1</h5>
                                            <p>Hello, How are you?</p>
                                        </div>
                                        <div class="last-chat-time">
                                            <small class="text-muted">10:20 PM</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
            <!-- / Chats sidebar -->
        </div>
        <!-- /Sidebar group -->

        <!-- Chat -->
        <div class="chat chat-messages" id="middle">
            <div class="slimscroll">
                <div class="chat-header">
                    <div class="user-details">
                        <div class="d-lg-none">
                            <ul class="list-inline mt-2 me-2">
                                <li class="list-inline-item">
                                    <a class="text-muted px-0 left_sides" href="#" data-chat="open">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <figure class="avatar ms-1">
                            <img src="assets/img/avatar/avatar-2.jpg" class="rounded-circle" alt="image">
                        </figure>
                        <div class="mt-1">
                            <h5>Storename 1</h5>
                            <small class="last-seen">
                                Last Seen at 07:15 PM
                            </small>
                        </div>
                    </div>
                </div>
                <div class="chat-body">
                    <div class="messages">
                        <div class="chats">
                            <div class="chat-avatar">
                                <img src="assets/img/avatar/avatar-2.jpg" class="rounded-circle dreams_chat"
                                    alt="image">
                            </div>
                            <div class="chat-content">
                                <div class="chat-profile-name">
                                    <h6>Storename 1<span>8:16 PM</span></h6>
                                </div>
                                <div class="message-content">Tranfer 12unit of Dell Laptop.</div>
                            </div>
                        </div>
                        <div class="chat-line">
                            <span class="chat-date">Today, July 24</span>
                        </div>
                        <div class="chats chats-right">
                            <div class="chat-content">
                                <div class="chat-profile-name text-end">
                                    <h6>Storename 2<span>8:16 PM</span></h6>
                                    <div class="chat-action-btns ms-3">
                                        <div class="chat-action-col">
                                            <a class="#" href="#" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="message-content ">Received succesfully !</div>
                            </div>
                            <div class="chat-avatar">
                                <img src="assets/img/avatar/avatar-2.jpg" class="rounded-circle dreams_chat"
                                    alt="image">
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <form>
                    <input type="text" class="form-control chat_form" placeholder="Type your message here...">
                    <div class="form-buttons">
                        <button class="btn send-btn" type="submit">
                            <i class="bx bx-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Chat -->



    </div>
</div>



@endsection