<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>کارمانیا پورتال</title>

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
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-white via-gray-50 to-primary/5">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <!-- Logo & Title -->
                <div class="text-center mb-8">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="کارمانیا پورتال" class="h-20 mx-auto mb-4">
                    @else
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-primary to-primary/80 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    @endif
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">کارمانیا پورتال</h1>
                    <p class="text-gray-500 text-sm">به پنل مدیریت خوش آمدید</p>
                </div>

                <!-- Login Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        {{ $slot }}
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                        <p class="text-center text-xs text-gray-500">
                            © {{ date('Y') }} کارمانیا پورتال. تمامی حقوق محفوظ است.
                        </p>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        نیاز به راهنمایی دارید؟ 
                        <a href="#" class="text-primary hover:text-primary/80 font-medium transition-colors">تماس با پشتیبانی</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
