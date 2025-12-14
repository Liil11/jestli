<div id="postModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    
    <div class="bg-[#121212] w-full max-w-2xl rounded-xl shadow-2xl border border-gray-800 transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="modalContent">
        
        <div class="flex justify-between items-center p-4 border-b border-gray-800">
            <h2 class="text-white text-lg font-semibold">Create Post</h2>
            <button id="closePostModalBtn" class="text-gray-400 hover:text-white transition p-2 rounded-full hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                
                <div class="flex gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-gray-700 flex-shrink-0 flex items-center justify-center font-bold text-white overflow-hidden">
                        @if(Auth::check() && Auth::user()->profile_photo_url)
                             <img src="{{ Auth::user()->profile_photo_url }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                             {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <textarea name="caption" id="captionInput" rows="3"
                        class="w-full bg-transparent text-white placeholder-gray-500 border-none focus:ring-0 resize-none text-lg p-2 mt-1"
                        placeholder="What is happening?!"></textarea>
                </div>

                <div id="mediaPreviewContainer" class="hidden relative w-full rounded-xl overflow-hidden bg-black mb-4 border border-gray-800 group">
                    <img id="previewImage" class="w-full h-auto object-contain max-h-[400px]" alt="Preview"/>
                    
                    <button type="button" id="removeImage" class="absolute top-3 right-3 bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition backdrop-blur-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="border-t border-gray-800 pt-3 mt-2">
                    <label for="imageInput" class="cursor-pointer inline-flex items-center gap-2 text-teal-500 hover:text-teal-400 transition font-medium text-sm p-2 hover:bg-teal-500/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Add Photo
                    </label>
                    <input type="file" name="image" id="imageInput" accept="image/*" class="hidden"/>
                </div>
            </div>

            <div class="p-4 border-t border-gray-800 flex justify-end bg-[#121212]">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-full font-bold text-sm transition shadow-lg disabled:opacity-50 hover:shadow-teal-500/20">
                    Post
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const modal = document.getElementById('postModal');
        const modalContent = document.getElementById('modalContent');
        const closeBtn = document.getElementById('closePostModalBtn');
        
        // Triggers
        const sidebarBtn = document.getElementById('openPostModalSidebar'); // Tombol Sidebar
        const feedBtn = document.getElementById('feedCreateTrigger');     // Tombol di Feed (jika ada)

        // Form Elements
        const imageInput = document.getElementById('imageInput');
        const removeImageBtn = document.getElementById('removeImage');
        const previewContainer = document.getElementById('mediaPreviewContainer');
        const previewImage = document.getElementById('previewImage');

        // Functions
        function openModal(e) {
            if(e) e.preventDefault(); // Mencegah link pindah halaman
            modal.classList.remove('hidden');
            // Small delay to allow transition
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Event Listeners
        if(sidebarBtn) sidebarBtn.addEventListener('click', openModal);
        if(feedBtn) feedBtn.addEventListener('click', openModal);
        
        if(closeBtn) closeBtn.addEventListener('click', closeModal);
        
        // Close when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Image Preview Logic
        if(imageInput) {
            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewImage.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if(removeImageBtn) {
            removeImageBtn.addEventListener('click', () => {
                imageInput.value = ''; 
                previewImage.src = '';
                previewContainer.classList.add('hidden');
            });
        }
    });
</script>