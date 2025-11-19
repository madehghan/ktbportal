<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            تصویر پروفایل
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            تصویر پروفایل خود را انتخاب و برش دهید.
        </p>
    </header>

    <div class="mt-6" 
         x-data="{
             showCropper: false,
             cropper: null,
             imageSrc: null,
             currentAvatar: '{{ $user->avatar ? asset("storage/" . $user->avatar) : "" }}',
             
             init() {
                 // Initialize cropper when modal opens
             },
             
             openFileInput() {
                 document.getElementById('avatar-input').click();
             },
             
             handleFileSelect(event) {
                 const file = event.target.files[0];
                 if (file) {
                     if (!file.type.match('image.*')) {
                         alert('لطفاً یک فایل تصویری انتخاب کنید');
                         return;
                     }
                     
                     const reader = new FileReader();
                     reader.onload = (e) => {
                         this.imageSrc = e.target.result;
                         this.showCropper = true;
                         this.$nextTick(() => {
                             this.initCropper();
                         });
                     };
                     reader.readAsDataURL(file);
                 }
             },
             
             initCropper() {
                 const image = document.getElementById('cropper-image');
                 if (image && typeof Cropper !== 'undefined') {
                     if (this.cropper) {
                         this.cropper.destroy();
                     }
                     this.cropper = new Cropper(image, {
                         aspectRatio: 1,
                         viewMode: 1,
                         dragMode: 'move',
                         autoCropArea: 0.8,
                         restore: false,
                         guides: true,
                         center: true,
                         highlight: false,
                         cropBoxMovable: true,
                         cropBoxResizable: true,
                         toggleDragModeOnDblclick: false,
                     });
                 }
             },
             
             cropImage() {
                 if (!this.cropper) {
                     return;
                 }
                 
                 const canvas = this.cropper.getCroppedCanvas({
                     width: 400,
                     height: 400,
                     imageSmoothingEnabled: true,
                     imageSmoothingQuality: 'high',
                 });
                 
                 const croppedImage = canvas.toDataURL('image/png');
                 this.uploadAvatar(croppedImage);
             },
             
             async uploadAvatar(imageData) {
                 try {
                     const response = await fetch('{{ route("profile.avatar.update") }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         },
                         body: JSON.stringify({
                             avatar: imageData
                         })
                     });
                     
                     const data = await response.json();
                     
                     if (data.success) {
                         this.currentAvatar = data.avatar_url;
                         this.showCropper = false;
                         this.imageSrc = null;
                         if (this.cropper) {
                             this.cropper.destroy();
                             this.cropper = null;
                         }
                         
                         // Show success message
                         alert('تصویر پروفایل با موفقیت به‌روزرسانی شد');
                         
                         // Reload page to show new avatar
                         window.location.reload();
                     } else {
                         alert(data.error || 'خطا در آپلود تصویر');
                     }
                 } catch (error) {
                     console.error('Error:', error);
                     alert('خطا در آپلود تصویر');
                 }
             },
             
             cancelCrop() {
                 this.showCropper = false;
                 this.imageSrc = null;
                 if (this.cropper) {
                     this.cropper.destroy();
                     this.cropper = null;
                 }
                 document.getElementById('avatar-input').value = '';
             }
         }">
        
        <!-- Current Avatar Display -->
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg">
                    <template x-if="currentAvatar">
                        <img :src="currentAvatar" alt="Avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!currentAvatar">
                        <div class="w-full h-full bg-primary flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">{{ mb_substr($user->name, 0, 2) }}</span>
                        </div>
                    </template>
                </div>
            </div>
            
            <div>
                <button @click="openFileInput()" 
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>انتخاب تصویر</span>
                </button>
            </div>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" 
               id="avatar-input" 
               accept="image/*" 
               class="hidden" 
               @change="handleFileSelect($event)">
        
        <!-- Cropper Modal -->
        <div x-show="showCropper" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
             @click.self="cancelCrop()">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6" dir="rtl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">برش تصویر</h3>
                    <button @click="cancelCrop()" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="max-w-full" style="max-height: 500px;">
                        <img id="cropper-image" :src="imageSrc" alt="Crop" style="max-width: 100%; display: block;">
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3">
                    <button @click="cancelCrop()" 
                            type="button"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        انصراف
                    </button>
                    <button @click="cropImage()" 
                            type="button"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        ذخیره
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

