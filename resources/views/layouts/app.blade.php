<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>کارمانیا توسعه - سامانه داخلی شرکت</title>

        <!-- Vazirmatn Font -->
        <style>
            @font-face {
                font-family: 'Vazirmatn';
                src: url('{{ asset('fonts/Vazirmatn-Regular.woff2') }}') format('woff2');
                font-weight: 400;
                font-style: normal;
                font-display: swap;
            }
            @font-face {
                font-family: 'Vazirmatn';
                src: url('{{ asset('fonts/Vazirmatn-Medium.woff2') }}') format('woff2');
                font-weight: 500;
                font-style: normal;
                font-display: swap;
            }
            @font-face {
                font-family: 'Vazirmatn';
                src: url('{{ asset('fonts/Vazirmatn-Bold.woff2') }}') format('woff2');
                font-weight: 700;
                font-style: normal;
                font-display: swap;
            }
            [x-cloak] { display: none !important; }
        </style>

        <!-- Persian Datepicker CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
        <!-- Cropper.js CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- jQuery (required for persian-datepicker) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Persian Date JS (required for datepicker) -->
        <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
        <!-- Persian Datepicker JS -->
        <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
        <!-- Sortable.js for drag and drop -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <!-- Cropper.js -->
        <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
        <!-- Quill Rich Text Editor -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-white via-gray-50 to-primary/5" 
             x-data="{ 
                 sidebarOpen: false,
                 taskModalOpen: false,
                 selectedProjectId: null,
                 columns: [],
                 loadingColumns: false,
                 formSubmitting: false,
                 currentStep: 1,
                 totalSteps: 3,
                 closeTaskModal() {
                     this.selectedProjectId = null;
                     this.columns = [];
                     this.currentStep = 1;
                     if (this.$refs.taskForm) {
                         this.$refs.taskForm.reset();
                     }
                     if (this.$refs.dueDateInput && this.$refs.dueDateInput.hasAttribute('data-persian-datepicker-initialized')) {
                         $(this.$refs.dueDateInput).persianDatepicker('destroy');
                         this.$refs.dueDateInput.removeAttribute('data-persian-datepicker-initialized');
                     }
                     this.taskModalOpen = false;
                 },
                 nextStep() {
                     if (this.currentStep === 1) {
                         // Validate step 1
                         if (!this.selectedProjectId || !this.$refs.columnSelect.value) {
                             alert('لطفاً پروژه و دسته‌بندی را انتخاب کنید');
                             return;
                         }
                     } else if (this.currentStep === 2) {
                         // Validate step 2
                         if (!this.$refs.titleInput.value.trim()) {
                             alert('لطفاً عنوان تسک را وارد کنید');
                             return;
                         }
                     }
                     if (this.currentStep < this.totalSteps) {
                         this.currentStep++;
                     }
                 },
                 prevStep() {
                     if (this.currentStep > 1) {
                         this.currentStep--;
                     }
                 },
                 async submitTaskForm() {
                     this.formSubmitting = true;
                     try {
                         const response = await fetch('{{ route('tasks.create.anywhere') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                             },
                             body: JSON.stringify({
                                 project_id: this.selectedProjectId,
                                 project_column_id: this.$refs.columnSelect.value,
                                 title: this.$refs.titleInput.value,
                                 description: this.$refs.descriptionInput.value,
                                 assigned_user_ids: Array.from(this.$refs.userSelect.selectedOptions).map(opt => parseInt(opt.value)),
                                 due_date_jalali: this.$refs.dueDateInput.value,
                                 priority: this.$refs.prioritySelect.value
                             })
                         });
                         const data = await response.json();
                         if (data.success) {
                             alert('تسک با موفقیت ایجاد شد');
                             this.closeTaskModal();
                             window.location.reload();
                         } else {
                             alert(data.message || 'خطا در ایجاد تسک');
                         }
                     } catch (error) {
                         console.error('Error:', error);
                         alert('خطا در ایجاد تسک');
                     } finally {
                         this.formSubmitting = false;
                     }
                 },
                 async loadColumns(projectId) {
                     if (!projectId) {
                         this.columns = [];
                         return;
                     }
                     this.loadingColumns = true;
                     try {
                         const response = await fetch(`/projects/${projectId}/columns`);
                         const data = await response.json();
                         if (data.success) {
                             this.columns = data.columns;
                         }
                     } catch (error) {
                         console.error('Error:', error);
                         this.columns = [];
                     } finally {
                         this.loadingColumns = false;
                     }
                 }
             }">
            <!-- Mobile Menu Button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="lg:hidden fixed top-4 right-4 z-50 p-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Overlay for mobile -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
                 style="display: none;">
            </div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'" 
                   class="fixed top-0 right-0 h-screen w-64 bg-white border-l border-gray-200 z-50 lg:translate-x-0 transition-transform duration-300 ease-in-out">
                <div class="h-full flex flex-col">
                    <!-- Logo Section -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-4 lg:hidden">
                            <h3 class="text-lg font-bold text-gray-900">منو</h3>
                            <button @click="sidebarOpen = false" class="p-1 text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            @if(file_exists(public_path('images/logo.png')))
                                <img src="{{ asset('images/logo.png') }}" alt="کارمانیا توسعه" class="h-10 w-10">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">کارمانیا توسعه</h2>
                                <p class="text-xs text-gray-500">سامانه داخلی شرکت</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    @php
                        // Group menu items
                        $dashboardItems = collect($sidebarMenuItems ?? [])->filter(fn($item) => $item['route'] === 'dashboard');
                        $projectsItems = collect($sidebarMenuItems ?? [])->filter(fn($item) => in_array($item['route'], ['projects.index']));
                        $managementItems = collect($sidebarMenuItems ?? [])->filter(fn($item) => in_array($item['route'], ['users.index', 'customers.index', 'roles.index']));
                        $settingsItems = collect($sidebarMenuItems ?? [])->filter(fn($item) => in_array($item['route'], ['sms-logs.index']));
                        $profileItems = collect($sidebarMenuItems ?? [])->filter(fn($item) => $item['route'] === 'profile.edit');
                        
                        // Determine which menu should be open by default
                        $defaultOpenMenu = null;
                        if ($projectsItems->where('active', true)->isNotEmpty()) {
                            $defaultOpenMenu = 'projects';
                        } elseif ($managementItems->where('active', true)->isNotEmpty()) {
                            $defaultOpenMenu = 'management';
                        } elseif ($settingsItems->where('active', true)->isNotEmpty()) {
                            $defaultOpenMenu = 'settings';
                        }
                    @endphp
                    <nav class="flex-1 p-4 space-y-1" x-data="{ openMenu: @js($defaultOpenMenu) }">

                        @if($dashboardItems->isNotEmpty())
                            @foreach($dashboardItems as $item)
                                <a href="{{ route($item['route']) }}" 
                                   @click="sidebarOpen = false"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ $item['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                                    {!! $item['icon'] !!}
                                    <span class="font-medium text-sm">{{ $item['name'] }}</span>
                                </a>
                            @endforeach
                        @endif

                        <!-- Messenger Menu -->
                        <a href="{{ route('messenger.index') }}" 
                           @click="sidebarOpen = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('messenger.*') ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="font-medium text-sm">پیامرسان</span>
                        </a>

                        @if($projectsItems->isNotEmpty())
                            <!-- Projects Menu with Submenu -->
                            <div class="relative">
                                <button @click="openMenu = openMenu === 'projects' ? null : 'projects'"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg transition-all {{ $projectsItems->where('active', true)->isNotEmpty() ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">پروژه‌ها</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="openMenu === 'projects' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Submenu -->
                                <div x-show="openMenu === 'projects'" 
                                     x-transition
                                     class="mt-1 mr-4 space-y-1">
                                    @foreach($projectsItems as $item)
                                        <a href="{{ route($item['route']) }}" 
                                           @click="sidebarOpen = false"
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ $item['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-600 hover:bg-gray-50 border border-transparent' }}">
                                            {!! $item['icon'] !!}
                                            <span class="font-medium text-xs">{{ $item['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($managementItems->isNotEmpty())
                            <!-- Management Menu with Submenu -->
                            <div class="relative">
                                <button @click="openMenu = openMenu === 'management' ? null : 'management'"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg transition-all {{ $managementItems->where('active', true)->isNotEmpty() ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">کاربران</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="openMenu === 'management' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Submenu -->
                                <div x-show="openMenu === 'management'" 
                                     x-transition
                                     class="mt-1 mr-4 space-y-1">
                                    @foreach($managementItems as $item)
                                        <a href="{{ route($item['route']) }}" 
                                           @click="sidebarOpen = false"
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ $item['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-600 hover:bg-gray-50 border border-transparent' }}">
                                            {!! $item['icon'] !!}
                                            <span class="font-medium text-xs">{{ $item['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($settingsItems->isNotEmpty())
                            <!-- Settings Menu with Submenu -->
                            <div class="relative">
                                <button @click="openMenu = openMenu === 'settings' ? null : 'settings'"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg transition-all {{ $settingsItems->where('active', true)->isNotEmpty() ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">تنظیمات سیستم</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="openMenu === 'settings' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Submenu -->
                                <div x-show="openMenu === 'settings'" 
                                     x-transition
                                     class="mt-1 mr-4 space-y-1">
                                    @foreach($settingsItems as $item)
                                        <a href="{{ route($item['route']) }}" 
                                           @click="sidebarOpen = false"
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ $item['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-600 hover:bg-gray-50 border border-transparent' }}">
                                            {!! $item['icon'] !!}
                                            <span class="font-medium text-xs">{{ $item['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($profileItems->isNotEmpty())
                            @foreach($profileItems as $item)
                                <a href="{{ route($item['route']) }}" 
                                   @click="sidebarOpen = false"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ $item['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-700 hover:bg-gray-50 border border-transparent' }}">
                                    {!! $item['icon'] !!}
                                    <span class="font-medium text-sm">{{ $item['name'] }}</span>
                                </a>
                            @endforeach
                        @endif

                        @if(collect($sidebarMenuItems ?? [])->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-sm text-gray-500">دسترسی به منو وجود ندارد</p>
                            </div>
                        @endif
                    </nav>

                    <!-- User Section -->
                    <div class="p-4 border-t border-gray-200">
                        <!-- User Info with Avatar -->
                        <div class="flex items-center gap-3 mb-3">
                            <a href="{{ route('profile.edit') }}" class="block flex-shrink-0">
                                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-primary/20 hover:border-primary/40 transition-all cursor-pointer shadow-md hover:shadow-lg relative">
                                    <div class="w-full h-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ mb_substr(Auth::user()->name, 0, 2) }}</span>
                                    </div>
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                             alt="{{ Auth::user()->name }}" 
                                             class="w-full h-full object-cover absolute inset-0"
                                             onerror="this.style.display='none';">
                                    @endif
                                </div>
                            </a>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->mobile }}</p>
                            </div>
                        </div>
                        
                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>خروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="lg:mr-64">
                <!-- Fixed Header -->
                <header class="fixed top-0 left-0 right-0 lg:right-64 bg-white border-b border-gray-200 z-30 shadow-sm">
                    <div class="px-4 sm:px-6 lg:px-8 relative">
                        <div class="flex items-center justify-end h-16 lg:justify-between">
                            <!-- Center - Logo/Brand (Mobile) -->
                            <div class="absolute left-1/2 transform -translate-x-1/2 lg:hidden">
                                @if(file_exists(public_path('images/logo.png')))
                                    <img src="{{ asset('images/logo.png') }}" alt="کارمانیا توسعه" class="h-8 w-8">
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-primary/80 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Center - Page Title (Desktop) -->
                            <div class="hidden lg:flex items-center flex-1">
                                <h1 class="text-lg font-semibold text-gray-900">
                                    @if(isset($pageTitle))
                                        {{ $pageTitle }}
                                    @else
                                        {{ $header ?? 'داشبورد' }}
                                    @endif
                                </h1>
                            </div>
                            
                            <!-- Right Side - User Info & Actions -->
                            <div class="flex items-center gap-3">
                                <!-- Notifications (Optional) -->
                                <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                </button>
                                
                                <!-- User Avatar & Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 transition">
                                        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-primary/20 relative">
                                            <div class="w-full h-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center">
                                                <span class="text-white font-bold text-xs">{{ mb_substr(Auth::user()->name, 0, 2) }}</span>
                                            </div>
                                            @if(Auth::user()->avatar)
                                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                                                     alt="{{ Auth::user()->name }}" 
                                                     class="w-full h-full object-cover absolute inset-0"
                                                     onerror="this.style.display='none';">
                                            @endif
                                        </div>
                                        <div class="hidden lg:block text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500">{{ Auth::user()->role->name ?? 'کاربر' }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                        <a href="{{ route('profile.edit') }}" 
                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            پروفایل
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-right">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                خروج
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Page Heading (Optional - for custom headers) -->
                @isset($header)
                    <div class="bg-white border-b border-gray-200 pt-16 lg:pt-0">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
            @endisset

            <!-- Page Content -->
                <main class="pt-16">
                {{ $slot }}
            </main>
            </div>

            <!-- Floating Add Task Button (Fixed Left Side) -->
            <button @click="taskModalOpen = true"
                    class="fixed left-4 bottom-4 lg:left-6 lg:bottom-6 z-50 w-14 h-14 bg-primary text-white rounded-full shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all duration-300 flex items-center justify-center group animate-pulse hover:animate-none"
                    title="افزودن تسک جدید">
                <svg class="w-7 h-7 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>

            <!-- Task Creation Modal -->
            <div x-show="taskModalOpen" 
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="closeTaskModal()"
             class="fixed inset-0 bg-black bg-opacity-50 z-[100] flex items-center justify-center p-4">
            <div @click.self="closeTaskModal()"
                 class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition-all ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">افزودن تسک جدید</h3>
                        <button @click="closeTaskModal()" 
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="flex items-center justify-between">
                        <template x-for="step in totalSteps" :key="step">
                            <div class="flex items-center flex-1">
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all"
                                         :class="step === currentStep ? 'bg-primary border-primary text-white' : (step < currentStep ? 'bg-primary/20 border-primary text-primary' : 'bg-gray-100 border-gray-300 text-gray-400')">
                                        <span x-show="step < currentStep" class="text-sm font-bold">✓</span>
                                        <span x-show="step >= currentStep" class="text-sm font-bold" x-text="step"></span>
                                    </div>
                                    <p class="text-xs mt-2 text-center" 
                                       :class="step === currentStep ? 'text-primary font-medium' : 'text-gray-500'"
                                       x-text="step === 1 ? 'پروژه' : (step === 2 ? 'اطلاعات' : 'اختصاص')"></p>
                                </div>
                                <div x-show="step < totalSteps" class="flex-1 h-0.5 mx-2"
                                     :class="step < currentStep ? 'bg-primary' : 'bg-gray-200'"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="submitTaskForm()" 
                x-ref="taskForm"
                class="p-6">
                    <!-- Step 1: Project & Category -->
                    <div x-show="currentStep === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform -translate-x-4"
                         class="space-y-4">
                        <h4 class="text-base font-semibold text-gray-900 mb-4">انتخاب پروژه و دسته‌بندی</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">پروژه <span class="text-red-500">*</span></label>
                            <select x-model="selectedProjectId"
                                    @change="loadColumns(selectedProjectId)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    required>
                                <option value="">انتخاب پروژه</option>
                                @foreach($projects ?? [] as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی <span class="text-red-500">*</span></label>
                            <select x-ref="columnSelect"
                                    :disabled="!selectedProjectId || loadingColumns"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    required>
                                <option value="">انتخاب دسته‌بندی</option>
                                <template x-for="column in columns" :key="column.id">
                                    <option :value="column.id" x-text="column.name"></option>
                                </template>
                            </select>
                            <p x-show="loadingColumns" class="mt-1 text-sm text-gray-500">در حال بارگذاری...</p>
                        </div>
                    </div>

                    <!-- Step 2: Task Information -->
                    <div x-show="currentStep === 2" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform -translate-x-4"
                         class="space-y-4">
                        <h4 class="text-base font-semibold text-gray-900 mb-4">اطلاعات تسک</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان تسک <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   x-ref="titleInput"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                            <textarea x-ref="descriptionInput"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                      placeholder="توضیحات تسک را وارد کنید..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                            <select x-ref="prioritySelect"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="medium">متوسط</option>
                                <option value="low">پایین</option>
                                <option value="high">بالا</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: Assignment & Due Date -->
                    <div x-show="currentStep === 3" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform -translate-x-4"
                         class="space-y-4">
                        <h4 class="text-base font-semibold text-gray-900 mb-4">اختصاص و مهلت انجام</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اختصاص به کاربران</label>
                            <select x-ref="userSelect"
                                    multiple
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    size="5">
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">برای انتخاب چند کاربر، کلید Ctrl (یا Cmd در Mac) را نگه دارید</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مهلت انجام</label>
                            <input type="text" 
                                   x-ref="dueDateInput"
                                   x-effect="
                                       if (currentStep === 3 && taskModalOpen && $el && !$el.hasAttribute('data-persian-datepicker-initialized')) {
                                           setTimeout(() => {
                                               if ($el) {
                                                   $($el).persianDatepicker({
                                                       observer: true,
                                                       format: 'YYYY/MM/DD',
                                                       timePicker: { enabled: false }
                                                   });
                                                   $el.setAttribute('data-persian-datepicker-initialized', 'true');
                                               }
                                           }, 300);
                                       }
                                   "
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary persian-datepicker"
                                   placeholder="انتخاب تاریخ">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button"
                                @click="closeTaskModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">
                            انصراف
                        </button>
                        
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    x-show="currentStep > 1"
                                    @click="prevStep()"
                                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    قبلی
                                </span>
                            </button>
                            
                            <button type="button"
                                    x-show="currentStep < totalSteps"
                                    @click="nextStep()"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                                <span class="flex items-center gap-2">
                                    بعدی
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </button>
                            
                            <button type="submit"
                                    x-show="currentStep === totalSteps"
                                    :disabled="formSubmitting"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!formSubmitting">ایجاد تسک</span>
                                <span x-show="formSubmitting">در حال ایجاد...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Additional Scripts -->
        @stack('scripts')
        
    </body>
</html>
