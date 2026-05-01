// resources/js/utils/wishlist.js
// utility functions untuk wishlist functionality

/**
 * toggle wishlist dengan smooth animation (vanilla JS approach)
 */
export function toggleWishlist(problemId, button) {
    // gunakan axios yang sudah ter-configure dengan CSRF token
    const url = `/student/wishlist/${problemId}/toggle`;

    axios.post(url)
        .then(response => {
            if (response.data.success) {
                // update UI
                updateWishlistButton(button, response.data.is_saved);
                
                // tampilkan notification
                showNotification(
                    response.data.message,
                    response.data.is_saved ? 'success' : 'info'
                );
            }
        })
        .catch(error => {
            console.error('Error toggling wishlist:', error);
            
            // handle error
            if (error.response && error.response.status === 401) {
                showNotification('Silakan login terlebih dahulu', 'warning');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            } else {
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        });
}

/**
 * update tampilan wishlist button
 */
function updateWishlistButton(button, isSaved) {
    const icon = button.querySelector('svg');
    
    setTimeout(() => {
        if (icon) {
            icon.setAttribute('fill', isSaved ? 'currentColor' : 'none');
            icon.classList.toggle('text-red-600', isSaved);
            icon.classList.toggle('text-gray-600', !isSaved);
        }
        
        // scale up animation
        button.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);
    }, 150);
}

/**
 * tampilkan notification toast dengan smooth animation
 */
function showNotification(message, type = 'success') {
    // hapus notification yang ada
    const existingNotification = document.querySelector('.wishlist-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // buat notification element
    const notification = document.createElement('div');
    notification.className = `wishlist-notification fixed bottom-4 right-4 z-[1100] flex items-center space-x-3 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-gray-900 text-white' : 'bg-red-600 text-white'
    }`;
    
    notification.innerHTML = `
        <svg class="w-5 h-5 ${type === 'success' ? 'text-green-400' : 'text-white'}" fill="currentColor" viewBox="0 0 20 20">
            ${type === 'success' 
                ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
            }
        </svg>
        <span class="text-sm font-medium">${message}</span>
    `;
    
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(20px)';
    
    document.body.appendChild(notification);
    
    // trigger animation
    requestAnimationFrame(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    });
    
    // auto remove setelah 3 detik
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

/**
 * batch toggle wishlist untuk multiple items
 */
async function batchToggleWishlist(problemIds) {
    const results = [];
    
    for (const problemId of problemIds) {
        try {
            const response = await fetch(`/student/wishlist/${problemId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            results.push({ problemId, success: data.success, saved: data.saved });
        } catch (error) {
            results.push({ problemId, success: false, error: error.message });
        }
    }
    
    return results;
}

/**
 * check wishlist status untuk problem
 */
async function checkWishlistStatus(problemId) {
    try {
        const response = await fetch(`/student/wishlist/${problemId}/check`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        return data.saved || false;
    } catch (error) {
        console.error('Error checking wishlist status:', error);
        return false;
    }
}

/**
 * update wishlist count di UI
 */
function updateWishlistCount(count) {
    const countElements = document.querySelectorAll('[data-wishlist-count]');
    countElements.forEach(element => {
        element.textContent = count;
        
        // add bounce animation
        element.style.transform = 'scale(1.2)';
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 200);
    });
}

/**
 * listen untuk wishlist updates dan sync UI
 */
function initWishlistSync() {
    window.addEventListener('wishlistUpdated', (event) => {
        const { problemId, saved } = event.detail;
        
        // update semua wishlist buttons untuk problem ini
        const buttons = document.querySelectorAll(`[data-problem-id="${problemId}"]`);
        buttons.forEach(button => {
            updateButtonState(button, saved);
        });
    });
}

/**
 * TAMBAHAN: Alpine.js component untuk wishlist button
 * digunakan di blade dengan x-data="wishlistButton(problemId, initialSaved)"
 */
window.wishlistButton = function(problemId, initialSaved = false) {
    return {
        problemId: problemId,
        saved: initialSaved,
        loading: false,
        
        async toggle() {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const response = await fetch(`/student/wishlist/${this.problemId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('gagal toggle wishlist');
                }

                const data = await response.json();
                
                if (data.success) {
                    this.saved = data.saved;
                    showNotification(data.message);
                    
                    // trigger animation
                    if (this.saved) {
                        const button = this.$el.querySelector('button');
                        if (button) {
                            button.classList.add('animate-bounce');
                            setTimeout(() => {
                                button.classList.remove('animate-bounce');
                            }, 500);
                        }
                    }
                    
                    // dispatch event untuk sync
                    window.dispatchEvent(new CustomEvent('wishlistUpdated', {
                        detail: { problemId: this.problemId, saved: this.saved }
                    }));
                }
            } catch (error) {
                console.error('error toggle wishlist:', error);
                showNotification('terjadi kesalahan, silakan coba lagi', 'error');
            } finally {
                this.loading = false;
            }
        }
    };
};

// inisialisasi saat DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWishlistSync);
} else {
    initWishlistSync();
}

// export functions untuk digunakan di tempat lain
export {
    toggleWishlist,
    checkWishlistStatus,
    batchToggleWishlist,
    updateWishlistCount,
    showNotification
};
