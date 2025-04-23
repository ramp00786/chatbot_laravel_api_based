@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Chatbot System Documentation</h2>
                </div>
                
                <div class="card-body">
                    <section class="mb-5">
                        <h3 class="border-bottom pb-2">Features</h3>
                        <div class="row mt-4">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5><i class="fas fa-comments me-2"></i> User Chat</h5>
                                        <ul class="mt-3">
                                            <li>Pre-chat registration</li>
                                            <li>Question/answer flow</li>
                                            <li>Session persistence</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5><i class="fas fa-eye me-2"></i> Admin Monitoring</h5>
                                        <ul class="mt-3">
                                            <li>View all conversations</li>
                                            <li>Search and filter</li>
                                            <li>Detailed chat history</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5><i class="fas fa-key me-2"></i> API Management</h5>
                                        <ul class="mt-3">
                                            <li>Key-based authentication</li>
                                            <li>Session tracking</li>
                                            <li>Webhook support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <section class="mb-5">
                        <h3 class="border-bottom pb-2">Getting Started</h3>
                        <div class="mt-4">
                            <h5>Embedding the Chatbot</h5>
                            <pre class="bg-light p-3 rounded"><code>&lt;div id="chatbot-container" data-key="YOUR_API_KEY"&gt;&lt;/div&gt;
&lt;script src="/chatbot.js"&gt;&lt;/script&gt;</code></pre>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Replace YOUR_API_KEY with a valid key from your admin panel
                            </div>
                            
                            <h5 class="mt-4">Admin Access</h5>
                            <p>Access the admin panel at <code>/admin</code> after logging in with admin credentials.</p>
                            
                            <div class="text-center my-4">
                                <img src="{{ asset('images/dashboard.png') }}" alt="Admin Dashboard" class="img-fluid border rounded">
                                <p class="text-muted mt-2">Admin Dashboard Overview</p>
                            </div>
                        </div>
                    </section>
                    
                    <section class="mb-4">
                        <h3 class="border-bottom pb-2">System Screenshots</h3>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/session-start-form.png') }}" class="card-img-top" alt="Chat Start">
                                    <div class="card-body">
                                        <h5>User Registration</h5>
                                        <p>Users provide details before starting chat</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/first-screen.png') }}" class="card-img-top" alt="Chat Interface">
                                    <div class="card-body">
                                        <h5>Chat Interface</h5>
                                        <p>Main chat screen with question options</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/question-with-input.png') }}" class="card-img-top" alt="Input Field">
                                    <div class="card-body">
                                        <h5>User Input</h5>
                                        <p>Free-form text input when enabled</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/list-all-questions.png') }}" class="card-img-top" alt="Question Management">
                                    <div class="card-body">
                                        <h5>Question Management</h5>
                                        <p>Admin view of all questions</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/add-question.png') }}" class="card-img-top" alt="Add Question">
                                    <div class="card-body">
                                        <h5>Add New Question</h5>
                                        <p>Admin interface for creating questions</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ asset('images/session-chat-history.png') }}" class="card-img-top" alt="Chat History">
                                    <div class="card-body">
                                        <h5>Chat History</h5>
                                        <p>Detailed view of conversation</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection