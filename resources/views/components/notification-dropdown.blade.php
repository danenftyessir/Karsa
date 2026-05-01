{{-- components/notification-dropdown.blade.php --}}
<div x-data="notificationDropdown()" x-init="init()" class="relative">
    {{-- notification bell button --}}
    <button @click="toggleDropdown()" 
            type="button"
            class="relative p-2 text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        {{-- unread badge --}}
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-red-600 rounded-full">
        </span>
    </button>

    {{-- dropdown menu --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        
        <div class="max-h-96 overflow-y-auto">
            {{-- header --}}
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                    <button @click="markAllAsRead()" 
                            x-show="unreadCount > 0"
                            class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Tandai Semua Dibaca
                    </button>
                </div>
            </div>

            {{-- notifikasi list --}}
            <div class="divide-y divide-gray-100">
                <template x-if="notifications.length === 0">
                    <div class="px-4 py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada notifikasi</p>
                    </div>
                </template>

                <template x-for="notification in notifications" :key="notification.id">
                    <div @click="handleNotificationClick(notification)"
                         class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors"
                         :class="{'bg-blue-50': !notification.is_read}">
                        <div class="flex items-start gap-3">
                            {{-- icon --}}
                            <div class="flex-shrink-0 mt-1">
                                <span class="text-2xl" x-text="getNotificationIcon(notification.type)"></span>
                            </div>

                            {{-- content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                <p class="text-xs text-gray-600 mt-1" x-text="notification.message"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                            </div>

                            {{-- unread indicator --}}
                            <div class="flex-shrink-0" x-show="!notification.is_read">
                                <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- footer --}}
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('notifications.index') }}" 
                   class="block text-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,

        async init() {
            await this.loadNotifications();
            // refresh setiap 30 detik
            setInterval(() => this.loadNotifications(), 30000);
        },

        async toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                await this.loadNotifications();
            }
        },

        async loadNotifications() {
            try {
                const response = await fetch('{{ route("notifications.latest") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('gagal load notifikasi:', error);
            }
        },

        async handleNotificationClick(notification) {
            // tandai sebagai dibaca jika belum dibaca
            if (!notification.is_read) {
                try {
                    await fetch(`/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    // reload notifications untuk update status
                    await this.loadNotifications();
                } catch (error) {
                    console.error('gagal mark as read:', error);
                }
            }
            
            // tutup dropdown setelah klik
            // TIDAK ada redirect, notifikasi hanya ditandai sebagai dibaca
            this.open = false;
        },

        async markAllAsRead() {
            try {
                const response = await fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    await this.loadNotifications();
                }
            } catch (error) {
                console.error('gagal mark all as read:', error);
            }
        },

        getNotificationIcon(type) {
            const icons = {
                'application_submitted': '📝',
                'application_accepted': '✅',
                'application_rejected': '❌',
                'report_submitted': '📄',
                'project_update': '🔔',
                'message': '💬',
                'review_received': '⭐',
                'default': '🔔'
            };
            return icons[type] || icons['default'];
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) {
                return 'Baru saja';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} menit yang lalu`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} jam yang lalu`;
            } else if (diffInSeconds < 604800) {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} hari yang lalu`;
            } else {
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            }
        }
    }
}
</script>
@endpush