<div id="confirm-modal-bg" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs min-w-[16rem] mx-auto">
        <h2 id="confirm-modal-title" class="text-lg font-bold mb-4 text-gray-900 text-center">Confirmation</h2>
        <p id="confirm-modal-message" class="text-gray-700 mb-6 text-center">Are you sure?</p>
        <div class="flex justify-center gap-3">
            <button id="confirm-modal-cancel" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancel</button>
            <button id="confirm-modal-ok" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 font-semibold">Confirm</button>
        </div>
    </div>
</div>
<script>
// Confirmation modal logic
window.showConfirmModal = function(message = 'Are you sure?', title = 'Confirmation') {
    return new Promise((resolve) => {
        const modalBg = document.getElementById('confirm-modal-bg');
        const modalTitle = document.getElementById('confirm-modal-title');
        const modalMessage = document.getElementById('confirm-modal-message');
        const okBtn = document.getElementById('confirm-modal-ok');
        const cancelBtn = document.getElementById('confirm-modal-cancel');
        
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalBg.classList.remove('hidden');
        
        function close(result) {
            modalBg.classList.add('hidden');
            okBtn.removeEventListener('click', onOk);
            cancelBtn.removeEventListener('click', onCancel);
            modalBg.removeEventListener('click', onBgClick);
            resolve(result);
        }
        function onOk(e) { e.preventDefault(); close(true); }
        function onCancel(e) { e.preventDefault(); close(false); }
        function onBgClick(e) { if (e.target === modalBg) close(false); }
        okBtn.addEventListener('click', onOk);
        cancelBtn.addEventListener('click', onCancel);
        modalBg.addEventListener('click', onBgClick);
    });
}
</script> 