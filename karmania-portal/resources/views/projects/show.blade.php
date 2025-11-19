<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" dir="rtl">
            <div class="flex items-center gap-4">
                <a href="{{ route('projects.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    جزئیات پروژه
                </h2>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Edit Button -->
                <a href="{{ route('projects.edit', $project) }}" 
                   class="inline-flex items-center p-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all border border-primary"
                   title="ویرایش پروژه">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('projects.destroy', $project) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('آیا از حذف این پروژه اطمینان دارید؟ تمام اطلاعات مرتبط با آن حذف خواهد شد.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all border border-red-600"
                            title="حذف پروژه">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8" dir="rtl">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" 
                 x-data="{ 
                     activeTab: window.location.hash ? window.location.hash.substring(1) : 'tasks',
                     changeTab(tab) {
                         this.activeTab = tab;
                         window.location.hash = tab;
                     },
                     showCompleted: true,
                     tasks: {!! json_encode($project->tasks->map(function($task) {
                         return [
                             'id' => $task->id,
                             'title' => $task->title,
                             'description' => $task->description,
                             'is_completed' => $task->is_completed,
                             'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null,
                             'completed_at_shamsi' => $task->completed_at ? \App\Services\DateConverterService::gregorianToJalali($task->completed_at->format('Y-m-d')) : null,
                             'completed_by_name' => $task->completedBy ? $task->completedBy->name : null,
                         ];
                     })->values()) !!},
                     newTaskTitle: '',
                     newTaskDescription: '',
                     loading: false,
                     async toggleTask(taskId) {
                         this.loading = true;
                         try {
                             const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}/toggle`, {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                                 }
                             });
                             const data = await response.json();
                             if (data.success) {
                                 const task = this.tasks.find(t => t.id === taskId);
                                 if (task) {
                                     task.is_completed = data.task.is_completed;
                                     task.completed_at = data.task.completed_at;
                                     task.completed_at_shamsi = data.task.completed_at_shamsi;
                                     task.completed_by_name = data.task.completed_by ? data.task.completed_by.name : null;
                                 }
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در به‌روزرسانی وظیفه');
                         } finally {
                             this.loading = false;
                         }
                     },
                     async addTask() {
                         if (!this.newTaskTitle.trim()) return;
                         this.loading = true;
                         try {
                             const response = await fetch('/projects/{{ $project->id }}/tasks', {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                                 },
                                 body: JSON.stringify({
                                     title: this.newTaskTitle,
                                     description: this.newTaskDescription
                                 })
                             });
                             const data = await response.json();
                             if (data.success) {
                                 this.tasks.push({
                                     id: data.task.id,
                                     title: data.task.title,
                                     description: data.task.description,
                                     is_completed: false,
                                     completed_at: null,
                                     completed_at_shamsi: null,
                                     completed_by_name: null
                                 });
                                 this.newTaskTitle = '';
                                 this.newTaskDescription = '';
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در ایجاد وظیفه');
                         } finally {
                             this.loading = false;
                         }
                     },
                     async deleteTask(taskId) {
                         if (!confirm('آیا از حذف این وظیفه اطمینان دارید؟')) return;
                         this.loading = true;
                         try {
                             const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
                                 method: 'DELETE',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                                 }
                             });
                             const data = await response.json();
                             if (data.success) {
                                 this.tasks = this.tasks.filter(t => t.id !== taskId);
                             }
                         } catch (error) {
                             console.error('Error:', error);
                             alert('خطا در حذف وظیفه');
                         } finally {
                             this.loading = false;
                         }
                     },
                     get filteredTasks() {
                         if (this.showCompleted) {
                             return this.tasks;
                         }
                         return this.tasks.filter(t => !t.is_completed);
                     }
                 }"
                 x-init="
                     window.addEventListener('hashchange', () => {
                         if (window.location.hash) {
                             activeTab = window.location.hash.substring(1);
                         }
                     });
                 ">
                <!-- Header with Icon -->
                <div class="bg-primary py-3 px-8">
                    <div class="flex items-center justify-between gap-4" dir="rtl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ $project->name }}</h3>
                        </div>
                        @php
                            $statusLabels = [
                                'pending' => 'در انتظار',
                                'in_progress' => 'در حال انجام',
                                'completed' => 'تکمیل شده',
                                'cancelled' => 'لغو شده',
                            ];
                        @endphp
                        <div class="text-white/90 text-sm whitespace-nowrap">{{ $statusLabels[$project->status] ?? $project->status }} - ایجاد شده در {{ $project->created_at->format('Y/m/d') }}</div>
                    </div>
                </div>

                <!-- Tabs Header -->
                <div class="px-6 pt-4 border-b border-gray-200" dir="rtl">
                    <div class="flex flex-wrap gap-2">
                        <button @click="changeTab('tasks')"
                                :class="activeTab === 'tasks' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            وظایف پروژه
                        </button>
                        <button @click="changeTab('chat')"
                                :class="activeTab === 'chat' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            چت گروهی
                        </button>
                        <button @click="changeTab('files')"
                                :class="activeTab === 'files' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            فایل های پروژه
                        </button>
                        <button @click="changeTab('contracts')"
                                :class="activeTab === 'contracts' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition">
                            قراردادها و فاکتورها
                        </button>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div class="p-6" x-show="activeTab === 'tasks'" x-cloak>
                    <!-- Header with Toggle -->
                    <div class="flex items-center justify-between mb-6" dir="rtl">
                        <h4 class="text-lg font-semibold text-gray-800">وظایف پروژه</h4>
                        <label class="flex items-center gap-2 cursor-pointer" dir="rtl">
                            <span class="text-sm text-gray-600">نمایش کارهای تمام شده</span>
                            <div class="relative">
                                <input type="checkbox" x-model="showCompleted" class="sr-only">
                                <div class="w-11 h-6 bg-gray-200 rounded-full transition-colors duration-200"
                                     :class="showCompleted ? 'bg-primary' : 'bg-gray-300'">
                                    <div class="w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-200 mt-0.5"
                                         :class="showCompleted ? 'translate-x-5 mr-0.5' : 'translate-x-0.5'">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Add New Task Form -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200" dir="rtl">
                        <div class="space-y-3">
                            <input type="text" 
                                   x-model="newTaskTitle"
                                   @keyup.enter="addTask()"
                                   placeholder="عنوان وظیفه جدید..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <textarea x-model="newTaskDescription"
                                      placeholder="توضیحات (اختیاری)..."
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                            <button @click="addTask()"
                                    :disabled="loading || !newTaskTitle.trim()"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">افزودن وظیفه</span>
                                <span x-show="loading">در حال افزودن...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tasks List -->
                    <div class="space-y-3" dir="rtl">
                        <template x-if="filteredTasks.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                <p>هیچ وظیفه‌ای وجود ندارد.</p>
                            </div>
                        </template>
                        <template x-for="task in filteredTasks" :key="task.id">
                            <div class="flex items-start gap-3 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                                 :class="task.is_completed ? 'bg-gray-50 opacity-75' : ''">
                                <!-- Checkbox -->
                                <input type="checkbox"
                                       :checked="task.is_completed"
                                       @change="toggleTask(task.id)"
                                       :disabled="loading"
                                       class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                                
                                <!-- Task Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-800 mb-1"
                                                :class="task.is_completed ? 'line-through text-gray-500' : ''"
                                                x-text="task.title"></h5>
                                            <p class="text-sm text-gray-600 mb-2"
                                               :class="task.is_completed ? 'line-through' : ''"
                                               x-show="task.description"
                                               x-text="task.description"></p>
                                            
                                            <!-- Completion Info -->
                                            <div class="flex items-center gap-4 text-xs text-gray-500 mt-2"
                                                 x-show="task.is_completed && task.completed_at_shamsi">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span x-text="task.completed_at_shamsi"></span>
                                                </span>
                                                <span class="flex items-center gap-1" x-show="task.completed_by_name">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    <span x-text="task.completed_by_name"></span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Button -->
                                        <button @click="deleteTask(task.id)"
                                                :disabled="loading"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition disabled:opacity-50"
                                                title="حذف وظیفه">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Group Chat Tab -->
                <div class="p-6" x-show="activeTab === 'chat'" x-cloak>
                    <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
                        این بخش به زودی تکمیل می‌شود.
                    </div>
                </div>

                <!-- Files Tab -->
                <div class="p-6" x-show="activeTab === 'files'" x-cloak>
                    <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
                        این بخش به زودی تکمیل می‌شود.
                    </div>
                </div>

                <!-- Contracts and Invoices Tab -->
                <div class="p-6" x-show="activeTab === 'contracts'" x-cloak>
                    <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
                        این بخش به زودی تکمیل می‌شود.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

