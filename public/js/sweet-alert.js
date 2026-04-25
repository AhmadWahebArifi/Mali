// Sweet Alert Utilities for BawarFinTrack
class BawarFinTrackAlert {
    // Success alert
    static success(title, message = '') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonColor: '#004ccd',
            background: '#ffffff',
            color: '#151c27',
            showConfirmButton: true,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    // Error alert
    static error(title, message = '') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonColor: '#ba1a1a',
            background: '#ffffff',
            color: '#151c27',
            showConfirmButton: true
        });
    }

    // Warning alert
    static warning(title, message = '') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            confirmButtonColor: '#004ccd',
            background: '#ffffff',
            color: '#151c27',
            showConfirmButton: true
        });
    }

    // Info alert
    static info(title, message = '') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'info',
            confirmButtonColor: '#004ccd',
            background: '#ffffff',
            color: '#151c27',
            showConfirmButton: true
        });
    }

    // Confirmation dialog
    static confirm(title, message, confirmText = 'Yes', cancelText = 'Cancel') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#004ccd',
            cancelButtonColor: '#737687',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            background: '#ffffff',
            color: '#151c27'
        });
    }

    // Delete confirmation
    static deleteConfirm(itemName = 'this item') {
        return Swal.fire({
            title: 'Delete Confirmation',
            text: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#737687',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            color: '#151c27'
        });
    }

    // Loading alert
    static loading(title = 'Processing...') {
        return Swal.fire({
            title: title,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Toast notification (top-right corner)
    static toast(message, type = 'success', duration = 3000) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const iconMap = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        return Toast.fire({
            icon: iconMap[type] || 'info',
            title: message
        });
    }

    // Transaction success
    static transactionSuccess(type, amount) {
        const typeText = type === 'income' ? 'Income' : 'Expense';
        const color = type === 'income' ? '#00714d' : '#ba1a1a';
        
        return Swal.fire({
            title: `${typeText} Added!`,
            text: `$${parseFloat(amount).toFixed(2)} ${typeText.toLowerCase()} has been successfully added.`,
            icon: 'success',
            confirmButtonColor: color,
            background: '#ffffff',
            color: '#151c27',
            timer: 2500,
            timerProgressBar: true
        });
    }

    // Form validation error
    static validationError(errors) {
        let errorMessage = 'Please fix the following errors:\n\n';
        if (typeof errors === 'object') {
            Object.keys(errors).forEach(key => {
                errorMessage += `• ${errors[key].join(', ')}\n`;
            });
        } else {
            errorMessage = errors;
        }

        return Swal.fire({
            title: 'Validation Error',
            text: errorMessage,
            icon: 'error',
            confirmButtonColor: '#ba1a1a',
            background: '#ffffff',
            color: '#151c27'
        });
    }
}

// Make it globally available
window.BawarFinTrackAlert = BawarFinTrackAlert;
