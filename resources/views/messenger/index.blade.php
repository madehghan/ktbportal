<x-app-layout>
    <div class="py-8" dir="rtl" x-data="messengerApp()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" style="height: calc(100vh - 200px);">
                <div class="flex h-full">
                    <!-- Sidebar: Users/Conversations List -->
                    <div class="w-80 border-l border-gray-200 bg-gray-50 flex flex-col">
                        <!-- Search Bar -->
                        <div class="p-4 border-b border-gray-200">
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                @input="filterUsers()"
                                placeholder="جستجوی کاربر..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            >
                        </div>

                        <!-- Users/Conversations List -->
                        <div class="flex-1 overflow-y-auto">
                            <!-- Conversations -->
                            <div x-show="filteredConversations.length > 0" class="p-2">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase px-2 mb-2">مکالمات</h3>
                                <template x-for="conv in filteredConversations" :key="conv.id">
                                    <div 
                                        @click="selectConversation(conv.id, conv.other_user.id)"
                                        :class="{'bg-primary/10 border-primary/20': selectedConversationId === conv.id}"
                                        class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 border border-transparent transition-all mb-1"
                                    >
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            <template x-if="conv.other_user.avatar">
                                                <img :src="getAvatarUrl(conv.other_user.avatar)" :alt="conv.other_user.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!conv.other_user.avatar">
                                                <span class="text-white font-bold text-sm" x-text="getInitials(conv.other_user.name)"></span>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="font-medium text-sm text-gray-900 truncate" x-text="conv.other_user.name"></p>
                                                <span class="text-xs text-gray-500" x-text="formatTime(conv.last_message_at)"></span>
                                            </div>
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-xs text-gray-500 truncate" x-text="getLastMessagePreview(conv.last_message)"></p>
                                                <span 
                                                    x-show="conv.unread_count > 0"
                                                    class="bg-primary text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                                                    x-text="conv.unread_count"
                                                ></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- All Users -->
                            <div class="p-2">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase px-2 mb-2">همه کاربران</h3>
                                <template x-for="user in filteredUsers" :key="user.id">
                                    <div 
                                        @click="startConversation(user.id)"
                                        class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 border border-transparent transition-all mb-1"
                                    >
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            <template x-if="user.avatar">
                                                <img :src="getAvatarUrl(user.avatar)" :alt="user.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!user.avatar">
                                                <span class="text-white font-bold text-sm" x-text="getInitials(user.name)"></span>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm text-gray-900 truncate" x-text="user.name"></p>
                                            <p class="text-xs text-gray-500 truncate" x-text="user.mobile || ''"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Main Chat Area -->
                    <div class="flex-1 flex flex-col" x-show="selectedConversationId">
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-gray-200 bg-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center overflow-hidden">
                                    <template x-if="selectedUser?.avatar">
                                        <img :src="getAvatarUrl(selectedUser.avatar)" :alt="selectedUser.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!selectedUser?.avatar">
                                        <span class="text-white font-bold text-sm" x-text="getInitials(selectedUser?.name || '')"></span>
                                    </template>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900" x-text="selectedUser?.name || ''"></p>
                                    <p class="text-xs text-gray-500" x-text="selectedUser?.mobile || ''"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div 
                            class="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-4"
                            id="messages-container"
                            x-ref="messagesContainer"
                        >
                            <template x-for="message in messages" :key="message.id">
                                <div 
                                    :class="(message.sender_id || (message.sender && message.sender.id)) === currentUserId ? 'flex justify-end items-end gap-2' : 'flex justify-start items-end gap-2'"
                                >
                                    <!-- Avatar for other user's messages (left side) -->
                                    <div 
                                        x-show="(message.sender_id || (message.sender && message.sender.id)) !== currentUserId"
                                        class="w-8 h-8 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden"
                                    >
                                        <template x-if="message.sender && message.sender.avatar">
                                            <img :src="getAvatarUrl(message.sender.avatar)" :alt="message.sender.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!message.sender || !message.sender.avatar">
                                            <span class="text-white font-bold text-xs" x-text="getInitials(message.sender?.name || '')"></span>
                                        </template>
                                    </div>

                                    <div 
                                        :class="(message.sender_id || (message.sender && message.sender.id)) === currentUserId 
                                            ? 'bg-primary text-white rounded-2xl rounded-tr-sm max-w-md' 
                                            : 'bg-white text-gray-900 rounded-2xl rounded-tl-sm max-w-md border border-gray-200'"
                                        class="px-4 py-2 shadow-sm"
                                    >
                                        <!-- Text Message -->
                                        <div x-show="message.type === 'text'">
                                            <p class="text-sm" x-text="message.body"></p>
                                        </div>

                                        <!-- Voice Message -->
                                        <div x-show="message.type === 'voice' && message.attachments && message.attachments.length > 0" class="flex items-center gap-2">
                                            <audio controls class="w-full">
                                                <source :src="getAttachmentUrl(message.attachments[0])" type="audio/mpeg">
                                            </audio>
                                        </div>
                                        <div x-show="message.type === 'voice' && (!message.attachments || message.attachments.length === 0)" class="flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                            </svg>
                                            <span class="text-sm">پیام صوتی</span>
                                        </div>

                                        <!-- File Message -->
                                        <div x-show="message.type === 'file'" class="space-y-2">
                                            <template x-for="attachment in message.attachments" :key="attachment.id">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <a 
                                                        :href="'/messenger/attachment/' + attachment.id"
                                                        class="text-sm underline hover:no-underline"
                                                        x-text="attachment.original_name"
                                                    ></a>
                                                </div>
                                            </template>
                                        </div>

                                        <p class="text-xs mt-1 opacity-70" x-text="formatTime(message.created_at)"></p>
                                    </div>

                                    <!-- Avatar for current user's messages (right side) -->
                                    <div 
                                        x-show="(message.sender_id || (message.sender && message.sender.id)) === currentUserId"
                                        class="w-8 h-8 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden"
                                    >
                                        <template x-if="currentUserAvatar">
                                            <img :src="getAvatarUrl(currentUserAvatar)" :alt="currentUserName" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!currentUserAvatar">
                                            <span class="text-white font-bold text-xs" x-text="getInitials(currentUserName)"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Message Input Area -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <!-- Voice Recording Indicator -->
                            <div x-show="isRecording" class="mb-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-red-700" x-text="'در حال ضبط... ' + recordingTime + ' ثانیه'"></span>
                                </div>
                                <button 
                                    @click="stopRecording()"
                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                                >
                                    توقف و ارسال
                                </button>
                            </div>

                            <div class="flex items-end gap-2">
                                <!-- File Input -->
                                <label class="cursor-pointer p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                    <input 
                                        type="file" 
                                        @change="handleFileSelect($event)"
                                        class="hidden"
                                        id="file-input"
                                    >
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </label>

                                <!-- Voice Recording Button -->
                                <button 
                                    @mousedown="startRecording()"
                                    @mouseup="stopRecording()"
                                    @mouseleave="stopRecording()"
                                    @touchstart="startRecording()"
                                    @touchend="stopRecording()"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                </button>

                                <!-- Text Input -->
                                <div class="flex-1">
                                    <textarea 
                                        x-model="messageText"
                                        @keydown.enter.prevent="sendMessage()"
                                        placeholder="پیام خود را بنویسید..."
                                        rows="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                    ></textarea>
                                </div>

                                <!-- Send Button -->
                                <button 
                                    @click="sendMessage()"
                                    :disabled="!messageText.trim() && !selectedFile && !recordedAudio"
                                    class="p-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!selectedConversationId" class="flex-1 flex items-center justify-center bg-gray-50">
                        <div class="text-center">
                            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-gray-500">یک کاربر را انتخاب کنید تا شروع به گفتگو کنید</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function messengerApp() {
            return {
                users: @js($users),
                conversations: @js($conversations),
                filteredUsers: @js($users),
                filteredConversations: @js($conversations),
                searchQuery: '',
                selectedConversationId: null,
                selectedUserId: null,
                selectedUser: null,
                messages: [],
                messageText: '',
                currentUserId: @js(auth()->id()),
                currentUserAvatar: @js(auth()->user()->avatar),
                currentUserName: @js(auth()->user()->name),
                selectedFile: null,
                recordedAudio: null,
                isRecording: false,
                recordingTime: 0,
                mediaRecorder: null,
                audioChunks: [],
                echo: null,
                pollingInterval: null,

                init() {
                    this.filterUsers();
                    this.setupEcho();
                },

                setupEcho() {
                    // Setup Laravel Echo for real-time updates
                    if (typeof Echo !== 'undefined') {
                        this.echo = Echo;
                        
                        // Listen for new messages
                        this.echo.private(`user.${this.currentUserId}`)
                            .listen('.message.sent', (e) => {
                                if (e.message.conversation_id === this.selectedConversationId) {
                                    this.messages.push(e.message);
                                    this.scrollToBottom();
                                }
                                this.updateConversationList(e.message);
                            });
                    } else {
                        // Fallback to polling if Echo is not available
                        this.startPolling();
                    }
                },

                startPolling() {
                    // Clear existing polling if any
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                    }
                    
                    // Poll for new messages every 2 seconds
                    this.pollingInterval = setInterval(async () => {
                        if (this.selectedConversationId) {
                            try {
                                const response = await fetch(`/messenger/messages/${this.selectedConversationId}`);
                                const newMessages = await response.json();
                                
                                // Check if there are new messages
                                const lastMessageId = this.messages.length > 0 
                                    ? this.messages[this.messages.length - 1].id 
                                    : 0;
                                
                                const unreadMessages = newMessages.filter(msg => msg.id > lastMessageId);
                                
                                if (unreadMessages.length > 0) {
                                    this.messages.push(...unreadMessages);
                                    this.scrollToBottom();
                                }
                            } catch (error) {
                                console.error('Error polling messages:', error);
                            }
                        }
                    }, 2000);
                },

                filterUsers() {
                    const query = this.searchQuery.toLowerCase();
                    this.filteredUsers = this.users.filter(user => 
                        user.name.toLowerCase().includes(query) ||
                        (user.mobile && user.mobile.includes(query))
                    );
                    
                    this.filteredConversations = this.conversations.filter(conv => 
                        conv.other_user.name.toLowerCase().includes(query)
                    );
                },

                async startConversation(userId) {
                    try {
                        const response = await fetch(`/messenger/conversation/${userId}`);
                        const data = await response.json();
                        
                        this.selectedConversationId = data.conversation.id;
                        this.selectedUserId = userId;
                        this.selectedUser = data.other_user;
                        this.messages = data.messages;
                        
                        // Setup Echo for this conversation
                        if (this.echo && this.selectedConversationId) {
                            this.echo.private(`conversation.${this.selectedConversationId}`)
                                .listen('.message.sent', (e) => {
                                    this.messages.push(e.message);
                                    this.scrollToBottom();
                                });
                        } else if (!this.echo && this.selectedConversationId) {
                            // Ensure polling is running
                            if (!this.pollingInterval) {
                                this.startPolling();
                            }
                        }
                        
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error starting conversation:', error);
                    }
                },

                async selectConversation(conversationId, userId) {
                    await this.startConversation(userId);
                },

                async sendMessage() {
                    if (!this.selectedConversationId) return;
                    
                    const formData = new FormData();
                    formData.append('conversation_id', this.selectedConversationId);
                    formData.append('type', this.selectedFile ? 'file' : (this.recordedAudio ? 'voice' : 'text'));
                    
                    if (this.messageText.trim()) {
                        formData.append('body', this.messageText);
                    }
                    
                    if (this.selectedFile) {
                        formData.append('file', this.selectedFile);
                    }
                    
                    if (this.recordedAudio) {
                        formData.append('file', this.recordedAudio, 'voice.mp3');
                    }

                    try {
                        const response = await fetch('/messenger/send', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.messages.push(data.message);
                            this.messageText = '';
                            this.selectedFile = null;
                            this.recordedAudio = null;
                            this.scrollToBottom();
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                    }
                },

                handleFileSelect(event) {
                    this.selectedFile = event.target.files[0];
                    if (this.selectedFile) {
                        this.sendMessage();
                    }
                },

                async startRecording() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.audioChunks = [];
                        this.isRecording = true;
                        this.recordingTime = 0;

                        this.mediaRecorder.ondataavailable = (event) => {
                            this.audioChunks.push(event.data);
                        };

                        this.mediaRecorder.onstop = () => {
                            const audioBlob = new Blob(this.audioChunks, { type: 'audio/mpeg' });
                            this.recordedAudio = audioBlob;
                            stream.getTracks().forEach(track => track.stop());
                        };

                        this.mediaRecorder.start();
                        
                        // Update recording time
                        this.recordingInterval = setInterval(() => {
                            this.recordingTime++;
                        }, 1000);
                    } catch (error) {
                        console.error('Error accessing microphone:', error);
                        alert('دسترسی به میکروفون امکان‌پذیر نیست');
                    }
                },

                stopRecording() {
                    if (this.mediaRecorder && this.isRecording) {
                        this.mediaRecorder.stop();
                        this.isRecording = false;
                        clearInterval(this.recordingInterval);
                        
                        if (this.recordedAudio) {
                            this.sendMessage();
                        }
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                getInitials(name) {
                    if (!name) return '';
                    const parts = name.split(' ');
                    if (parts.length >= 2) {
                        return (parts[0][0] + parts[1][0]).toUpperCase();
                    }
                    return name.substring(0, 2).toUpperCase();
                },

                getAvatarUrl(avatar) {
                    if (!avatar) return '';
                    return `/storage/${avatar}`;
                },

                formatTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    const now = new Date();
                    const diff = now - date;
                    const minutes = Math.floor(diff / 60000);
                    const hours = Math.floor(diff / 3600000);
                    const days = Math.floor(diff / 86400000);

                    if (minutes < 1) return 'الان';
                    if (minutes < 60) return minutes + ' دقیقه پیش';
                    if (hours < 24) return hours + ' ساعت پیش';
                    if (days < 7) return days + ' روز پیش';
                    
                    return date.toLocaleDateString('fa-IR');
                },

                getLastMessagePreview(message) {
                    if (!message) return 'شروع گفتگو';
                    if (message.type === 'voice') return '🎤 پیام صوتی';
                    if (message.type === 'file') return '📎 فایل';
                    return message.body || 'پیام';
                },

                getAttachmentUrl(attachment) {
                    return attachment?.url || '';
                },

                updateConversationList(message) {
                    // Update conversation list when new message arrives
                    const convIndex = this.conversations.findIndex(c => c.id === message.conversation_id);
                    if (convIndex !== -1) {
                        this.conversations[convIndex].last_message = message;
                        this.conversations[convIndex].last_message_at = message.created_at;
                        if (message.sender_id !== this.currentUserId) {
                            this.conversations[convIndex].unread_count++;
                        }
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

