<x-app-layout>
    <div class="py-8" dir="rtl">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <!-- Page Actions -->
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('projects.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>بازگشت به لیست پروژه‌ها</span>
                </a>
            
            <div class="flex items-center gap-2">
                <!-- Edit Button -->
                <a href="{{ route('projects.edit', $project) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                        <span>ویرایش پروژه</span>
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('projects.destroy', $project) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('آیا از حذف این پروژه اطمینان دارید؟ تمام اطلاعات مرتبط با آن حذف خواهد شد.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                            <span>حذف پروژه</span>
                    </button>
                </form>
            </div>
        </div>
            
            @php
                $chatData = [
                    'projectId' => $project->id,
                    'currentUserId' => Auth::id(),
                    'projectUsers' => $projectUsers->map(function($user) {
                        return ['id' => $user->id, 'name' => $user->name];
                    })->toArray(),
                    'messagesUrl' => route('projects.chat.messages', $project),
                    'sendUrl' => route('projects.chat.send', $project),
                    'csrfToken' => csrf_token(),
                ];
            @endphp
            <script>
                window.projectChatConfig = @json($chatData);
            </script>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" 
                 x-data="{ 
                     activeTab: window.location.hash ? window.location.hash.substring(1) : 'tasks',
                     changeTab(tab) {
                         this.activeTab = tab;
                         window.location.hash = tab;
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
                <div class="px-4 sm:px-6 pt-4 border-b border-gray-200" dir="rtl">
                    <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2 -mx-4 sm:-mx-6 px-4 sm:px-6" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <style>
                            .scrollbar-hide::-webkit-scrollbar {
                                display: none;
                            }
                        </style>
                        <button @click="changeTab('tasks')"
                                :class="activeTab === 'tasks' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition flex-shrink-0 whitespace-nowrap">
                            وظایف پروژه
                        </button>
                        <button @click="changeTab('chat')"
                                :class="activeTab === 'chat' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition flex-shrink-0 whitespace-nowrap">
                            چت گروهی
                        </button>
                        <button @click="changeTab('files')"
                                :class="activeTab === 'files' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition flex-shrink-0 whitespace-nowrap">
                            فایل های پروژه
                        </button>
                        <button @click="changeTab('accounts')"
                                :class="activeTab === 'accounts' ? 'bg-primary/10 text-primary border-primary/20' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-medium border transition flex-shrink-0 whitespace-nowrap">
                            اکانت‌ها
                        </button>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div class="p-6" x-show="activeTab === 'tasks'" x-cloak
                     x-data="{
                         columns: @js($project->columns->map(function($col) {
                             return [
                                 'id' => $col->id,
                                 'name' => $col->name,
                                 'color' => $col->color,
                                 'order' => $col->order,
                                 'tasks' => $col->tasks->map(function($task) {
                                     return [
                                         'id' => $task->id,
                                         'title' => $task->title,
                                         'description' => $task->description,
                                         'due_date_jalali' => $task->due_date_jalali,
                                         'priority' => $task->priority,
                                         'project_column_id' => $task->project_column_id,
                                         'is_completed' => $task->is_completed,
                                         'completed_at_shamsi' => $task->completed_at ? \App\Services\DateConverterService::gregorianToJalali($task->completed_at->format('Y-m-d')) : null,
                                         'completed_by_name' => $task->completedBy ? $task->completedBy->name : null,
                                         'assigned_users' => $task->assignedUsers->map(function($user) {
                                             return ['id' => $user->id, 'name' => $user->name];
                                         })->toArray(),
                                         'order' => $task->order,
                                     ];
                                 })->sortBy('order')->values(),
                             ];
                         })->sortBy('order')->values()),
                         projectUsers: @js($projectUsers->map(function($user) {
                             return ['id' => $user->id, 'name' => $user->name];
                         })),
                         currentUserId: @js(Auth::id()),
                         showAddColumnModal: false,
                         showAddTaskModal: false,
                         showEditTaskModal: false,
                         showEditColumnModal: false,
                         showCompleted: false,
                         newColumnName: '',
                         newColumnColor: '#6366f1',
                         selectedColumnId: null,
                         editingTask: null,
                         editingColumn: null,
                         loading: false,
                         newTask: {
                             title: '',
                             description: '',
                             due_date_jalali: '',
                             priority: 'medium',
                             assigned_user_ids: [],
                         },
                         async addColumn() {
                             if (!this.newColumnName.trim()) return;
                             
                             try {
                                 const response = await fetch('{{ route('projects.columns.store', $project) }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         name: this.newColumnName,
                                         color: this.newColumnColor,
                                     }),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     this.columns.push({
                                         id: data.column.id,
                                         name: data.column.name,
                                         color: data.column.color,
                                         order: data.column.order,
                                         tasks: [],
                                     });
                                     this.newColumnName = '';
                                     this.newColumnColor = '#6366f1';
                                     this.showAddColumnModal = false;
                                     this.$nextTick(() => {
                                         this.initSortable();
                                     });
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در افزودن ستون');
                             }
                         },
                         async deleteColumn(columnId) {
                             if (!confirm('آیا از حذف این ستون اطمینان دارید؟ تمام تسک‌های آن نیز حذف خواهند شد.')) return;
                             
                             const column = this.columns.find(c => c.id === columnId);
                             if (!column) return;
                             
                             try {
                                 const response = await fetch(`{{ route('projects.columns.destroy', [$project, ':column']) }}`.replace(':column', columnId), {
                                     method: 'DELETE',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     // Destroy sortable instance for deleted column
                                     if (this.sortableInstances[columnId]) {
                                         this.sortableInstances[columnId].destroy();
                                         delete this.sortableInstances[columnId];
                                     }
                                     this.columns = this.columns.filter(c => c.id !== columnId);
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در حذف ستون');
                             }
                         },
                         openAddTaskModal(columnId) {
                             this.selectedColumnId = columnId;
                             // Check if current user is in projectUsers and auto-select them
                             const currentUserInProject = this.projectUsers.find(u => u.id === this.currentUserId);
                             this.newTask = {
                                 title: '',
                                 description: '',
                                 due_date_jalali: '',
                                 priority: 'medium',
                                 assigned_user_ids: currentUserInProject ? [this.currentUserId] : [],
                             };
                             this.showAddTaskModal = true;
                             this.$nextTick(() => {
                                 setTimeout(() => {
                                     this.initNewTaskDatepicker();
                                 }, 100);
                             });
                         },
                         convertPersianToEnglish(str) {
                             const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                             const englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                             let result = str;
                             for (let i = 0; i < persianDigits.length; i++) {
                                 result = result.replace(new RegExp(persianDigits[i], 'g'), englishDigits[i]);
                             }
                             return result;
                         },
                         initNewTaskDatepicker() {
                             const $input = $('#new_task_due_date');
                             if ($input.length && typeof $.fn.persianDatepicker !== 'undefined') {
                                 if ($input.data('persianDatepicker')) {
                                     $input.persianDatepicker('destroy');
                                 }
                                 $input.persianDatepicker({
                                     initialValue: false,
                                     format: 'YYYY/MM/DD',
                                     autoClose: true,
                                     calendar: {
                                         persian: {
                                             locale: 'fa',
                                             digits: 'en'
                                         }
                                     },
                                     observer: true,
                                     altField: '#new_task_due_date',
                                     altFormat: 'YYYY/MM/DD',
                                     onSelect: () => {
                                         const value = $input.val();
                                         const englishValue = this.convertPersianToEnglish(value);
                                         $input.val(englishValue);
                                         this.newTask.due_date_jalali = englishValue;
                                     }
                                 });
                                 $input.off('change.persianDatepicker').on('change.persianDatepicker', () => {
                                     const value = $input.val();
                                     const englishValue = this.convertPersianToEnglish(value);
                                     $input.val(englishValue);
                                     this.newTask.due_date_jalali = englishValue;
                                 });
                             }
                         },
                         async addTask() {
                             if (!this.newTask.title.trim()) return;
                             
                             const column = this.columns.find(c => c.id === this.selectedColumnId);
                             if (!column) return;
                             
                             try {
                                 const response = await fetch(`{{ route('columns.tasks.store', [':column']) }}`.replace(':column', this.selectedColumnId), {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify(this.newTask),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     column.tasks.push({
                                         id: data.task.id,
                                         title: data.task.title,
                                         description: data.task.description,
                                         due_date_jalali: data.task.due_date_jalali,
                                         priority: data.task.priority,
                                         project_column_id: data.task.project_column_id,
                                         assigned_users: data.task.assigned_users || [],
                                         order: data.task.order,
                                     });
                                     // Destroy datepicker before closing modal
                                     const $input = $('#new_task_due_date');
                                     if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                         $input.persianDatepicker('destroy');
                                     }
                                     this.showAddTaskModal = false;
                                     this.newTask = {
                                         title: '',
                                         description: '',
                                         due_date_jalali: '',
                                         priority: 'medium',
                                         assigned_user_ids: [],
                                     };
                                     this.$nextTick(() => {
                                         this.initSortable();
                                     });
                                 } else {
                                     alert(data.message || 'خطا در افزودن تسک');
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در افزودن تسک');
                             }
                         },
                         openEditTaskModal(task, columnId) {
                             this.editingTask = { 
                                 ...task, 
                                 columnId: columnId, 
                                 project_column_id: task.project_column_id || columnId,
                                 assigned_user_ids: task.assigned_users ? task.assigned_users.map(u => u.id) : []
                             };
                             this.showEditTaskModal = true;
                             this.$nextTick(() => {
                                 setTimeout(() => {
                                     this.initEditTaskDatepicker();
                                 }, 100);
                             });
                         },
                         initEditTaskDatepicker() {
                             const $input = $('#edit_task_due_date');
                             if ($input.length && typeof $.fn.persianDatepicker !== 'undefined') {
                                 if ($input.data('persianDatepicker')) {
                                     $input.persianDatepicker('destroy');
                                 }
                                 // Set initial value if exists
                                 if (this.editingTask && this.editingTask.due_date_jalali) {
                                     const englishValue = this.convertPersianToEnglish(this.editingTask.due_date_jalali);
                                     $input.val(englishValue);
                                 }
                                 $input.persianDatepicker({
                                     initialValue: false,
                                     format: 'YYYY/MM/DD',
                                     autoClose: true,
                                     calendar: {
                                         persian: {
                                             locale: 'fa',
                                             digits: 'en'
                                         }
                                     },
                                     observer: true,
                                     altField: '#edit_task_due_date',
                                     altFormat: 'YYYY/MM/DD',
                                     onSelect: () => {
                                         const value = $input.val();
                                         const englishValue = this.convertPersianToEnglish(value);
                                         $input.val(englishValue);
                                         if (this.editingTask) {
                                             this.editingTask.due_date_jalali = englishValue;
                                         }
                                     }
                                 });
                                 $input.off('change.persianDatepicker').on('change.persianDatepicker', () => {
                                     const value = $input.val();
                                     const englishValue = this.convertPersianToEnglish(value);
                                     $input.val(englishValue);
                                     if (this.editingTask) {
                                         this.editingTask.due_date_jalali = englishValue;
                                     }
                                 });
                             }
                         },
                         async updateTask() {
                             if (!this.editingTask.title.trim()) return;
                             
                             try {
                                 const response = await fetch(`{{ route('tasks.update', [':task']) }}`.replace(':task', this.editingTask.id), {
                                     method: 'PUT',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         title: this.editingTask.title,
                                         description: this.editingTask.description,
                                         due_date_jalali: this.editingTask.due_date_jalali,
                                         priority: this.editingTask.priority,
                                         assigned_user_ids: this.editingTask.assigned_user_ids || [],
                                         project_column_id: this.editingTask.project_column_id || this.editingTask.columnId,
                                     }),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     const column = this.columns.find(c => c.id === this.editingTask.columnId);
                                     if (column) {
                                         const taskIndex = column.tasks.findIndex(t => t.id === this.editingTask.id);
                                         if (taskIndex !== -1) {
                                             column.tasks[taskIndex] = {
                                                 id: data.task.id,
                                                 title: data.task.title,
                                                 description: data.task.description,
                                                 due_date_jalali: data.task.due_date_jalali,
                                                 priority: data.task.priority,
                                                 project_column_id: data.task.project_column_id,
                                                 assigned_users: data.task.assigned_users || [],
                                                 order: data.task.order,
                                             };
                                         }
                                     }
                                     // Destroy datepicker before closing modal
                                     const $input = $('#edit_task_due_date');
                                     if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                         $input.persianDatepicker('destroy');
                                     }
                                     this.showEditTaskModal = false;
                                     this.editingTask = null;
                                 } else {
                                     alert(data.message || 'خطا در به‌روزرسانی تسک');
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در به‌روزرسانی تسک');
                             }
                         },
                         async deleteTask(taskId, columnId) {
                             if (!confirm('آیا از حذف این تسک اطمینان دارید؟')) return;
                             
                             try {
                                 const response = await fetch(`{{ route('tasks.destroy', [':task']) }}`.replace(':task', taskId), {
                                     method: 'DELETE',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     const column = this.columns.find(c => c.id === columnId);
                                     if (column) {
                                         column.tasks = column.tasks.filter(t => t.id !== taskId);
                                     }
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در حذف تسک');
                             }
                         },
                         openEditColumnModal(column) {
                             this.editingColumn = { ...column };
                             this.showEditColumnModal = true;
                         },
                         async updateColumn() {
                             if (!this.editingColumn.name.trim()) return;
                             
                             try {
                                 const response = await fetch(`{{ route('projects.columns.update', [$project, ':column']) }}`.replace(':column', this.editingColumn.id), {
                                     method: 'PUT',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         name: this.editingColumn.name,
                                         color: this.editingColumn.color,
                                     }),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     const column = this.columns.find(c => c.id === this.editingColumn.id);
                                     if (column) {
                                         column.name = data.column.name;
                                         column.color = data.column.color;
                                     }
                                     this.showEditColumnModal = false;
                                     this.editingColumn = null;
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در به‌روزرسانی ستون');
                             }
                         },
                         getPriorityColor(priority) {
                             const colors = {
                                 'low': 'bg-green-100 text-green-800',
                                 'medium': 'bg-yellow-100 text-yellow-800',
                                 'high': 'bg-red-100 text-red-800',
                             };
                             return colors[priority] || colors.medium;
                         },
                         getPriorityLabel(priority) {
                             const labels = {
                                 'low': 'کم',
                                 'medium': 'متوسط',
                                 'high': 'زیاد',
                             };
                             return labels[priority] || labels.medium;
                         },
                         sortableInstances: {},
                         initSortable() {
                             this.$nextTick(() => {
                                 this.columns.forEach(column => {
                                     const tasksContainer = document.getElementById(`tasks-column-${column.id}`);
                                     if (tasksContainer && typeof Sortable !== 'undefined') {
                                         // Destroy existing instance if any
                                         if (this.sortableInstances[column.id]) {
                                             this.sortableInstances[column.id].destroy();
                                         }
                                         
                                         this.sortableInstances[column.id] = new Sortable(tasksContainer, {
                                             group: 'tasks',
                                             animation: 150,
                                             handle: '.task-drag-handle',
                                             ghostClass: 'opacity-50',
                                             chosenClass: 'shadow-lg',
                                             dragClass: 'cursor-grabbing',
                                             onEnd: (evt) => {
                                                 this.handleTaskMove(evt, column.id);
                                             }
                                         });
                                     }
                                 });
                             });
                         },
                         async handleTaskMove(evt, currentColumnId) {
                             const taskId = parseInt(evt.item.dataset.taskId);
                             const newColumnId = parseInt(evt.to.dataset.columnId);
                             const oldColumnId = parseInt(evt.from.dataset.columnId);
                             
                             // Find the task
                             let task = null;
                             let sourceColumn = this.columns.find(c => c.id === oldColumnId);
                             if (sourceColumn) {
                                 task = sourceColumn.tasks.find(t => t.id === taskId);
                             }
                             
                             if (!task) return;
                             
                             // Update order for all tasks in the new column
                             const newColumn = this.columns.find(c => c.id === newColumnId);
                             if (!newColumn) return;
                             
                             const tasksToUpdate = [];
                             const taskElements = evt.to.querySelectorAll('[data-task-id]');
                             
                             taskElements.forEach((element, index) => {
                                 const id = parseInt(element.dataset.taskId);
                                 tasksToUpdate.push({
                                     id: id,
                                     order: index,
                                     project_column_id: newColumnId
                                 });
                             });
                             
                             // If task moved to different column, update source column too
                             if (oldColumnId !== newColumnId) {
                                 const sourceTaskElements = evt.from.querySelectorAll('[data-task-id]');
                                 sourceTaskElements.forEach((element, index) => {
                                     const id = parseInt(element.dataset.taskId);
                                     tasksToUpdate.push({
                                         id: id,
                                         order: index,
                                         project_column_id: oldColumnId
                                     });
                                 });
                             }
                             
                             try {
                                 const response = await fetch('{{ route('tasks.reorder') }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         tasks: tasksToUpdate
                                     }),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     // Update local state
                                     if (oldColumnId !== newColumnId) {
                                         // Remove from old column
                                         if (sourceColumn) {
                                             sourceColumn.tasks = sourceColumn.tasks.filter(t => t.id !== taskId);
                                         }
                                         // Add to new column
                                         task.project_column_id = newColumnId;
                                         newColumn.tasks.push(task);
                                     }
                                     
                                     // Update orders
                                     tasksToUpdate.forEach(update => {
                                         const taskToUpdate = this.findTaskById(update.id);
                                         if (taskToUpdate) {
                                             taskToUpdate.order = update.order;
                                         }
                                     });
                                     
                                     // Re-sort tasks in both columns
                                     if (sourceColumn) {
                                         sourceColumn.tasks.sort((a, b) => a.order - b.order);
                                     }
                                     newColumn.tasks.sort((a, b) => a.order - b.order);
                                 } else {
                                     // Revert on error
                                     location.reload();
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 location.reload();
                             }
                         },
                         findTaskById(taskId) {
                             for (const column of this.columns) {
                                 const task = column.tasks.find(t => t.id === taskId);
                                 if (task) return task;
                             }
                             return null;
                         },
                         async toggleTask(taskId) {
                             this.loading = true;
                             try {
                                 const response = await fetch(`{{ route('tasks.toggle', [':task']) }}`.replace(':task', taskId), {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     }
                                 });
                                 const data = await response.json();
                                 if (data.success) {
                                     const task = this.findTaskById(taskId);
                                     if (task) {
                                         task.is_completed = data.task.is_completed;
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
                         getFilteredTasks(column) {
                             if (this.showCompleted) {
                                 // نمایش همه تسک‌ها: ابتدا ناتمام، سپس تمام شده
                                 const incomplete = column.tasks.filter(t => !t.is_completed);
                                 const completed = column.tasks.filter(t => t.is_completed);
                                 return [...incomplete, ...completed];
                             }
                             return column.tasks.filter(t => !t.is_completed);
                         },
                         $watch: {
                             'columns.length'() {
                                 this.initSortable();
                             }
                         },
                     }"
                     x-init="
                         initSortable();
                     ">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">وظایف پروژه</h3>
                        <div class="flex items-center gap-4">
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
                        <button @click="showAddColumnModal = true" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            افزودن ستون جدید
                        </button>
                        </div>
                    </div>

                    <!-- Kanban Board -->
                    <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
                        <template x-for="column in columns" :key="column.id">
                            <div class="flex-shrink-0 w-80 bg-gray-50 rounded-lg p-4" :style="`border-top: 4px solid ${column.color}`">
                                <!-- Column Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full" :style="`background-color: ${column.color}`"></div>
                                        <h4 class="font-semibold text-gray-900" x-text="column.name"></h4>
                                        <span class="text-sm text-gray-500" x-text="`(${column.tasks.length})`"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button @click="openEditColumnModal(column)" 
                                                class="p-1 text-gray-500 hover:text-gray-700 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="deleteColumn(column.id)" 
                                                class="p-1 text-gray-500 hover:text-red-600 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Tasks List -->
                                <div id="tasks-column-:columnId" 
                                     :data-column-id="column.id"
                                     class="space-y-3 mb-4 min-h-[50px]"
                                     x-bind:id="`tasks-column-${column.id}`">
                                    <template x-for="task in getFilteredTasks(column)" :key="task.id">
                                        <div :data-task-id="task.id"
                                             class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border border-gray-200 task-item"
                                             :class="task.is_completed ? 'bg-gray-50 opacity-75' : ''">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex items-center gap-2 flex-1">
                                                    <input type="checkbox"
                                                           :checked="task.is_completed"
                                                           @change.stop="toggleTask(task.id)"
                                                           :disabled="loading"
                                                           class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer flex-shrink-0">
                                                    <div class="task-drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-900 flex-1 cursor-pointer"
                                                        :class="task.is_completed ? 'line-through text-gray-500' : ''"
                                                        @click="openEditTaskModal(task, column.id)"
                                                        x-text="task.title"></h5>
                                                </div>
                                                <button @click.stop="deleteTask(task.id, column.id)" 
                                                        class="p-1 text-gray-400 hover:text-red-600 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p x-show="task.description" 
                                               class="text-sm text-gray-600 mb-2 cursor-pointer"
                                               :class="task.is_completed ? 'line-through' : ''"
                                               @click="openEditTaskModal(task, column.id)"
                                               x-text="task.description"></p>
                                            <div class="flex items-center justify-between mt-3">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-xs px-2 py-1 rounded-full" 
                                                          :class="getPriorityColor(task.priority)" 
                                                          x-text="getPriorityLabel(task.priority)"></span>
                                                    <span x-show="task.due_date_jalali" class="text-xs text-gray-500" x-text="task.due_date_jalali"></span>
                                                    
                                                    <!-- Completion Info -->
                                                    <div class="flex items-center gap-3 text-xs text-gray-500"
                                                         x-show="task.is_completed && task.completed_at_shamsi">
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <span x-text="task.completed_at_shamsi"></span>
                                                        </span>
                                                        <span class="flex items-center gap-1" x-show="task.completed_by_name">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            <span x-text="task.completed_by_name"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div x-show="task.assigned_users && task.assigned_users.length > 0" class="flex items-center gap-1">
                                                    <template x-for="(user, index) in task.assigned_users.slice(0, 3)" :key="user.id">
                                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center -mr-2 border-2 border-white" 
                                                             :title="user.name"
                                                             :style="`z-index: ${10 - index}`">
                                                            <span class="text-xs text-white font-bold" x-text="user.name.charAt(0)"></span>
                                                        </div>
                                                    </template>
                                                    <div x-show="task.assigned_users.length > 3" 
                                                         class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center border-2 border-white text-xs text-white font-bold"
                                                         :title="task.assigned_users.slice(3).map(u => u.name).join(', ')">
                                                        <span x-text="`+${task.assigned_users.length - 3}`"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Add Task Button -->
                                <button @click="openAddTaskModal(column.id)" 
                                        class="w-full py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 transition-all">
                                    + افزودن تسک
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Add Column Modal -->
                    <div x-show="showAddColumnModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="showAddColumnModal = false">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <h3 class="text-lg font-bold mb-4">افزودن ستون جدید</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نام ستون</label>
                                    <input type="text" x-model="newColumnName" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="مثال: در حال انجام">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ</label>
                                    <input type="color" x-model="newColumnColor" 
                                           :value="newColumnColor || '#6366f1'"
                                           class="w-full h-10 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-6">
                                <button @click="addColumn()" 
                                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                    افزودن
                                </button>
                                <button @click="showAddColumnModal = false" 
                                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    انصراف
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Task Modal -->
                    <div x-show="showAddTaskModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="
                             const $input = $('#new_task_due_date');
                             if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                 $input.persianDatepicker('destroy');
                             }
                             showAddTaskModal = false;
                         ">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <h3 class="text-lg font-bold mb-4">افزودن تسک جدید</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان تسک <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="newTask.title" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="عنوان تسک">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                                    <textarea x-model="newTask.description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                              placeholder="توضیحات تسک"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تاریخ سررسید (شمسی)</label>
                                    <input type="text" 
                                           id="new_task_due_date"
                                           x-model="newTask.due_date_jalali" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="مثال: 1403/09/01">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                                    <select x-model="newTask.priority" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <option value="low">کم</option>
                                        <option value="medium">متوسط</option>
                                        <option value="high">زیاد</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">واگذار شده به</label>
                                    <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                                        <template x-for="user in projectUsers" :key="user.id">
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                                <input type="checkbox" 
                                                       :value="user.id"
                                                       x-model="newTask.assigned_user_ids"
                                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                                <span class="text-sm text-gray-700" x-text="user.name"></span>
                                            </label>
                                        </template>
                                        <p x-show="projectUsers.length === 0" class="text-sm text-gray-500 text-center py-2">
                                            کاربری برای انتخاب وجود ندارد
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-6">
                                <button @click="addTask()" 
                                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                    افزودن
                                </button>
                                <button @click="
                                    const $input = $('#new_task_due_date');
                                    if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                        $input.persianDatepicker('destroy');
                                    }
                                    showAddTaskModal = false;
                                " 
                                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    انصراف
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Task Modal -->
                    <div x-show="showEditTaskModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="
                             const $input = $('#edit_task_due_date');
                             if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                 $input.persianDatepicker('destroy');
                             }
                             showEditTaskModal = false;
                         ">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <template x-if="editingTask">
                                <div>
                                    <h3 class="text-lg font-bold mb-4">ویرایش تسک</h3>
                                    <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان تسک <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="editingTask.title" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                                    <textarea x-model="editingTask.description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تاریخ سررسید (شمسی)</label>
                                    <input type="text" 
                                           id="edit_task_due_date"
                                           x-model="editingTask.due_date_jalali" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="مثال: 1403/09/01">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                                    <select x-model="editingTask.priority" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <option value="low">کم</option>
                                        <option value="medium">متوسط</option>
                                        <option value="high">زیاد</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">واگذار شده به</label>
                                    <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                                        <template x-for="user in projectUsers" :key="user.id">
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                                <input type="checkbox" 
                                                       :value="user.id"
                                                       x-model="editingTask.assigned_user_ids"
                                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                                <span class="text-sm text-gray-700" x-text="user.name"></span>
                                            </label>
                                        </template>
                                        <p x-show="projectUsers.length === 0" class="text-sm text-gray-500 text-center py-2">
                                            کاربری برای انتخاب وجود ندارد
                                        </p>
                                    </div>
                                </div>
                                    </div>
                                    <div class="flex items-center gap-3 mt-6">
                                        <button @click="updateTask()" 
                                                class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                            ذخیره
                                        </button>
                                        <button @click="
                                            const $input = $('#edit_task_due_date');
                                            if ($input.length && typeof $.fn.persianDatepicker !== 'undefined' && $input.data('persianDatepicker')) {
                                                $input.persianDatepicker('destroy');
                                            }
                                            showEditTaskModal = false;
                                        " 
                                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                            انصراف
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Edit Column Modal -->
                    <div x-show="showEditColumnModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="showEditColumnModal = false">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <template x-if="editingColumn">
                                <div>
                                    <h3 class="text-lg font-bold mb-4">ویرایش ستون</h3>
                                    <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نام ستون</label>
                                    <input type="text" x-model="editingColumn.name" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ</label>
                                    <input type="color" x-model="editingColumn.color" 
                                           :value="editingColumn.color || '#6366f1'"
                                           class="w-full h-10 border border-gray-300 rounded-lg">
                                </div>
                                    </div>
                                    <div class="flex items-center gap-3 mt-6">
                                        <button @click="updateColumn()" 
                                                class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                            ذخیره
                                        </button>
                                        <button @click="showEditColumnModal = false" 
                                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                            انصراف
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Group Chat Tab -->
                <div class="p-6" x-show="activeTab === 'chat'" x-cloak
                     x-data="projectChatData(window.projectChatConfig)"
                     x-init="
                         this.fileInput = $refs.fileInput;
                         setTimeout(() => {
                             this.scrollToBottom();
                         }, 100);
                     ">
                    <!-- Chat Container -->
                    <div class="flex flex-col h-[600px] bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Chat Header -->
                        <div class="bg-primary text-white px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="text-lg font-bold">چت گروهی پروژه</h3>
                            </div>
                            <div class="text-sm opacity-90">
                                <span x-text="projectUsers.length"></span> عضو
                    </div>
                </div>

                        <!-- Messages Container -->
                        <div id="chat-messages-container" 
                             class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50"
                             style="direction: rtl;">
                            <template x-if="loading && messages.length === 0">
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-gray-500">در حال بارگذاری...</div>
                    </div>
                            </template>
                            
                            <template x-if="!loading && messages.length === 0">
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <p>هنوز پیامی ارسال نشده است</p>
                                        <p class="text-sm mt-2">اولین پیام را ارسال کنید</p>
                </div>
                                </div>
                            </template>
                            
                            <template x-for="(message, index) in messages" :key="message.id">
                                <div class="flex items-end gap-2" :class="isMyMessage(message) ? 'justify-end' : 'justify-start'">
                                    <!-- Avatar -->
                                    <div x-show="!isMyMessage(message)" class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-primary/20 flex items-center justify-center">
                                            <img x-show="message.sender_avatar" 
                                                 :src="message.sender_avatar" 
                                                 :alt="getUserName(message.sender_id)"
                                                 class="w-full h-full object-cover">
                                            <span x-show="!message.sender_avatar" 
                                                  class="text-xs font-semibold text-primary"
                                                  x-text="getUserName(message.sender_id).charAt(0)"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Message Content -->
                                    <div class="max-w-[70%]"
                                         :class="isMyMessage(message) ? 'bg-primary text-white rounded-lg rounded-tr-none' : 'bg-white text-gray-900 rounded-lg rounded-tl-none border border-gray-200'">
                                        <!-- Message Header -->
                                        <div class="px-4 pt-3 pb-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-semibold" 
                                                      :class="isMyMessage(message) ? 'text-white/90' : 'text-gray-600'"
                                                      x-text="getUserName(message.sender_id)"></span>
                                                <span class="text-xs" 
                                                      :class="isMyMessage(message) ? 'text-white/70' : 'text-gray-400'"
                                                      x-text="message.created_at_formatted"></span>
                                            </div>
                                            
                                            <!-- Text Message -->
                                            <div x-show="message.type === 'text' && message.body" 
                                                 class="text-sm mb-2"
                                                 :class="isMyMessage(message) ? 'text-white' : 'text-gray-900'"
                                                 x-text="message.body"></div>
                                            
                                            <!-- Voice Message -->
                                            <div x-show="message.type === 'voice'" class="mb-2">
                                                <audio controls class="w-full" 
                                                       x-show="message.attachments && message.attachments.length > 0">
                                                    <source :src="message.attachments[0].url" type="audio/webm">
                                                    مرورگر شما از پخش صدا پشتیبانی نمی‌کند.
                                                </audio>
                                            </div>
                                            
                                            <!-- File Attachments -->
                                            <template x-if="message.type === 'file' && message.attachments && message.attachments.length > 0">
                                                <div class="space-y-2 mb-2">
                                                    <template x-for="attachment in message.attachments" :key="attachment.id">
                                                        <div class="flex items-center gap-2 p-2 rounded"
                                                             :class="isMyMessage(message) ? 'bg-white/20' : 'bg-gray-100'">
                                                            <svg class="w-5 h-5 flex-shrink-0" 
                                                                 :class="isMyMessage(message) ? 'text-white' : 'text-gray-600'"
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <a :href="attachment.url" 
                                                                   target="_blank"
                                                                   class="text-sm font-medium block truncate hover:underline"
                                                                   :class="isMyMessage(message) ? 'text-white' : 'text-gray-900'"
                                                                   :title="attachment.original_name"
                                                                   x-text="attachment.original_name"></a>
                                                                <span class="text-xs"
                                                                      :class="isMyMessage(message) ? 'text-white/70' : 'text-gray-500'"
                                                                      x-text="formatFileSize(attachment.file_size)"></span>
                                                            </div>
                                                            <a :href="'{{ route('projects.chat.download-attachment', ':id') }}'.replace(':id', attachment.id)"
                                                               class="p-1 rounded hover:bg-white/20"
                                                               :class="isMyMessage(message) ? 'text-white' : 'text-gray-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Avatar for my messages -->
                                    <div x-show="isMyMessage(message)" class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-primary/20 flex items-center justify-center">
                                            <img x-show="message.sender_avatar" 
                                                 :src="message.sender_avatar" 
                                                 :alt="getUserName(message.sender_id)"
                                                 class="w-full h-full object-cover">
                                            <span x-show="!message.sender_avatar" 
                                                  class="text-xs font-semibold text-primary"
                                                  x-text="getUserName(message.sender_id).charAt(0)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Selected File Preview -->
                        <div x-show="selectedFile" 
                             class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-700" x-text="selectedFile.name"></span>
                                <span class="text-xs text-gray-500" x-text="'(' + formatFileSize(selectedFile.size) + ')'"></span>
                            </div>
                            <button @click="removeFile()" 
                                    class="p-1 text-gray-500 hover:text-red-600 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Input Area -->
                        <div class="px-4 py-3 bg-white border-t border-gray-200">
                            <div class="flex items-end gap-2">
                                <!-- File Input Button -->
                                <button @click="$refs.fileInput.click()" 
                                        class="p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition"
                                        title="ارسال فایل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </button>
                                <input type="file" 
                                       x-ref="fileInput"
                                       @change="selectFile($event)"
                                       class="hidden"
                                       accept="*/*">
                                
                                <!-- Voice Recording Button -->
                                <button @mousedown="startRecording()" 
                                        @mouseup="stopRecording()"
                                        @mouseleave="stopRecording()"
                                        @touchstart.prevent="startRecording()"
                                        @touchend.prevent="stopRecording()"
                                        :class="recording ? 'bg-red-500 text-white' : 'text-gray-600 hover:text-primary hover:bg-gray-100'"
                                        class="p-2 rounded-lg transition"
                                        title="ضبط صدا">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"></path>
                                        <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Text Input -->
                                <div class="flex-1 relative">
                                    <textarea x-model="newMessage"
                                              @keydown.enter.prevent="if (!event.shiftKey) sendMessage()"
                                              placeholder="پیام خود را بنویسید..."
                                              rows="1"
                                              class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                              style="min-height: 40px; max-height: 120px;"></textarea>
                                </div>
                                
                                <!-- Send Button -->
                                <button @click="sendMessage()"
                                        :disabled="sending || (!newMessage.trim() && !selectedFile)"
                                        class="p-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="ارسال">
                                    <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Recording Indicator -->
                            <div x-show="recording" 
                                 class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                <div class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></div>
                                <span>در حال ضبط صدا...</span>
                                <span class="text-xs font-mono font-bold" x-text="formatRecordingTime(recordingTime)"></span>
                                <span class="text-xs text-gray-500">برای توقف، دکمه را رها کنید</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files Tab -->
                <div class="p-6" x-show="activeTab === 'files'" x-cloak
                     x-data="{
                         files: [],
                         loading: true,
                         uploading: false,
                         showUploadModal: false,
                         uploadTitle: '',
                         uploadDescription: '',
                         selectedFile: null,
                         filesUrl: '{{ route('projects.files.index', $project) }}',
                         uploadUrl: '{{ route('projects.files.store', $project) }}',
                         csrfToken: '{{ csrf_token() }}',
                         async init() {
                             await this.loadFiles();
                             // Poll for new files every 5 seconds
                             setInterval(() => {
                                 if (this.$el && !this.$el.hasAttribute('x-cloak')) {
                                     this.loadFiles();
                                 }
                             }, 5000);
                         },
                         async loadFiles() {
                             try {
                                 const response = await fetch(this.filesUrl);
                                 if (response.ok) {
                                     this.files = await response.json();
                                 }
                             } catch (error) {
                                 console.error('Error loading files:', error);
                             } finally {
                                 this.loading = false;
                             }
                         },
                         openUploadModal() {
                             this.showUploadModal = true;
                             this.uploadTitle = '';
                             this.uploadDescription = '';
                             this.selectedFile = null;
                         },
                         closeUploadModal() {
                             this.showUploadModal = false;
                             this.uploadTitle = '';
                             this.uploadDescription = '';
                             this.selectedFile = null;
                         },
                         handleFileSelect(event) {
                             this.selectedFile = event.target.files[0];
                         },
                         async uploadFile() {
                             if (!this.uploadTitle.trim() || !this.selectedFile) {
                                 alert('لطفاً عنوان و فایل را وارد کنید');
                                 return;
                             }
                             
                             this.uploading = true;
                             
                             const formData = new FormData();
                             formData.append('title', this.uploadTitle);
                             formData.append('description', this.uploadDescription);
                             formData.append('file', this.selectedFile);
                             formData.append('_token', this.csrfToken);
                             
                             try {
                                 const response = await fetch(this.uploadUrl, {
                                     method: 'POST',
                                     body: formData
                                 });
                                 
                                 if (response.ok) {
                                     const newFile = await response.json();
                                     this.files.unshift(newFile);
                                     this.closeUploadModal();
                                 } else {
                                     const error = await response.json();
                                     alert(error.error || 'خطا در آپلود فایل');
                                 }
                             } catch (error) {
                                 console.error('Error uploading file:', error);
                                 alert('خطا در آپلود فایل');
                             } finally {
                                 this.uploading = false;
                             }
                         },
                         async deleteFile(fileId) {
                             if (!confirm('آیا از حذف این فایل اطمینان دارید؟')) {
                                 return;
                             }
                             
                             try {
                                 const response = await fetch(`{{ route('projects.files.destroy', [$project, ':id']) }}`.replace(':id', fileId), {
                                     method: 'DELETE',
                                     headers: {
                                         'X-CSRF-TOKEN': this.csrfToken,
                                         'Content-Type': 'application/json'
                                     }
                                 });
                                 
                                 if (response.ok) {
                                     this.files = this.files.filter(f => f.id !== fileId);
                                 } else {
                                     alert('خطا در حذف فایل');
                                 }
                             } catch (error) {
                                 console.error('Error deleting file:', error);
                                 alert('خطا در حذف فایل');
                             }
                         },
                         formatFileSize(bytes) {
                             if (bytes === 0) return '0 Bytes';
                             const k = 1024;
                             const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                             const i = Math.floor(Math.log(bytes) / Math.log(k));
                             return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                         },
                         getFileIcon(fileType) {
                             if (!fileType) return '📄';
                             if (fileType.includes('image')) return '🖼️';
                             if (fileType.includes('pdf')) return '📕';
                             if (fileType.includes('word') || fileType.includes('document')) return '📝';
                             if (fileType.includes('excel') || fileType.includes('spreadsheet')) return '📊';
                             if (fileType.includes('zip') || fileType.includes('rar')) return '📦';
                             if (fileType.includes('video')) return '🎥';
                             if (fileType.includes('audio')) return '🎵';
                             return '📄';
                         }
                     }">
                    <!-- Upload Button -->
                    <div class="mb-4 flex justify-end">
                        <button @click="openUploadModal()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            آپلود فایل جدید
                        </button>
                    </div>
                    
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-8 text-gray-500">
                        در حال بارگذاری...
                </div>

                    <!-- Empty State -->
                    <div x-show="!loading && files.length === 0" class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
                        هیچ فایلی آپلود نشده است.
                    </div>
                    
                    <!-- Files List -->
                    <div x-show="!loading && files.length > 0" class="space-y-3">
                        <template x-for="file in files" :key="file.id">
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="text-2xl flex-shrink-0" x-text="getFileIcon(file.file_type)"></div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1" x-text="file.title"></h4>
                                            <p x-show="file.description" class="text-sm text-gray-600 mb-2" x-text="file.description"></p>
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span x-text="file.original_name"></span>
                                                <span x-text="formatFileSize(file.file_size)"></span>
                                                <span x-text="'آپلود شده توسط ' + file.uploaded_by"></span>
                                                <span x-text="file.created_at_formatted"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a :href="file.url" 
                                           target="_blank"
                                           class="p-2 text-primary hover:bg-primary/10 rounded-lg transition"
                                           title="دانلود">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                        <button @click="deleteFile(file.id)"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Upload Modal -->
                    <div x-show="showUploadModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="closeUploadModal()">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.stop>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">آپلود فایل جدید</h3>
                                    <button @click="closeUploadModal()" 
                                            class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <form @submit.prevent="uploadFile()" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            عنوان <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               x-model="uploadTitle"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                               placeholder="عنوان فایل">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            توضیحات (اختیاری)
                                        </label>
                                        <textarea x-model="uploadDescription"
                                                  rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                                                  placeholder="توضیحات فایل"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            فایل <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" 
                                               @change="handleFileSelect($event)"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <p class="text-xs text-gray-500 mt-1">حداکثر حجم فایل: 10 مگابایت</p>
                                    </div>
                                    
                                    <div class="flex items-center justify-end gap-3 pt-4">
                                        <button type="button" 
                                                @click="closeUploadModal()"
                                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                            انصراف
                                        </button>
                                        <button type="submit" 
                                                :disabled="uploading"
                                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!uploading">آپلود</span>
                                            <span x-show="uploading">در حال آپلود...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accounts Tab -->
                <div class="p-6" x-show="activeTab === 'accounts'" x-cloak
                     x-data="{
                         accounts: @js($project->accounts ?? []),
                         showAddModal: false,
                         showEditModal: false,
                         editingAccount: null,
                         newAccount: {
                             url: '',
                             username: '',
                             password: '',
                             description: ''
                         },
                         visiblePasswords: {},
                         togglePassword(accountId) {
                             this.visiblePasswords[accountId] = !this.visiblePasswords[accountId];
                         },
                         openAddModal() {
                             this.newAccount = {
                                 url: '',
                                 username: '',
                                 password: '',
                                 description: ''
                             };
                             this.showAddModal = true;
                         },
                         openEditModal(account) {
                             this.editingAccount = { ...account };
                             this.showEditModal = true;
                         },
                         async saveAccount() {
                             try {
                                 const response = await fetch('{{ route('projects.accounts.store', $project) }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify(this.newAccount),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     this.accounts.push(data.account);
                                     this.showAddModal = false;
                                     this.newAccount = {
                                         url: '',
                                         username: '',
                                         password: '',
                                         description: ''
                                     };
                                 } else {
                                     alert(data.message || 'خطا در افزودن اکانت');
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در افزودن اکانت');
                             }
                         },
                         async updateAccount() {
                             try {
                                 const response = await fetch(`{{ route('projects.accounts.update', [$project, ':id']) }}`.replace(':id', this.editingAccount.id), {
                                     method: 'PUT',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                     body: JSON.stringify({
                                         url: this.editingAccount.url,
                                         username: this.editingAccount.username,
                                         password: this.editingAccount.password,
                                         description: this.editingAccount.description || ''
                                     }),
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     const index = this.accounts.findIndex(a => a.id === this.editingAccount.id);
                                     if (index !== -1) {
                                         this.accounts[index] = data.account;
                                     }
                                     this.showEditModal = false;
                                     this.editingAccount = null;
                                 } else {
                                     alert(data.message || 'خطا در به‌روزرسانی اکانت');
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در به‌روزرسانی اکانت');
                             }
                         },
                         async deleteAccount(accountId) {
                             if (!confirm('آیا از حذف این اکانت اطمینان دارید؟')) return;
                             
                             try {
                                 const response = await fetch(`{{ route('projects.accounts.destroy', [$project, ':id']) }}`.replace(':id', accountId), {
                                     method: 'DELETE',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     },
                                 });
                                 
                                 const data = await response.json();
                                 if (data.success) {
                                     this.accounts = this.accounts.filter(a => a.id !== accountId);
                                 } else {
                                     alert(data.message || 'خطا در حذف اکانت');
                                 }
                             } catch (error) {
                                 console.error('Error:', error);
                                 alert('خطا در حذف اکانت');
                             }
                         },
                         copyToClipboard(text) {
                             navigator.clipboard.writeText(text).then(() => {
                                 alert('کپی شد');
                             }).catch(() => {
                                 alert('خطا در کپی');
                             });
                         }
                     }">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">مدیریت اکانت‌ها</h3>
                        <button @click="openAddModal()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            افزودن اکانت جدید
                        </button>
                    </div>

                    <!-- Accounts List -->
                    <div class="space-y-4" x-show="accounts.length > 0">
                        <template x-for="account in accounts" :key="account.id">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                            </svg>
                                            <a :href="account.url" target="_blank" class="text-primary hover:underline font-medium" x-text="account.url"></a>
                                            <button @click="copyToClipboard(account.url)" 
                                                    class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                            <div>
                                                <span class="text-xs text-gray-500 block mb-1">یوزرنیم</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-900" x-text="account.username"></span>
                                                    <button @click="copyToClipboard(account.username)" 
                                                            class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <span class="text-xs text-gray-500 block mb-1">پسورد</span>
                                                <div class="flex items-center gap-2">
                                                    <input :type="visiblePasswords[account.id] ? 'text' : 'password'" 
                                                           :value="account.password" 
                                                           readonly
                                                           class="text-sm font-medium text-gray-900 bg-transparent border-none p-0 flex-1">
                                                    <button @click="togglePassword(account.id)" 
                                                            class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                                        <svg x-show="!visiblePasswords[account.id]" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <svg x-show="visiblePasswords[account.id]" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="copyToClipboard(account.password)" 
                                                            class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div x-show="account.description" class="mt-3">
                                            <span class="text-xs text-gray-500 block mb-1">توضیحات</span>
                                            <p class="text-sm text-gray-700" x-text="account.description"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 mr-4">
                                        <button @click="openEditModal(account)" 
                                                class="p-2 text-gray-500 hover:text-primary rounded-lg hover:bg-gray-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="deleteAccount(account.id)" 
                                                class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-gray-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="accounts.length === 0" class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p>هیچ اکانتی ثبت نشده است</p>
                        <button @click="openAddModal()" 
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                            افزودن اکانت جدید
                        </button>
                    </div>

                    <!-- Add Account Modal -->
                    <div x-show="showAddModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="showAddModal = false">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <h3 class="text-lg font-bold mb-4">افزودن اکانت جدید</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">آدرس (URL)</label>
                                    <input type="url" x-model="newAccount.url" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="https://example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">یوزرنیم</label>
                                    <input type="text" x-model="newAccount.username" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="نام کاربری">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">پسورد</label>
                                    <input type="password" x-model="newAccount.password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="رمز عبور">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                                    <textarea x-model="newAccount.description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                              placeholder="توضیحات اختیاری"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-6">
                                <button @click="saveAccount()" 
                                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                    افزودن
                                </button>
                                <button @click="showAddModal = false" 
                                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    انصراف
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Account Modal -->
                    <div x-show="showEditModal" 
                         x-cloak
                         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                         @click.self="showEditModal = false">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md" dir="rtl">
                            <template x-if="editingAccount">
                                <div>
                                    <h3 class="text-lg font-bold mb-4">ویرایش اکانت</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">آدرس (URL)</label>
                                            <input type="url" x-model="editingAccount.url" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">یوزرنیم</label>
                                            <input type="text" x-model="editingAccount.username" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">پسورد</label>
                                            <input type="password" x-model="editingAccount.password" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                                            <textarea x-model="editingAccount.description" rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 mt-6">
                                        <button @click="updateAccount()" 
                                                class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                            ذخیره
                                        </button>
                                        <button @click="showEditModal = false" 
                                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                            انصراف
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    window.projectChatData = function(config) {
    return {
        messages: [],
        conversationId: null,
        newMessage: '',
        loading: false,
        sending: false,
        recording: false,
        recordingTime: 0,
        recordingTimer: null,
        mediaRecorder: null,
        audioChunks: [],
        audioStream: null,
        dataAvailablePromise: null,
        dataAvailableResolve: null,
        selectedFile: null,
        fileInput: null,
        currentUserId: config.currentUserId,
        projectId: config.projectId,
        projectUsers: config.projectUsers,
        messagesUrl: config.messagesUrl,
        sendUrl: config.sendUrl,
        csrfToken: config.csrfToken,
        init() {
            this.loadMessages();
            const pollInterval = setInterval(() => {
                if (this.$el && !this.$el.hasAttribute('x-cloak')) {
                    this.loadMessages();
                }
            }, 3000);
            
            this.$el.addEventListener('alpine:destroy', () => {
                clearInterval(pollInterval);
            });
            
            this.$watch('messages.length', () => {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            });
        },
        async loadMessages() {
            if (this.loading) return;
            this.loading = true;
            try {
                const response = await fetch(this.messagesUrl, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });
                const data = await response.json();
                if (data.conversation_id) {
                    this.conversationId = data.conversation_id;
                    this.messages = data.messages || [];
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.loading = false;
            }
        },
        async sendMessage() {
            if (this.sending || (!this.newMessage.trim() && !this.selectedFile)) return;
            
            this.sending = true;
            const formData = new FormData();
            formData.append('body', this.newMessage);
            formData.append('type', this.selectedFile ? 'file' : 'text');
            
            if (this.selectedFile) {
                formData.append('file', this.selectedFile);
            }
            
            try {
                const response = await fetch(this.sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: formData,
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.selectedFile = null;
                    if (this.fileInput) {
                        this.fileInput.value = '';
                    }
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                } else {
                    alert('خطا در ارسال پیام');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('خطا در ارسال پیام');
            } finally {
                this.sending = false;
            }
        },
        async sendVoiceMessage(audioBlob) {
            this.sending = true;
            const formData = new FormData();
            formData.append('type', 'voice');
            formData.append('file', audioBlob, 'voice-message.webm');
            
            try {
                const response = await fetch(this.sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: formData,
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                } else {
                    alert('خطا در ارسال پیام صوتی');
                }
            } catch (error) {
                console.error('Error sending voice message:', error);
                alert('خطا در ارسال پیام صوتی');
            } finally {
                this.sending = false;
            }
        },
        startRecording() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('ضبط صدا در مرورگر شما پشتیبانی نمی‌شود');
                return;
            }
            
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    this.audioStream = stream;
                    this.recording = true;
                    this.recordingTime = 0;
                    this.audioChunks = [];
                    this.dataAvailablePromise = null;
                    this.dataAvailableResolve = null;
                    
                    // Start timer
                    this.recordingTimer = setInterval(() => {
                        this.recordingTime++;
                    }, 1000);
                    
                    // Get available MIME types
                    const options = { mimeType: 'audio/webm' };
                    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                        options.mimeType = 'audio/webm;codecs=opus';
                        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                            options.mimeType = 'audio/ogg;codecs=opus';
                            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                                options.mimeType = '';
                            }
                        }
                    }
                    
                    this.mediaRecorder = new MediaRecorder(stream, options);
                    
                    this.mediaRecorder.ondataavailable = (event) => {
                        if (event.data && event.data.size > 0) {
                            this.audioChunks.push(event.data);
                            console.log('Audio chunk received:', event.data.size, 'bytes');
                        }
                        // Resolve promise when data is available
                        if (this.dataAvailableResolve) {
                            this.dataAvailableResolve();
                            this.dataAvailableResolve = null;
                        }
                    };
                    
                    this.mediaRecorder.onstop = async () => {
                        clearInterval(this.recordingTimer);
                        this.recordingTimer = null;
                        
                        // Request final data
                        this.mediaRecorder.requestData();
                        
                        // Wait for final data to be available (max 500ms)
                        if (this.dataAvailablePromise) {
                            try {
                                await Promise.race([
                                    this.dataAvailablePromise,
                                    new Promise(resolve => setTimeout(resolve, 500))
                                ]);
                            } catch (e) {
                                console.error('Error waiting for data:', e);
                            }
                        }
                        
                        // Additional delay to ensure all chunks are collected
                        await new Promise(resolve => setTimeout(resolve, 300));
                        
                        console.log('Total chunks:', this.audioChunks.length);
                        console.log('Recording time:', this.recordingTime);
                        
                        if (this.audioChunks.length > 0) {
                            const mimeType = this.mediaRecorder.mimeType || 'audio/webm';
                            const audioBlob = new Blob(this.audioChunks, { type: mimeType });
                            
                            console.log('Audio blob size:', audioBlob.size, 'bytes');
                            
                            // Check if blob has data (at least 3KB) and recording time is at least 1 second
                            if (audioBlob.size > 3072 && this.recordingTime >= 1) {
                                this.sendVoiceMessage(audioBlob);
                            } else {
                                if (audioBlob.size <= 3072) {
                                    alert('صدای ضبط شده خیلی کوتاه است. لطفاً حداقل 1 ثانیه ضبط کنید.');
                                } else {
                                    alert('زمان ضبط کافی نیست. لطفاً حداقل 1 ثانیه ضبط کنید.');
                                }
                            }
                        } else {
                            alert('هیچ صدایی ضبط نشد. لطفاً دوباره تلاش کنید.');
                        }
                        
                        if (this.audioStream) {
                            this.audioStream.getTracks().forEach(track => track.stop());
                            this.audioStream = null;
                        }
                        
                        this.recording = false;
                        this.recordingTime = 0;
                        this.audioChunks = [];
                        this.dataAvailablePromise = null;
                        this.dataAvailableResolve = null;
                    };
                    
                    this.mediaRecorder.onerror = (event) => {
                        console.error('MediaRecorder error:', event);
                        clearInterval(this.recordingTimer);
                        this.recordingTimer = null;
                        this.recording = false;
                        this.recordingTime = 0;
                        if (this.audioStream) {
                            this.audioStream.getTracks().forEach(track => track.stop());
                            this.audioStream = null;
                        }
                        alert('خطا در ضبط صدا');
                    };
                    
                    // Start recording with timeslice to ensure data collection
                    // Timeslice of 250ms ensures we get data chunks regularly
                    this.mediaRecorder.start(250);
                })
                .catch(error => {
                    console.error('Error accessing microphone:', error);
                    alert('خطا در دسترسی به میکروفون. لطفاً مجوز دسترسی به میکروفون را بررسی کنید.');
                });
        },
        stopRecording() {
            if (this.mediaRecorder && this.recording) {
                // Check minimum recording time (at least 1 second)
                if (this.recordingTime < 1) {
                    alert('لطفاً حداقل 1 ثانیه ضبط کنید');
                    // Cancel recording
                    this.mediaRecorder.stop();
                    if (this.audioStream) {
                        this.audioStream.getTracks().forEach(track => track.stop());
                        this.audioStream = null;
                    }
                    clearInterval(this.recordingTimer);
                    this.recordingTimer = null;
                    this.recording = false;
                    this.recordingTime = 0;
                    this.audioChunks = [];
                    return;
                }
                
                // Create promise to wait for final data
                this.dataAvailablePromise = new Promise((resolve) => {
                    this.dataAvailableResolve = resolve;
                });
                
                // Request final data before stopping
                this.mediaRecorder.requestData();
                
                // Wait a bit to ensure data request is processed
                setTimeout(() => {
                    if (this.mediaRecorder && this.recording) {
                        this.mediaRecorder.stop();
                    }
                }, 300);
            }
        },
        formatRecordingTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },
        selectFile(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('حجم فایل نباید بیشتر از 10 مگابایت باشد');
                    return;
                }
                this.selectedFile = file;
            }
        },
        removeFile() {
            this.selectedFile = null;
            if (this.fileInput) {
                this.fileInput.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },
        scrollToBottom() {
            const chatContainer = document.getElementById('chat-messages-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        isMyMessage(message) {
            return message.sender_id === this.currentUserId;
        },
        getUserName(userId) {
            const user = this.projectUsers.find(u => u.id === userId);
            return user ? user.name : 'کاربر ناشناس';
        }
    };
    };
    </script>
</x-app-layout>