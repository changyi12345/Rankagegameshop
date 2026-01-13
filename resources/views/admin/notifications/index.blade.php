@extends('layouts.admin')

@section('title', 'Notifications - Admin Panel')
@section('page-title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div x-data="notificationsData()">
        <!-- Telegram Bot Settings -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Telegram Bot Settings</h2>
            
            <form @submit.prevent="saveBotSettings">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Bot Token</label>
                        <input type="text" 
                               x-model="botForm.token" 
                               class="input-field" 
                               placeholder="Enter Telegram Bot Token"
                               value="{{ $bot_settings->token ?? '' }}">
                        <p class="text-xs text-gray-500 mt-1">Get token from @BotFather on Telegram</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Admin Chat ID</label>
                        <input type="text" 
                               x-model="botForm.admin_chat_id" 
                               class="input-field" 
                               placeholder="Enter Admin Chat ID"
                               value="{{ $bot_settings->admin_chat_id ?? '' }}">
                        <p class="text-xs text-gray-500 mt-1">Your Telegram chat ID for notifications</p>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="botForm.notify_new_order" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Notify on New Order</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="botForm.notify_payment_pending" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Notify on Payment Pending</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="botForm.notify_low_balance" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Notify on Low Balance</span>
                        </label>
                    </div>

                    <div x-show="botError" 
                         x-transition
                         class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                        <span x-text="botError"></span>
                    </div>

                    <button type="submit" 
                            class="btn-primary w-full py-3"
                            :disabled="botLoading">
                        <span x-show="!botLoading">Save Bot Settings</span>
                        <span x-show="botLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Test Notification -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Test Notification</h2>
            <button @click="sendTestNotification()" 
                    class="btn-outline w-full py-3"
                    :disabled="testing">
                <span x-show="!testing">Send Test Notification</span>
                <span x-show="testing" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        </div>

        <!-- Broadcast Message -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Broadcast Message</h2>
            
            <form @submit.prevent="sendBroadcast">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Message</label>
                        <textarea x-model="broadcastForm.message" 
                                  rows="4"
                                  class="input-field" 
                                  placeholder="Enter broadcast message..."></textarea>
                    </div>

                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   x-model="broadcastForm.send_to_all" 
                                   class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-300">Send to all users</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="btn-secondary w-full py-3"
                            :disabled="broadcastLoading">
                        <span x-show="!broadcastLoading">Send Broadcast</span>
                        <span x-show="broadcastLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification History -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Notification History</h2>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($notifications ?? [] as $notification)
                <div class="bg-dark-base rounded-xl p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="text-light-text font-semibold">{{ $notification->type ?? 'Notification' }}</p>
                            <p class="text-gray-400 text-sm">{{ $notification->message ?? 'No message' }}</p>
                        </div>
                        <span class="text-gray-500 text-xs">{{ $notification->created_at->format('M d, h:i A') }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($notification->status === 'sent')
                            <span class="badge badge-success">Sent</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <span class="text-4xl block mb-2">📬</span>
                    <p>No notifications sent yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationsData() {
    return {
        botForm: {
            token: '{{ $bot_settings->token ?? '' }}',
            admin_chat_id: '{{ $bot_settings->admin_chat_id ?? '' }}',
            notify_new_order: {{ $bot_settings->notify_new_order ?? true ? 'true' : 'false' }},
            notify_payment_pending: {{ $bot_settings->notify_payment_pending ?? true ? 'true' : 'false' }},
            notify_low_balance: {{ $bot_settings->notify_low_balance ?? true ? 'true' : 'false' }}
        },
        broadcastForm: {
            message: '',
            send_to_all: true
        },
        botLoading: false,
        broadcastLoading: false,
        testing: false,
        botError: '',
        
        async saveBotSettings() {
            this.botLoading = true;
            this.botError = '';
            
            try {
                const res = await fetch('/admin/notifications/save-bot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.botForm)
                });
                
                const data = await res.json();
                
                if (!data.success) {
                    this.botError = data.message || 'Failed to save settings';
                } else {
                    alert('Bot settings saved successfully!');
                }
            } catch (e) {
                this.botError = 'An error occurred. Please try again.';
            } finally {
                this.botLoading = false;
            }
        },
        
        async sendTestNotification() {
            this.testing = true;
            try {
                const res = await fetch('/admin/notifications/test', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                alert(data.success ? 'Test notification sent!' : 'Failed to send notification');
            } catch (e) {
                alert('An error occurred');
            } finally {
                this.testing = false;
            }
        },
        
        async sendBroadcast() {
            this.broadcastLoading = true;
            try {
                const res = await fetch('/admin/notifications/broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.broadcastForm)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Broadcast sent successfully!');
                    this.broadcastForm.message = '';
                } else {
                    alert('Failed to send broadcast');
                }
            } catch (e) {
                alert('An error occurred');
            } finally {
                this.broadcastLoading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
