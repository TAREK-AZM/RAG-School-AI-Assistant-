<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant - RAG Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: rgba(79, 70, 229, 0.1);
            --primary-hover: #4338ca;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --border-radius: 0.5rem;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --message-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .chat-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 1rem;
        }
        
        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }
        
        .chat-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .chat-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #111827;
        }
        
        .chat-title .badge {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 1rem;
        }
        
        .chat-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .chat-welcome {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin: 2rem 0;
        }
        
        .chat-welcome h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #111827;
        }
        
        .chat-welcome p {
            color: var(--secondary-color);
            max-width: 600px;
            margin: 0 auto 1.5rem;
        }
        
        .chat-welcome .examples {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        .chat-welcome .example-chip {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .chat-welcome .example-chip:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .message {
            display: flex;
            gap: 1rem;
            max-width: 80%;
            animation: fadeIn 0.3s ease;
        }
        
        .message.user-message {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        
        .message-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .user-message .message-avatar {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        .ai-message .message-avatar {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        .message-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .message-bubble {
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--message-shadow);
            position: relative;
        }
        
        .user-message .message-bubble {
            background-color: var(--primary-color);
            color: white;
            border-top-right-radius: 0;
        }
        
        .ai-message .message-bubble {
            background-color: white;
            color: #1f2937;
            border-top-left-radius: 0;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: var(--secondary-color);
            align-self: flex-end;
        }
        
        .user-message .message-time {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .message-sources {
            margin-top: 0.75rem;
        }
        
        .sources-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--primary-color);
            background: none;
            border: none;
            padding: 0.5rem 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sources-toggle:hover {
            color: var(--primary-hover);
        }
        
        .sources-content {
            display: none;
            margin-top: 0.75rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        
        .source-item {
            padding: 0.75rem;
            border-radius: 0.375rem;
            background-color: white;
            margin-bottom: 0.75rem;
            border: 1px solid #e5e7eb;
        }
        
        .source-item:last-child {
            margin-bottom: 0;
        }
        
        .source-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #111827;
            font-size: 0.875rem;
        }
        
        .source-content {
            color: #4b5563;
            font-size: 0.8125rem;
            line-height: 1.5;
        }
        
        .chat-footer {
            padding: 1rem 0;
            border-top: 1px solid #e5e7eb;
        }
        
        .chat-input-container {
            display: flex;
            position: relative;
        }
        
        .chat-input {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            padding: 0.875rem 4rem 0.875rem 1rem;
            font-size: 0.95rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            resize: none;
            min-height: 60px;
            max-height: 150px;
            transition: all 0.2s ease;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .chat-send-btn {
            position: absolute;
            right: 0.75rem;
            bottom: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .chat-send-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .chat-send-btn:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }
        
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            background-color: white;
            border-radius: var(--border-radius);
            width: fit-content;
            box-shadow: var(--message-shadow);
            margin-left: 3.5rem;
        }
        
        .typing-dot {
            width: 0.5rem;
            height: 0.5rem;
            background-color: #d1d5db;
            border-radius: 50%;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) {
            animation-delay: 0s;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typingAnimation {
            0%, 60%, 100% {
                transform: translateY(0);
                background-color: #d1d5db;
            }
            30% {
                transform: translateY(-5px);
                background-color: var(--primary-color);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .chat-container {
                padding: 0.5rem;
            }
            
            .message {
                max-width: 90%;
            }
            
            .chat-welcome {
                padding: 1.5rem 1rem;
            }
            
            .chat-title h1 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-title">
                <h1>AI Assistant</h1>
                <span class="badge">RAG-powered</span>
            </div>
            <div class="chat-actions">
                <button class="btn btn-outline-secondary btn-sm" id="clearChat">
                    <i class="bi bi-trash"></i> Clear Chat
                </button>
            </div>
        </div>
        
        <div class="chat-body" id="chatBody">
            <div class="chat-welcome">
                <h2>Welcome to the AI Assistant</h2>
                <p>Ask me anything about your documents. I'll use the knowledge from your uploaded files to provide accurate and relevant answers.</p>
                <div class="examples">
                    <div class="example-chip" data-question="What are the main features of the system?">What are the main features of the system?</div>
                    <div class="example-chip" data-question="Can you summarize the document about ENSAT?">Can you summarize the document about ENSAT?</div>
                    <div class="example-chip" data-question="What is RAG and how does it work?">What is RAG and how does it work?</div>
                </div>
            </div>
        </div>
        
        <div class="chat-footer">
            <form id="chatForm">
                <div class="chat-input-container">
                    <textarea class="chat-input" id="userQuestion" placeholder="Type your question here..." rows="1"></textarea>
                    <button type="submit" class="chat-send-btn" id="sendButton">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const chatBody = $('#chatBody');
            const userQuestion = $('#userQuestion');
            const sendButton = $('#sendButton');
            const chatForm = $('#chatForm');
            const clearChatBtn = $('#clearChat');
            
            // Auto-resize textarea
            userQuestion.on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
                
                // Enable/disable send button based on input
                if ($(this).val().trim() === '') {
                    sendButton.prop('disabled', true);
                } else {
                    sendButton.prop('disabled', false);
                }
            });
            
            // Initialize send button state
            sendButton.prop('disabled', true);
            
            // Example question chips
            $('.example-chip').on('click', function() {
                const question = $(this).data('question');
                userQuestion.val(question);
                userQuestion.trigger('input');
                chatForm.submit();
            });
            
            // Clear chat
            clearChatBtn.on('click', function() {
                $('.message').remove();
                $('.typing-indicator').remove();
                $('.chat-welcome').show();
            });
            
            // Handle form submission
            chatForm.on('submit', function(e) {
                e.preventDefault();
                
                const question = userQuestion.val().trim();
                if (question === '') return;
                
                // Hide welcome message if visible
                $('.chat-welcome').hide();
                
                // Add user message to chat
                addUserMessage(question);
                
                // Clear input and reset height
                userQuestion.val('');
                userQuestion.css('height', 'auto');
                sendButton.prop('disabled', true);
                
                // Show typing indicator
                showTypingIndicator();
                
                // Scroll to bottom
                scrollToBottom();
                
                // Send request to server
                $.ajax({
                    url: '{{ route("school-assistant.ask") }}',
                    type: 'POST',
                    data: {
                        question: question,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Remove typing indicator
                        $('.typing-indicator').remove();
                        
                        // Add AI message to chat
                        addAIMessage(response.answer, response.sources);
                        
                        // Scroll to bottom
                        scrollToBottom();
                    },
                    error: function(error) {
                        // Remove typing indicator
                        $('.typing-indicator').remove();
                        
                        // Add error message
                        addAIMessage("I'm sorry, I encountered an error while processing your question. Please try again later.", null);
                        
                        console.error('Error:', error);
                        
                        // Scroll to bottom
                        scrollToBottom();
                    }
                });
            });
            
            // Function to add user message to chat
            function addUserMessage(message) {
                const time = getCurrentTime();
                const messageHtml = `
                    <div class="message user-message">
                        <div class="message-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="message-content">
                            <div class="message-bubble">${message}</div>
                            <div class="message-time">${time}</div>
                        </div>
                    </div>
                `;
                
                chatBody.append(messageHtml);
            }
            
            // Function to add AI message to chat
            function addAIMessage(message, sources) {
                const time = getCurrentTime();
                let sourcesHtml = '';
                
                if (sources && sources.length > 0) {
                    let sourcesContent = '';
                    sources.forEach(function(source) {
                        sourcesContent += `
                            <div class="source-item">
                                <div class="source-title">${source.document_title}</div>
                                <div class="source-content">${source.content}</div>
                            </div>
                        `;
                    });
                    
                    sourcesHtml = `
                        <div class="message-sources">
                            <button class="sources-toggle" onclick="toggleSources(this)">
                                <i class="bi bi-info-circle"></i>
                                <span>View ${sources.length} source${sources.length > 1 ? 's' : ''}</span>
                            </button>
                            <div class="sources-content">
                                ${sourcesContent}
                            </div>
                        </div>
                    `;
                }
                
                const messageHtml = `
                    <div class="message ai-message">
                        <div class="message-avatar">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div class="message-content">
                            <div class="message-bubble">${message}</div>
                            <div class="message-time">${time}</div>
                            ${sourcesHtml}
                        </div>
                    </div>
                `;
                
                chatBody.append(messageHtml);
            }
            
            // Function to show typing indicator
            function showTypingIndicator() {
                const typingHtml = `
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                `;
                
                chatBody.append(typingHtml);
            }
            
            // Function to get current time
            function getCurrentTime() {
                const now = new Date();
                let hours = now.getHours();
                let minutes = now.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                
                hours = hours % 12;
                hours = hours ? hours : 12;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                
                return `${hours}:${minutes} ${ampm}`;
            }
            
            // Function to scroll to bottom of chat
            function scrollToBottom() {
                chatBody.scrollTop(chatBody[0].scrollHeight);
            }
        });
        
        // Function to toggle sources visibility
        function toggleSources(button) {
            const sourcesContent = $(button).next('.sources-content');
            const iconElement = $(button).find('i');
            const textElement = $(button).find('span');
            
            if (sourcesContent.is(':visible')) {
                sourcesContent.slideUp(200);
                iconElement.removeClass('bi-info-circle-fill').addClass('bi-info-circle');
                textElement.text(textElement.text().replace('Hide', 'View'));
            } else {
                sourcesContent.slideDown(200);
                iconElement.removeClass('bi-info-circle').addClass('bi-info-circle-fill');
                textElement.text(textElement.text().replace('View', 'Hide'));
            }
        }
    </script>
</body>
</html>
