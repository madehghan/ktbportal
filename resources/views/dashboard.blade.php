<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $tabsData = $tabs->map(function($tab) {
                    return [
                        'id' => $tab->id,
                        'name' => $tab->name,
                        'content' => $tab->content ?? '',
                        'order' => $tab->order,
                        'users' => $tab->users->map(function($user) {
                            return ['id' => $user->id, 'name' => $user->name];
                        })->toArray(),
                    ];
                });
                $allUsersData = $isAdmin ? $allUsers->map(function($user) {
                    return ['id' => $user->id, 'name' => $user->name, 'mobile' => $user->mobile ?? ''];
                })->toArray() : [];
            @endphp
            <script>
                window.dashboardTabsData = @json($tabsData);
                window.dashboardActiveTabId = @json($tabs->first()?->id ?? null);
                window.dashboardIsAdmin = @json($isAdmin);
                window.dashboardCurrentUserId = @json(Auth::id());
                window.dashboardAllUsers = @json($allUsersData);
            </script>
            <!-- Agile Board with Tabs -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6"
                 x-data="{
                     tabs: window.dashboardTabsData || [],
                     activeTabId: window.dashboardActiveTabId || null,
                     isAdmin: window.dashboardIsAdmin || false,
                     currentUserId: window.dashboardCurrentUserId || 0,
                     allUsers: window.dashboardAllUsers || [],
                     showAddTabModal: false,
                     showEditTabModal: false,
                     showManageUsersModal: false,
                     editingTab: null,
                     editingTabName: '',
                     newTabName: '',
                     selectedUserIds: [],
                     quillInstances: {},
                     newTab: {
                         name: '',
                         user_ids: []
                     },
                     init() {
                         this.$watch('activeTabId', (tabId) => {
                             if (tabId && typeof Quill !== 'undefined') {
                                 this.$nextTick(() => {
                                     this.initQuillForTab(tabId);
                                 });
                             }
                         });
                         if (this.activeTabId) {
                             this.$nextTick(() => {
                                 this.initQuillForTab(this.activeTabId);
                             });
                         }
                     },
                     initQuillForTab(tabId) {
                         const editorId = `tab-content-${tabId}`;
                         const editor = document.getElementById(editorId);
                         if (!editor || this.quillInstances[tabId]) return;
                         
                         const tab = this.tabs.find(t => t.id === tabId);
                         if (!tab) return;
                         
                         // Only admin can edit
                         if (this.isAdmin) {
                             this.quillInstances[tabId] = new Quill(`#${editorId}`, {
                                 theme: 'snow',
                                 modules: {
                                     toolbar: [
                                         [{ 'header': [1, 2, 3, false] }],
                                         ['bold', 'italic', 'underline', 'strike'],
                                         [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                         [{ 'color': [] }, { 'background': [] }],
                                         [{ 'align': [] }],
                                         ['link', 'image'],
                                         ['clean']
                                     ]
                                 }
                             });
                             
                             if (tab.content) {
                                 this.quillInstances[tabId].root.innerHTML = tab.content;
                             }
                             
                             this.quillInstances[tabId].on('text-change', () => {
                                 this.saveTabContent(tabId);
                             });
                         }
                     },
                     async saveTabContent(tabId) {
                         const quill = this.quillInstances[tabId];
                         if (!quill) return;
                         
                         const content = quill.root.innerHTML;
                         const tab = this.tabs.find(t => t.id === tabId);
                         if (!tab) return;
                         
                         try {
                             const response = await fetch(`{{ url('/dashboard/tabs') }}/${tabId}/content`, {
                                 method: 'PUT',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                 },
                                 body: JSON.stringify({ content })
                             });
                             
                             const data = await response.json();
                             if (data.success) {
                                 tab.content = content;
                             }
                         } catch (error) {
                             console.error('Error:', error);
                         }
                     },
                     openAddTabModal() {
                         this.newTab = { name: '', user_ids: [] };
                         this.showAddTabModal = true;
                     },
                     async addTab() {
                         if (!this.newTab.name.trim()) return;
                         
                         try {
                             const response = await fetch('{{ route('dashboard.tabs.store') }}', {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                 },
                                 body: JSON.stringify(this.newTab)
                             });
                             
                             const data = await response.json();
                             if (data.success) {
                                 this.tabs.push(data.tab);
                                 this.activeTabId = data.tab.id;
                                 this.showAddTabModal = false;
                                 this.$nextTick(() => {
                                     this.initQuillForTab(data.tab.id);
                                 });
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در ایجاد تب');
                         }
                     },
                     openEditTabModal(tab) {
                         this.editingTab = tab;
                         this.editingTabName = tab.name;
                         this.selectedUserIds = tab.users.map(u => u.id);
                         this.showEditTabModal = true;
                     },
                     openManageUsersModal(tab) {
                         this.editingTab = tab;
                         this.selectedUserIds = tab.users.map(u => u.id);
                         this.showManageUsersModal = true;
                     },
                     async updateTab() {
                         if (!this.editingTab || !this.editingTabName.trim()) return;
                         
                         try {
                             const response = await fetch(`{{ url('/dashboard/tabs') }}/${this.editingTab.id}`, {
                                 method: 'PUT',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                 },
                                 body: JSON.stringify({ 
                                     name: this.editingTabName,
                                     user_ids: this.selectedUserIds 
                                 })
                             });
                             
                             const data = await response.json();
                             if (data.success) {
                                 const tab = this.tabs.find(t => t.id === this.editingTab.id);
                                 if (tab) {
                                     tab.name = data.tab.name;
                                     tab.users = data.tab.users;
                                 }
                                 this.showEditTabModal = false;
                                 this.editingTab = null;
                                 // Reload page to update visible tabs
                                 location.reload();
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در به‌روزرسانی تب');
                         }
                     },
                     async updateTabUsers() {
                         if (!this.editingTab) return;
                         
                         try {
                             const response = await fetch(`{{ url('/dashboard/tabs') }}/${this.editingTab.id}/users`, {
                                 method: 'PUT',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                 },
                                 body: JSON.stringify({ user_ids: this.selectedUserIds })
                             });
                             
                             const data = await response.json();
                             if (data.success) {
                                 const tab = this.tabs.find(t => t.id === this.editingTab.id);
                                 if (tab) {
                                     tab.users = this.allUsers.filter(u => this.selectedUserIds.includes(u.id));
                                 }
                                 this.showManageUsersModal = false;
                                 this.editingTab = null;
                                 // Reload page to update visible tabs
                                 location.reload();
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در به‌روزرسانی کاربران');
                         }
                     },
                     async deleteTab(tabId) {
                         if (!confirm('آیا از حذف این تب اطمینان دارید؟')) return;
                         
                         try {
                             const response = await fetch(`{{ url('/dashboard/tabs') }}/${tabId}`, {
                                 method: 'DELETE',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                 }
                             });
                             
                             const data = await response.json();
                             if (data.success) {
                                 this.tabs = this.tabs.filter(t => t.id !== tabId);
                                 if (this.activeTabId === tabId) {
                                     this.activeTabId = this.tabs.length > 0 ? this.tabs[0].id : null;
                                 }
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در حذف تب');
                         }
                     },
                     getActiveTab() {
                         return this.tabs.find(t => t.id === this.activeTabId);
                     },
                     canEditTab(tab) {
                         if (!tab) return false;
                         // Only admin can edit
                         return this.isAdmin;
                     }
                 }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">دستورکار هفته</h3>
                    @if($isAdmin)
                        <button @click="openAddTabModal()" 
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition text-sm">
                            + افزودن تب
                        </button>
                    @endif
                </div>
                
                <!-- Tabs Header -->
                <div class="border-b border-gray-200 mb-4" dir="rtl">
                    <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <style>
                            .scrollbar-hide::-webkit-scrollbar { display: none; }
                        </style>
                        <template x-for="tab in tabs" :key="tab.id">
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button @click="activeTabId = tab.id"
                                        :class="activeTabId === tab.id ? 'bg-primary text-white border-primary' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                        class="px-4 py-2 rounded-t-lg text-sm font-medium border-b-2 transition whitespace-nowrap">
                                    <span x-text="tab.name"></span>
                                </button>
                                @if($isAdmin)
                                    <button @click.stop="openEditTabModal(tab)"
                                            :class="activeTabId === tab.id ? 'text-dark hover:text-white hover:bg-white/20' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                                            class="p-1 rounded transition"
                                            title="تنظیمات تب">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </button>
                                    <button @click.stop="deleteTab(tab.id)"
                                            :class="activeTabId === tab.id ? 'text-dark hover:text-white hover:bg-white/20' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                                            class="p-1 rounded transition"
                                            title="حذف تب">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Tab Content -->
                <template x-for="tab in tabs" :key="tab.id">
                    <div x-show="activeTabId === tab.id" 
                         x-cloak
                         class="tab-content">
                        <div :id="`tab-content-${tab.id}`" 
                             class="min-h-[200px] bg-white" 
                             style="direction: rtl;"
                             x-show="canEditTab(tab)"></div>
                        <div x-show="!canEditTab(tab)"
                             class="min-h-[200px] p-4 bg-gray-50 rounded-lg border border-gray-200 prose max-w-none" 
                             style="direction: rtl;">
                            <template x-if="tab.content">
                                <div x-html="tab.content"></div>
                            </template>
                            <template x-if="!tab.content">
                                <p class="text-gray-500 text-center">محتوایی وجود ندارد</p>
                            </template>
                        </div>
                    </div>
                </template>
                
                <div x-show="tabs.length === 0" class="text-center py-8 text-gray-500">
                    <p>هیچ تبی وجود ندارد</p>
                    @if($isAdmin)
                        <button @click="openAddTabModal()" 
                                class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                            ایجاد تب جدید
                        </button>
                    @endif
                </div>
                
                <!-- Add Tab Modal -->
                <div x-show="showAddTabModal" 
                     x-cloak
                     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                     @click.self="showAddTabModal = false">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                        <h3 class="text-lg font-bold mb-4">افزودن تب جدید</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نام تب <span class="text-red-500">*</span></label>
                                <input type="text" x-model="newTab.name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="نام تب">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">کاربران دسترسی</label>
                                <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                                    <template x-for="user in allUsers" :key="user.id">
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                            <input type="checkbox" 
                                                   :value="user.id"
                                                   x-model="newTab.user_ids"
                                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <div class="flex-1">
                                                <span class="text-sm text-gray-700 font-medium" x-text="user.name"></span>
                                                <span x-show="user.mobile" class="text-xs text-gray-500 mr-2" x-text="user.mobile"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-6">
                            <button @click="addTab()" 
                                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                افزودن
                            </button>
                            <button @click="showAddTabModal = false" 
                                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                انصراف
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Tab Modal -->
                <div x-show="showEditTabModal" 
                     x-cloak
                     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                     @click.self="showEditTabModal = false">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                        <h3 class="text-lg font-bold mb-4">ویرایش تب</h3>
                        <template x-if="editingTab">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">نام تب <span class="text-red-500">*</span></label>
                                        <input type="text" 
                                               x-model="editingTabName"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                               placeholder="نام تب">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">کاربران دسترسی</label>
                                        <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                                            <template x-for="user in allUsers" :key="user.id">
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                                    <input type="checkbox" 
                                                           :value="user.id"
                                                           x-model="selectedUserIds"
                                                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                                    <div class="flex-1">
                                                        <span class="text-sm text-gray-700 font-medium" x-text="user.name"></span>
                                                        <span x-show="user.mobile" class="text-xs text-gray-500 mr-2" x-text="user.mobile"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mt-6">
                                    <button @click="updateTab()" 
                                            class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                        ذخیره
                                    </button>
                                    <button @click="showEditTabModal = false" 
                                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        انصراف
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Manage Users Modal -->
                <div x-show="showManageUsersModal" 
                     x-cloak
                     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                     @click.self="showManageUsersModal = false">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                        <h3 class="text-lg font-bold mb-4">مدیریت کاربران تب</h3>
                        <template x-if="editingTab">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">تب: <span class="font-semibold" x-text="editingTab.name"></span></p>
                                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                                    <template x-for="user in allUsers" :key="user.id">
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                            <input type="checkbox" 
                                                   :value="user.id"
                                                   x-model="selectedUserIds"
                                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <div class="flex-1">
                                                <span class="text-sm text-gray-700 font-medium" x-text="user.name"></span>
                                                <span x-show="user.mobile" class="text-xs text-gray-500 mr-2" x-text="user.mobile"></span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                                <div class="flex items-center gap-3 mt-6">
                                    <button @click="updateTabUsers()" 
                                            class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                        ذخیره
                                    </button>
                                    <button @click="showManageUsersModal = false" 
                                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        انصراف
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Welcome Card -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary/80 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">خوش آمدید، {{ Auth::user()->name }}!</h3>
                                <p class="text-sm text-gray-500">به پنل مدیریت کارمانیا پورتال خوش آمدید</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Stat Card 1 - Incomplete Tasks -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">تسک‌های تمام نشده</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($incompleteTasksCount) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Stat Card 2 - Unread Messages -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">پیام‌های خوانده نشده</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($unreadMessagesCount) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">فعالیت‌های اخیر</h3>
                        <div class="space-y-2">
                            @if($recentActivities->count() > 0)
                                @foreach($recentActivities as $activity)
                                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                                        <div class="flex-shrink-0">
                                            @if($activity['type'] === 'login')
                                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-6 h-6 bg-primary/10 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm text-gray-900 truncate">
                                                    <span class="font-medium">{{ $activity['title'] }}</span>
                                                    <span class="text-gray-500 mr-1">•</span>
                                                    <span class="text-gray-600">{{ Str::limit($activity['description'], 50) }}</span>
                                                </p>
                                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $activity['created_at']->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-6 text-gray-500">
                                    <p class="text-sm">هیچ فعالیتی وجود ندارد</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - My Tasks -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">وظایف من</h3>
                    <span class="text-sm text-gray-500">
                        {{ $tasksByProject->sum(function($project) { return $project['columns']->sum(function($col) { return $col['tasks']->count(); }); }) }} وظیفه ناتمام
                    </span>
                </div>
                
                @if($tasksByProject->count() > 0)
                    <div class="space-y-4">
                        @foreach($tasksByProject as $projectGroup)
                            <div>
                                <!-- Project Header -->
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-2 h-2 bg-primary rounded-full"></div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $projectGroup['project']['name'] }}</h4>
                                    <span class="text-xs text-gray-500">
                                        ({{ $projectGroup['columns']->sum(function($col) { return $col['tasks']->count(); }) }})
                                    </span>
                                </div>
                                
                                <!-- Columns -->
                                <div class="space-y-3 mr-4">
                                    @foreach($projectGroup['columns'] as $columnGroup)
                                        <div>
                                            <!-- Column Header -->
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <div class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background-color: {{ $columnGroup['column']['color'] }}"></div>
                                                <span class="text-xs font-medium text-gray-700">{{ $columnGroup['column']['name'] }}</span>
                                                <span class="text-xs text-gray-400">({{ $columnGroup['tasks']->count() }})</span>
                                            </div>
                                            
                                            <!-- Tasks List - Compact One Line -->
                                            <div class="space-y-1 mr-3">
                                                @foreach($columnGroup['tasks'] as $task)
                                                    <a href="{{ route('projects.show', $projectGroup['project']['id']) }}#tasks" 
                                                       class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 transition-colors group">
                                                        <span class="text-sm text-gray-900 flex-1 truncate">{{ $task['title'] }}</span>
                                                        <span class="text-xs px-1.5 py-0.5 rounded 
                                                            @if($task['priority'] === 'high') bg-red-100 text-red-700
                                                            @elseif($task['priority'] === 'medium') bg-yellow-100 text-yellow-700
                                                            @else bg-green-100 text-green-700
                                                            @endif">
                                                            @if($task['priority'] === 'high') زیاد
                                                            @elseif($task['priority'] === 'medium') متوسط
                                                            @else کم
                                                            @endif
                                                        </span>
                                                        @if($task['due_date_jalali'])
                                                            <span class="text-xs text-gray-500 flex-shrink-0">{{ $task['due_date_jalali'] }}</span>
                                                        @endif
                                                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-primary transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p class="text-sm">هیچ وظیفه ناتمامی ندارید</p>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Quill RTL Support */
        [id^="tab-content-"] .ql-editor {
            direction: rtl;
            text-align: right;
            min-height: 200px;
        }
        [id^="tab-content-"] .ql-toolbar {
            direction: rtl;
        }
        [id^="tab-content-"] .ql-container {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</x-app-layout>
