// chatbot.js
class ChatbotWidget {
    constructor(apiKey) {
        this.API_URL = 'http://127.0.0.1:8000';
        this.apiKey = apiKey;
        this.sessionId = localStorage.getItem('chatbot_session_id') || null;
        this.userData = JSON.parse(localStorage.getItem('chatbot_user_data')) || null;
        this.currentQuestions = null;
        this.questionHistory = [];
        this.isMinimized = false;
        this.pendingNextQuestions = null;
        this.question_answer_after_input = null;
        this.init();
    }

    async init() {
        this.createWidget();
        this.bindEvents();
        this.bindUnloadEvents();
    
        if (this.sessionId && this.userData) {
            const isStillActive = await this.checkSessionStatus();
            
            if (isStillActive) {
                this.showChatInterface();
            } else {
                this.logout();
            }
        }
    }

    async checkSessionStatus() {
        try {
            const response = await fetch(`${this.API_URL}/api/chat/sessions/check?session_id=${this.sessionId}`, {
                headers: {
                    'X-API-KEY': this.apiKey
                }
            });
            const data = await response.json();
            this.sessionId = data.session_id;
            return data.status === 'active';
        } catch (error) {
            console.error('Failed to verify session status:', error);
            return false;
        }
    }

    bindUnloadEvents() {
        const sendLogoutBeacon = () => {
            this.logEvent('user', question.question, 'window_closed');
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

    startSessionMonitor() {
        this.stopSessionMonitor();
        this.sessionMonitorInterval = setInterval(async () => {
            if (!this.sessionId) return;
    
            try {
                const res = await fetch(`${this.API_URL}/api/chat/sessions/check?session_id=${this.sessionId}`, {
                    headers: {
                        'X-API-KEY': this.apiKey
                    }
                });
                const data = await res.json();
    
                if (data.status === 'ended') {
                    this.logout('⏹️ Your session has been ended due to inactivity.');
                }
            } catch (e) {
                console.warn('Session check failed:', e);
            }
        }, 1000);
    }
    
    stopSessionMonitor() {
        if (this.sessionMonitorInterval) {
            clearInterval(this.sessionMonitorInterval);
            this.sessionMonitorInterval = null;
        }
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
        this.injectStyles();
        this.loadFontAwesome();
    }

    showChatInterface() {
        document.getElementById('registration-form').style.display = 'none';
        document.getElementById('chat-messages').style.display = 'block';
        document.getElementById('quick-questions').style.display = 'block';
        document.getElementById('chatbot-footer').style.display = 'block';
        document.getElementById('chatbot-restart').style.display = 'inline-block';
        
        if (document.getElementById('chat-messages').children.length === 0) {
            this.addMessage(`Welcome back, ${this.userData.name}! How can I help you today?`, 'bot');
        }
        
        this.loadInitialQuestions();
        this.startSessionMonitor();
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
            
            .rich-text-content img {
                max-width: 100%;
                height: auto;
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
        
        document.getElementById('user-input')?.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') this.handleUserMessage();
        });
    }

    async startChat() {
        const location = await this.getLocationFromIp();
        console.log(location);

        const nameInput = document.getElementById('user-name');
        const emailInput = document.getElementById('user-email');
        
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

        document.getElementById('start-chat-text').classList.add('d-none');
        document.getElementById('start-chat-spinner').classList.remove('d-none');
        document.getElementById('start-chat-btn').disabled = true;

        this.userData = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            mobile: document.getElementById('user-mobile').value.trim(),
            ip_address:location.ip,
            location:location.city,
            location_json:JSON.stringify(location)
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
            
            localStorage.setItem('chatbot_session_id', this.sessionId);
            localStorage.setItem('chatbot_user_data', JSON.stringify(this.userData));
            
            this.showChatInterface();
        } catch (error) {
            console.error('Error starting chat:', error);
            this.showAlert('Failed to start chat. Please try again.', 'danger');
        } finally {
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
            this.questionHistory = [];
            this.displayQuestions(this.currentQuestions);
        } catch (error) {
            console.error('Error loading questions:', error);
            this.addMessage('Failed to load questions. Please try again later.', 'bot');
        }
    }

