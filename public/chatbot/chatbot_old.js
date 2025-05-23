// chatbot.js
class ChatbotWidget {
    constructor(apiKey) {
        this.API_URL = 'http://127.0.0.1:8000';
        this.apiKey = apiKey;
        this.sessionId = localStorage.getItem('chatbot_session_id') || null;
        this.userData = JSON.parse(localStorage.getItem('chatbot_user_data')) || null;
        this.currentQuestions = null;
        this.questionHistory = []; // Track question navigation
        this.isMinimized = false;
        this.pendingNextQuestions = null;
        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
        this.bindUnloadEvents();
        // Check if we have a session and user data
        if (this.sessionId && this.userData) {
            this.showChatInterface();
        }
    }

    bindUnloadEvents() {
        const sendLogoutBeacon = () => {
            if (this.sessionId) {
                const data = new Blob([JSON.stringify({
                    session_id: this.sessionId,
                    api_key: this.apiKey
                })], {type: 'application/json'});
                navigator.sendBeacon(this.API_URL+'/api/chat/sessions/end', data);
            }
        };

        window.addEventListener('beforeunload', sendLogoutBeacon);
        window.addEventListener('pagehide', sendLogoutBeacon);
        window.addEventListener('unload', sendLogoutBeacon);
    }


    createWidget() {
        this.container = document.createElement('div');
        this.container.id = 'chatbot-widget-container';
        this.container.className = 'bg-white d-flex flex-column';
        this.container.innerHTML = `
            <div class="chatbot-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0">Chat Assistant</h5>
                <div>
                    <button id="chatbot-restart" class="btn btn-sm btn-outline-light me-2" style="display: none;">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button id="chatbot-minimize" class="btn btn-sm btn-outline-light me-2">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button id="chatbot-close" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="chatbot-body flex-grow-1 overflow-auto">
                <div id="registration-form">
                    <div class="p-3">
                        <p class="mb-3">Please provide your details to start chatting:</p>
                        <div class="mb-3">
                            <input type="text" id="user-name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" id="user-email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" id="user-mobile" class="form-control" placeholder="Your Mobile (optional)">
                        </div>
                        <button id="start-chat-btn" class="btn btn-primary w-100">
                            <span id="start-chat-text">Start Chat</span>
                            <span id="start-chat-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div id="chat-messages" class="chatbot-messages" style="display: none;"></div>
                <div id="quick-questions" class="p-3" style="display: none;"></div>
            </div>
            <div id="chatbot-footer" class="p-3 border-top" style="display: none;">
                <div class="input-group">
                    <input type="text" id="user-input" class="form-control" placeholder="Type your message..." style="display: none;">
                    <button id="send-btn" class="btn btn-primary" style="display: none;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(this.container);
        
        // Inject required CSS
        this.injectStyles();
        // Load Font Awesome if not already loaded
        this.loadFontAwesome();
    }

    showChatInterface() {
        // Show chat interface
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('chat-messages').style.display = 'block';
        document.getElementById('quick-questions').style.display = 'block';
        document.getElementById('chatbot-footer').style.display = 'block';
        document.getElementById('chatbot-restart').style.display = 'inline-block';
        
        // Add welcome message if no messages exist
        if (document.getElementById('chat-messages').children.length === 0) {
            this.addMessage(`Welcome back, ${this.userData.name}! How can I help you today?`, 'bot');
        }
        
        this.loadInitialQuestions();
    }

    injectStyles() {
        const style = document.createElement('style');
        style.textContent = `
            #chatbot-widget-container {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 350px;
                max-width: 90vw;
                max-height: 70vh;
                z-index: 1050;
                transition: all 0.3s ease;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                border-radius: 0.5rem;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            
            #chatbot-widget-container.minimized {
                height: 60px !important;
                max-height: 60px;
            }
            
            .chatbot-messages {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
                background-color: #f8f9fa;
            }
            
            .message {
                max-width: 80%;
                margin-bottom: 0.75rem;
                padding: 0.5rem 0.75rem;
                border-radius: 0.75rem;
                word-wrap: break-word;
            }
            
            .bot-message {
                background-color: white;
                border: 1px solid #dee2e6;
                margin-right: auto;
            }
            
            .user-message {
                background-color: #0d6efd;
                color: white;
                margin-left: auto;
            }
            
