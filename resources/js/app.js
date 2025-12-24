import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// Global toast notification function
window.showToast = function(message, type = 'success', duration = 5000) {
    // Remove existing toasts
    document.querySelectorAll('[x-data*="toast"]').forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100); 
                     setTimeout(() => show = false, ${duration}); 
                     setTimeout(() => \$el.remove(), ${duration + 500});"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-4 right-4 ${getToastColor(type)} text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-sm">
            <div class="flex items-center">
                <i class="${getToastIcon(type)} mr-3 text-lg"></i>
                <div class="flex-1">
                    <p class="font-medium">${message}</p>
                </div>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Initialize Alpine.js on the new element
    Alpine.initTree(toast);
};

function getToastColor(type) {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    return colors[type] || colors.success;
}

function getToastIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.success;
}

// Handle form submissions with toast
document.addEventListener('DOMContentLoaded', function() {
    // Intercept form submissions
    document.querySelectorAll('form[data-toast]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const toastMessage = this.getAttribute('data-toast-message');
            const toastType = this.getAttribute('data-toast-type') || 'success';
            
            if (toastMessage) {
                showToast(toastMessage, toastType);
            }
        });
    });
    
    // Handle Livewire events
    window.addEventListener('show-toast', event => {
        showToast(event.detail.message, event.detail.type);
    });
});