    displayQuestions(questions) {
        const container = document.getElementById('quick-questions');
        container.innerHTML = '';     
      
        if (this.questionHistory.length > 0) {
            const backBtn = document.createElement('button');
            backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
            backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
            backBtn.addEventListener('click', () => this.goBack());
            container.appendChild(backBtn);
        }
        
        if (typeof questions !== 'undefined'){
            this.logEvent('bot', JSON.stringify(questions), 'list');
            questions.forEach(question => {
                const btn = document.createElement('button');
                btn.className = 'question-btn btn btn-outline-primary';
                btn.textContent = question.question;
                btn.addEventListener('click', () => this.handleQuestionClick(question));
                container.appendChild(btn);
            });
        }
        
        document.getElementById('chatbot-restart').style.display = 'inline-block';
        document.getElementById('user-input').style.display = 'none';
        document.getElementById('send-btn').style.display = 'none';
    }

    goBack() {
        this.logEvent('user', "Back Button", "clicked");
        if (this.questionHistory.length > 0) {
            const previousQuestions = this.questionHistory.pop();
            this.currentQuestions = previousQuestions;
            this.displayQuestions(previousQuestions);
        }
    }

    async logout(message = null) {
        try {
            const restartBtn = document.getElementById('chatbot-restart');
            restartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            restartBtn.disabled = true;
    
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
    
            localStorage.removeItem('chatbot_session_id');
            localStorage.removeItem('chatbot_user_data');

            document.getElementById('chat-messages').innerHTML = '';
            document.getElementById('registration-form').style.display = 'block';
            document.getElementById('chat-messages').style.display = 'none';
            document.getElementById('quick-questions').style.display = 'none';
            document.getElementById('chatbot-footer').style.display = 'none';

            if(message){
                document.getElementById('chat-messages').style.display = 'block';
                this.addMessage(message, 'bot');
            }
            
            this.sessionId = null;
            this.userData = null;
            this.currentQuestions = null;
            this.questionHistory = [];
            
            restartBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            restartBtn.disabled = false;
            restartBtn.style.display = 'none';
            
            document.getElementById('user-name').value = '';
            document.getElementById('user-email').value = '';
            document.getElementById('user-mobile').value = '';
            
            document.getElementById('user-input').style.display = 'none';
            document.getElementById('send-btn').style.display = 'none';

            this.stopSessionMonitor();
        } catch (error) {
            console.error('Error during logout:', error);
            
            const restartBtn = document.getElementById('chatbot-restart');
            restartBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            restartBtn.disabled = false;
            
            this.addMessage("Failed to properly end the session. All local data has been cleared.", 'bot');
        }
    }

    // handleQuestionClick(question) {

    //     console.log(question);

    //     if(question.answer){
    //         this.question_answer_after_input = question.answer;
    //     }
        
    //     this.questionHistory.push(this.currentQuestions);
    
    //     if (question.enable_input) {
    //         this.addMessage(question.question, 'bot');            
    //         this.logEvent('user', question.question, 'selected');
    //         this.logEvent('bot', question.question, 'input_for');
            
    //         this.pendingNextQuestions = question.children;
    
    //         document.getElementById('quick-questions').innerHTML = '';

    //         const backBtn = document.createElement('button');
    //         backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
    //         backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
    //         backBtn.addEventListener('click', () => this.goBack());
    //         document.getElementById('quick-questions').appendChild(backBtn);

    //     } else {
    //         this.addMessage(question.question, 'user');
    //         this.logEvent('user', question.question, 'selected');
    
    //         if (question.is_final) {
    //             this.addMessage(
    //                 question.answer, 
    //                 'bot', 
    //                 question.answer_type, 
    //                 question.answer_data ? JSON.parse(question.answer_data) : null
    //             );
    //             this.logEvent('bot', question.answer, 'final_answer');
    //             this.currentQuestions = null;
    //             document.getElementById('quick-questions').innerHTML = '';
    
    //             const backBtn = document.createElement('button');
    //             backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
    //             backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
    //             backBtn.addEventListener('click', () => this.goBack());
    //             document.getElementById('quick-questions').appendChild(backBtn);
    //         } else {
    //             if(question.answer){
    //                 this.addMessage(
    //                     question.answer, 
    //                     'bot', 
    //                     question.answer_type, 
    //                     question.answer_data ? JSON.parse(question.answer_data) : null
    //                 );
    //                 this.logEvent('bot', question.answer, 'final_answer');
    //             }
                
    //             this.currentQuestions = question.children;
    //             this.displayQuestions(question.children);
    //         }
    //     }
    