            .question-btn {
                display: block;
                width: 100%;
                text-align: left;
                margin-bottom: 0.5rem;
                white-space: normal;
            }
            
            .chatbot-header {
                cursor: pointer;
            }
            
            @media (max-width: 576px) {
                #chatbot-widget-container {
                    width: 90vw;
                    right: 5vw;
                    max-height: 80vh;
                }
            }
        `;
        document.head.appendChild(style);
    }

    loadFontAwesome() {
        if (!document.querySelector('link[href*="font-awesome"]')) {
            const faLink = document.createElement('link');
            faLink.rel = 'stylesheet';
            faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
            document.head.appendChild(faLink);
        }
    }

    bindEvents() {
        document.getElementById('start-chat-btn').addEventListener('click', () => this.startChat());
        document.getElementById('send-btn').addEventListener('click', () => this.handleUserMessage());
        document.getElementById('chatbot-minimize').addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMinimize();
        });
        document.getElementById('chatbot-close').addEventListener('click', (e) => {
            e.stopPropagation();
            this.closeWidget();
        });
        document.getElementById('chatbot-restart').addEventListener('click', (e) => {
            e.stopPropagation();
            this.logout();
        });
        document.querySelector('.chatbot-header').addEventListener('click', () => {
            if(this.isMinimized) this.toggleMinimize();
        });
        
        // Handle Enter key in input field
        document.getElementById('user-input')?.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') this.handleUserMessage();
        });
    }

    async startChat() {
        const nameInput = document.getElementById('user-name');
        const emailInput = document.getElementById('user-email');
        
        // Basic validation
        if(!nameInput.value.trim()) {
            this.showAlert('Please enter your name', 'danger');
            nameInput.focus();
            return;
        }
        
        if(!emailInput.value.trim() || !this.validateEmail(emailInput.value)) {
            this.showAlert('Please enter a valid email', 'danger');
            emailInput.focus();
            return;
        }

        // Show loading state
        document.getElementById('start-chat-text').classList.add('d-none');
        document.getElementById('start-chat-spinner').classList.remove('d-none');
        document.getElementById('start-chat-btn').disabled = true;

        this.userData = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            mobile: document.getElementById('user-mobile').value.trim()
        };

        try {
            const response = await fetch(this.API_URL+'/api/chat/sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': this.apiKey
                },
                body: JSON.stringify(this.userData)
            });

            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            this.sessionId = data.session_id;
            
            // Store in localStorage
            localStorage.setItem('chatbot_session_id', this.sessionId);
            localStorage.setItem('chatbot_user_data', JSON.stringify(this.userData));
            
            this.showChatInterface();
        } catch (error) {
            console.error('Error starting chat:', error);
            this.showAlert('Failed to start chat. Please try again.', 'danger');
        } finally {
            // Reset button state
            document.getElementById('start-chat-text').classList.remove('d-none');
            document.getElementById('start-chat-spinner').classList.add('d-none');
            document.getElementById('start-chat-btn').disabled = false;
        }
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const form = document.getElementById('registration-form');
        form.insertBefore(alert, form.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    }

    async loadInitialQuestions() {
        try {
            const response = await fetch(this.API_URL+`/api/chat/questions`, {
                headers: {
                    'X-API-KEY': this.apiKey
                }
            });
            
            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            this.currentQuestions = await response.json();
            this.questionHistory = []; // Reset history when loading initial questions
            this.displayQuestions(this.currentQuestions);
        } catch (error) {
            console.error('Error loading questions:', error);
            this.addMessage('Failed to load questions. Please try again later.', 'bot');
        }
    }

    displayQuestions(questions) {
        const container = document.getElementById('quick-questions');
        container.innerHTML = '';
        
        // Always show back button except for initial questions
        // if (this.questionHistory.length > 0 || (this.currentQuestions && this.currentQuestions.some(q => q.is_final))) {
        //     const backBtn = document.createElement('button');
        //     backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
        //     backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
        //     backBtn.addEventListener('click', () => this.goBack());
        //     container.appendChild(backBtn);
        // }

        if (this.questionHistory.length > 0) {
            const backBtn = document.createElement('button');
            backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
            backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
            backBtn.addEventListener('click', () => this.goBack());
            container.appendChild(backBtn);
        }
        
        console.log('display:', questions);

        questions.forEach(question => {
            const btn = document.createElement('button');
            btn.className = 'question-btn btn btn-outline-primary';
            btn.textContent = question.question;
            btn.addEventListener('click', () => this.handleQuestionClick(question));
            container.appendChild(btn);
        });
        
        // Show restart button in header
        document.getElementById('chatbot-restart').style.display = 'inline-block';
        
        // Hide input by default
        document.getElementById('user-input').style.display = 'none';
        document.getElementById('send-btn').style.display = 'none';
    }

    goBack() {
        if (this.questionHistory.length > 0) {
            const previousQuestions = this.questionHistory.pop();
            this.currentQuestions = previousQuestions;
            this.displayQuestions(previousQuestions);
        }
    }

    restartChat() {
        // Clear localStorage
        localStorage.removeItem('chatbot_session_id');
        localStorage.removeItem('chatbot_user_data');
        
        // Clear chat messages
        document.getElementById('chat-messages').innerHTML = '';
        
        // Show registration form again
        document.getElementById('registration-form').style.display = 'block';
        document.getElementById('chat-messages').style.display = 'none';
        document.getElementById('quick-questions').style.display = 'none';
        document.getElementById('chatbot-footer').style.display = 'none';
        
        // Reset states
        this.sessionId = null;
        this.userData = null;
        this.currentQuestions = null;
        this.questionHistory = [];
        
        // Hide restart button
        document.getElementById('chatbot-restart').style.display = 'none';
        
        // Clear input fields
        document.getElementById('user-name').value = '';
        document.getElementById('user-email').value = '';
        document.getElementById('user-mobile').value = '';
        
        // Hide input and send button
        document.getElementById('user-input').style.display = 'none';
        document.getElementById('send-btn').style.display = 'none';
    }

    async logout() {
        try {
            // Show loading state on restart button
            const restartBtn = document.getElementById('chatbot-restart');
            restartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            restartBtn.disabled = true;
    
            // Send request to end session if we have a session ID
            if (this.sessionId) {
                const response = await fetch(this.API_URL+'/api/chat/sessions/end', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': this.apiKey
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId
                    })
                });
    
                if (!response.ok) {
                    throw new Error(`Failed to end session: ${response.status}`);
                }
            }
    
            // Clear localStorage
            localStorage.removeItem('chatbot_session_id');
            localStorage.removeItem('chatbot_user_data');
            
            // Clear chat messages
            document.getElementById('chat-messages').innerHTML = '';
            
            // Show registration form again
            document.getElementById('registration-form').style.display = 'block';
            document.getElementById('chat-messages').style.display = 'none';
            document.getElementById('quick-questions').style.display = 'none';
            document.getElementById('chatbot-footer').style.display = 'none';
            
            // Reset states
            this.sessionId = null;
            this.userData = null;
            this.currentQuestions = null;
            this.questionHistory = [];
            
            // Reset restart button
            restartBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            restartBtn.disabled = false;
            restartBtn.style.display = 'none';
            
            // Clear input fields
            document.getElementById('user-name').value = '';
            document.getElementById('user-email').value = '';
            document.getElementById('user-mobile').value = '';
            
            // Hide input and send button
            document.getElementById('user-input').style.display = 'none';
            document.getElementById('send-btn').style.display = 'none';
    
        } catch (error) {
            console.error('Error during logout:', error);
            
            // Reset button state even if error occurred
            const restartBtn = document.getElementById('chatbot-restart');
            restartBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            restartBtn.disabled = false;
            
            // Show error message
            this.addMessage("Failed to properly end the session. All local data has been cleared.", 'bot');
        }
    }

    // handleQuestionClick(question) {

    //     this.addMessage(question.question, 'user');
    //     // Add user message only if not waiting for input
    //     if (question.enable_input) {
    //         // Show bot asking the input
    //         this.addMessage(question.question, 'bot');
    //     } else {
    //         this.addMessage(question.question, 'user');
    //     }
        
    //     // Save current questions to history before displaying new ones
    //     // if (!question.is_final) {
    //     //     this.questionHistory.push(this.currentQuestions);
    //     // }
    //     this.questionHistory.push(this.currentQuestions);
        
    //     if(question.is_final) {
    //         this.addMessage(question.answer, 'bot');
    //         this.currentQuestions = null;
    //         document.getElementById('quick-questions').innerHTML = '';
            
    //         // Show back button even for final answers
    //         const backBtn = document.createElement('button');
    //         backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
    //         backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
    //         backBtn.addEventListener('click', () => this.goBack());
    //         document.getElementById('quick-questions').appendChild(backBtn);
    //     } else if (question.enable_input) {
    //         this.pendingNextQuestions = question.children;
    //     }else {
    //         this.currentQuestions = question.children;
    //         this.displayQuestions(question.children);
    //     }
        
    //     // Show input only if question has input enabled
    //     const inputEnabled = question.enable_input || false;
    //     document.getElementById('user-input').style.display = inputEnabled ? 'block' : 'none';
    //     document.getElementById('send-btn').style.display = inputEnabled ? 'block' : 'none';
        
    //     // Focus input if enabled
    //     if (inputEnabled) {
    //         document.getElementById('user-input').focus();
    //     }
    // }

    handleQuestionClick(question) {
        // Save current questions to history
        this.questionHistory.push(this.currentQuestions);
    
        // If input is required, show bot message instead of user
        if (question.enable_input) {
            this.addMessage(question.question, 'bot');
            this.pendingNextQuestions = question.children;
    
            // Clear quick questions so only input is visible
            document.getElementById('quick-questions').innerHTML = '';
        } else {
            this.addMessage(question.question, 'user');
    
            if (question.is_final) {
                this.addMessage(question.answer, 'bot');
                this.currentQuestions = null;
                document.getElementById('quick-questions').innerHTML = '';
    
                const backBtn = document.createElement('button');
                backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
                backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
                backBtn.addEventListener('click', () => this.goBack());
                document.getElementById('quick-questions').appendChild(backBtn);
            } else {
                this.currentQuestions = question.children;
                this.displayQuestions(question.children);
            }
        }
    
        // Show or hide input based on question
        const inputEnabled = question.enable_input || false;
        document.getElementById('user-input').style.display = inputEnabled ? 'block' : 'none';
        document.getElementById('send-btn').style.display = inputEnabled ? 'block' : 'none';
    
        if (inputEnabled) {
            document.getElementById('user-input').focus();
        }
    }
    

    async handleUserMessage() {
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        
        if(!message) return;
        
        this.addMessage(message, 'user');
        input.value = '';
        
        try {
            const response = await fetch(this.API_URL+'/api/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': this.apiKey
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    message: message
                })
            });
            
            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // console.log(data.message);
            // this.addMessage(data.message, 'bot');

            // Check if there are pending next questions
            if (this.pendingNextQuestions && this.pendingNextQuestions.length > 0) {
                this.currentQuestions = this.pendingNextQuestions;
                this.displayQuestions(this.pendingNextQuestions);
                this.pendingNextQuestions = null;
            } else {
                this.pendingNextQuestions = null;
                this.addMessage(data.message || "Chat message saved successfully", 'bot');
            }


        } catch (error) {
            console.error('Error sending message:', error);
            this.addMessage("Sorry, I'm having trouble responding right now. Please try again later.", 'bot');
        }
    }

    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chat-messages');
        const message = document.createElement('div');
        message.className = `message ${sender}-message`;
        message.textContent = text;
        messagesContainer.appendChild(message);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    toggleMinimize() {
        this.isMinimized = !this.isMinimized;
        this.container.classList.toggle('minimized');
    }

    closeWidget() {
        this.container.remove();
    }
}

// Initialize when script is loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        // 1. Get the chatbot container
        const container = document.getElementById('chatbot-container');
        
        if (!container) {
            throw new Error('Chatbot container element not found');
        }

        // 2. Get API key from data attribute
        const apiKey = container.dataset.key;
        
        if (!apiKey) {
            throw new Error('API key not found in data-key attribute');
        }

        // 3. Initialize chatbot with the API key
        new ChatbotWidget(apiKey);

    } catch (error) {
        console.error('Chatbot initialization error:', error);
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
        errorDiv.style.zIndex = '1100';
        errorDiv.innerHTML = `
            <strong>Chatbot Error:</strong> ${error.message}<br>
            Please contact support if this persists.
        `;
        document.body.appendChild(errorDiv);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 10000);
    }
});