    //     const inputEnabled = question.enable_input || false;
    //     document.getElementById('user-input').style.display = inputEnabled ? 'block' : 'none';
    //     document.getElementById('send-btn').style.display = inputEnabled ? 'block' : 'none';
    
    //     if (inputEnabled) {
    //         document.getElementById('user-input').focus();
    //     }
    // }

    async handleQuestionClick(question) {
        this.logEvent('user', question.question, 'selected');
        
        // Add user's question to chat
        this.addMessage(question.question, 'user');
        
        // Store current questions in history before fetching new ones
        this.questionHistory.push(this.currentQuestions);
        
        try {
            // Show loading state
            const quickQuestionsContainer = document.getElementById('quick-questions');
            quickQuestionsContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>';
            
            // Fetch question details from API
            const response = await fetch(`${this.API_URL}/api/chat/questions/${question.id}`, {
                headers: {
                    'X-API-KEY': this.apiKey
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const questionData = await response.json();
            
            // Handle the response based on question type
            if (questionData.enable_input) {
                // Show input field for user response
                this.pendingNextQuestions = questionData.children || null;
                
                quickQuestionsContainer.innerHTML = '';
                
                // Add back button
                const backBtn = document.createElement('button');
                backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
                backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
                backBtn.addEventListener('click', () => this.goBack());
                quickQuestionsContainer.appendChild(backBtn);
                
                // Show input field
                document.getElementById('user-input').style.display = 'block';
                document.getElementById('send-btn').style.display = 'block';
                document.getElementById('user-input').focus();
                
            } else {
                // Show answer if available
                if (questionData.answer) {
                    this.addMessage(
                        questionData.answer, 
                        'bot', 
                        questionData.answer_type, 
                        questionData.answer_data ? JSON.parse(questionData.answer_data) : null
                    );
                    this.logEvent('bot', questionData.answer, 'answer');
                }
                
                // Show child questions if available
                if (questionData.children && questionData.children.length > 0) {
                    this.currentQuestions = questionData.children;
                    this.displayQuestions(questionData.children);
                } else {
                    // No children - show back button
                    quickQuestionsContainer.innerHTML = '';
                    
                    const backBtn = document.createElement('button');
                    backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
                    backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
                    backBtn.addEventListener('click', () => this.goBack());
                    quickQuestionsContainer.appendChild(backBtn);
                    
                    this.currentQuestions = null;
                }
            }
            
        } catch (error) {
            console.error('Error handling question:', error);
            this.addMessage("Sorry, I'm having trouble processing your request. Please try again.", 'bot');
            
            // Restore previous state
            this.goBack();
        }
    }

    async handleUserMessage() {
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        
        if(!message) return;
        
        this.addMessage(message, 'user');
        this.logEvent('user', message, 'input');

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

            if (this.pendingNextQuestions && this.pendingNextQuestions.length > 0) {
                this.currentQuestions = this.pendingNextQuestions;
                this.displayQuestions(this.pendingNextQuestions);
                this.pendingNextQuestions = null;
            } else {
                this.pendingNextQuestions = null;
                if(this.question_answer_after_input){
                    this.addMessage(
                        this.question_answer_after_input, 
                        'bot',
                        'simple'
                    );
                    this.logEvent('bot', this.question_answer_after_input, 'final_answer');
                }
                else{
                    this.addMessage(
                        data.message || "Chat message saved successfully", 
                        'bot',
                        'simple'
                    );
                    this.logEvent('bot', data.message || "Chat message saved successfully", 'final_answer');
                }
                
                document.getElementById('quick-questions').innerHTML = '';
    
                const backBtn = document.createElement('button');
                backBtn.className = 'question-btn btn btn-outline-secondary mb-2';
                backBtn.innerHTML = '<i class="fas fa-arrow-left me-2"></i> Back';
                backBtn.addEventListener('click', () => this.goBack());
                document.getElementById('quick-questions').appendChild(backBtn);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.addMessage("Sorry, I'm having trouble responding right now. Please try again later.", 'bot');
        }
    }

    addMessage(answer, sender = 'bot', answerType = 'simple', answerData = null) {
        if (!answer) return;
        
        const messagesContainer = document.getElementById('chat-messages');
        const message = document.createElement('div');
        message.className = `message ${sender}-message`;
    
        let content = '';
        
        

        if (sender === 'bot') {
            switch (answerType) {
                case 'rich_text':
                    content = `<div class="rich-text-content">${answer}</div>`;
                    break;
    
                case 'file':
                    if (answerData) {
                        
                        const baseUrl = this.API_URL+'/file/view/';
                        const fileUrl = baseUrl + this.extractFileName(answerData.file_path || '');
    
                        // if (answerData.mime_type.startsWith('image/')) {
                        //     content = `<div class="text-center">
                        //         <img src="${fileUrl}" class="img-fluid rounded" alt="Image preview">
                        //     </div>`;
                        // }
                        
                        console.log('Attempting to load file from:', fileUrl); // Debug log

                    if (answerData.mime_type.startsWith('image/')) {
                        // Create a temporary image to test loading
                        const testImg = new Image();
                        testImg.src = fileUrl;
                        
                        testImg.onload = () => {
                            // If image loads successfully, update the content
                            message.innerHTML = `
                                <div class="text-center">
                                    <img src="${fileUrl}" 
                                         class="img-fluid rounded" 
                                         alt="Image preview">
                                </div>
                            `;
                        };
                        
                        testImg.onerror = () => {
                            // If image fails to load, show error message
                            message.innerHTML = `
                                <div class="alert alert-warning">
                                    Image failed to load. 
                                    <a href="${fileUrl}" target="_blank">Open in new tab</a>
                                </div>
                            `;
                        };
                        
                        // Show loading placeholder immediately
                        content = `
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading image...</span>
                                </div>
                                <p>Loading image...</p>
                            </div>
                        `;
                    }
                        else if (answerData.mime_type.startsWith('video/')) {
                            content = `<div class="ratio ratio-16x9">
                                <video controls>
                                    <source src="${fileUrl}" type="${answerData.mime_type}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>`;
                        }
                        else if (answerData.mime_type.startsWith('application/pdf')) {
                            content = `<div class="text-center">
                                <i class="fas fa-file-pdf fa-3x text-danger"></i><br>
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">View PDF</a>
                            </div>`;
                        }
                        else {
                            content = `<div class="text-center">
                                <i class="fas fa-file-alt fa-3x"></i><br>
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">Download File</a>
                            </div>`;
                        }
                    } else {
                        content = `<p>No file available.</p>`;
                    }
                    break;
    
                case 'youtube':
                    if (answerData) {
                        content = `<div class="ratio ratio-16x9">
                            <iframe src="${answerData.embed_url}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>`;
                    } else {
                        content = `<p>No YouTube video available.</p>`;
                    }
                    break;
    
                default:
                    content = `<p>${answer}</p>`;
            }
        } else {
            content = `<p>${answer}</p>`;
        }
    
        message.innerHTML = content;
        messagesContainer.appendChild(message);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    extractFileName(path) {
        return path.split('/').pop();
    }
    
    async logEvent(sender, message, type = null, parent_id = null) {
        try {
            await fetch(this.API_URL + '/api/chat/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': this.apiKey
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    sender: sender,
                    message: message,
                    type: type,
                    parent_id: parent_id
                })
            });
        } catch (error) {
            console.error('Failed to log chat event:', error);
        }
    }

    toggleMinimize() {
        this.isMinimized = !this.isMinimized;
        this.container.classList.toggle('minimized');
    }

    closeWidget() {
        this.container.remove();
    }

    async getIpAddress() {
        try {
            const response = await fetch("https://api.ipify.org?format=json");
            const data = await response.json();
            return data.ip;
        } catch (error) {
            console.error("Error fetching IP address:", error);
            return null;
        }
    }

    async getLocationFromIp() {
        try {
            const response = await fetch("http://ip-api.com/json/");
            const data = await response.json();
            return {
                ip: data.query,
                city: data.city,
                region: data.regionName,
                country: data.country,
                lat: data.lat,
                lon: data.lon
            };
        } catch (error) {
            console.error("Error fetching location:", error);
            return null;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    try {
        const container = document.getElementById('chatbot-container');
        
        if (!container) {
            throw new Error('Chatbot container element not found');
        }

        const apiKey = container.dataset.key;
        
        if (!apiKey) {
            throw new Error('API key not found in data-key attribute');
        }

        new ChatbotWidget(apiKey);
    } catch (error) {
        console.error('Chatbot initialization error:', error);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
        errorDiv.style.zIndex = '1100';
        errorDiv.innerHTML = `
            <strong>Chatbot Error:</strong> ${error.message}<br>
            Please contact support if this persists.
        `;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 10000);
    }
});