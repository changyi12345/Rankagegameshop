@extends('layouts.user')

@section('title', 'Support - RanKage Game Shop')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-2xl">
    <h1 class="text-2xl font-bold text-light-text mb-6 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full mr-3"></span>
        Support & Help
    </h1>

    <div class="space-y-6">
        <!-- Contact Methods -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Contact Us</h2>
            <div class="space-y-4">
                <a href="https://t.me/rankage_support" target="_blank" class="flex items-center space-x-4 p-4 bg-dark-base rounded-xl hover:bg-dark-card transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-[#0088cc]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#0088cc]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.165 1.657-.878 5.686-1.241 7.543-.171.872-.508 1.161-.835 1.19-.713.056-1.253-.47-1.942-.922-1.077-.722-1.686-1.17-2.731-1.876-1.218-.835-.428-1.293.266-2.043.182-.195 3.247-2.978 3.307-3.23.007-.032.014-.154-.056-.213-.07-.06-.173-.04-.248-.024-.106.023-1.789 1.14-5.058 3.347-.479.33-.913.49-1.302.482-.428-.008-1.252-.242-1.865-.44-.752-.243-1.349-.374-1.297-.789.027-.216.325-.437.894-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.14.118.095.151.223.167.312.016.09.036.297.02.458z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-light-text">Telegram</h3>
                        <p class="text-gray-400 text-sm">@rankage_support</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <div class="flex items-center space-x-4 p-4 bg-dark-base rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center">
                        <span class="text-2xl">📞</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-light-text">Phone</h3>
                        <p class="text-gray-400 text-sm">09-123456789</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4 p-4 bg-dark-base rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-secondary/20 flex items-center justify-center">
                        <span class="text-2xl">📧</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-light-text">Email</h3>
                        <p class="text-gray-400 text-sm">support@rankage.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Frequently Asked Questions</h2>
            <div class="space-y-4" x-data="{ openFaq: null }">
                <div>
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex items-center justify-between p-4 bg-dark-base rounded-xl hover:bg-dark-card transition-colors">
                        <span class="font-semibold text-light-text text-left">How long does top-up take?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-transition class="p-4 bg-dark-base rounded-xl mt-2">
                        <p class="text-gray-400 text-sm">Most top-ups are processed instantly via G2Bulk API. In rare cases, it may take up to 5-10 minutes.</p>
                    </div>
                </div>

                <div>
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex items-center justify-between p-4 bg-dark-base rounded-xl hover:bg-dark-card transition-colors">
                        <span class="font-semibold text-light-text text-left">What payment methods do you accept?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-transition class="p-4 bg-dark-base rounded-xl mt-2">
                        <p class="text-gray-400 text-sm">We accept WavePay, KBZ Pay, and manual bank transfer. You can also use your wallet balance.</p>
                    </div>
                </div>

                <div>
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex items-center justify-between p-4 bg-dark-base rounded-xl hover:bg-dark-card transition-colors">
                        <span class="font-semibold text-light-text text-left">What if my order fails?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-transition class="p-4 bg-dark-base rounded-xl mt-2">
                        <p class="text-gray-400 text-sm">If your order fails, the amount will be automatically refunded to your wallet within 24 hours. Contact support if you need immediate assistance.</p>
                    </div>
                </div>

                <div>
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full flex items-center justify-between p-4 bg-dark-base rounded-xl hover:bg-dark-card transition-colors">
                        <span class="font-semibold text-light-text text-left">How do I check my order status?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-transition class="p-4 bg-dark-base rounded-xl mt-2">
                        <p class="text-gray-400 text-sm">Go to "Orders" in the bottom navigation to view all your orders and their current status.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="card">
            <h2 class="text-xl font-bold text-light-text mb-4">Send us a Message</h2>
            <form @submit.prevent="submitMessage" x-data="supportFormData()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Subject</label>
                        <input type="text" 
                               x-model="form.subject" 
                               required 
                               class="input-field" 
                               placeholder="What is this about?">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Message</label>
                        <textarea x-model="form.message" 
                                  required 
                                  rows="5"
                                  class="input-field" 
                                  placeholder="Describe your issue..."></textarea>
                    </div>
                    <div x-show="success" 
                         x-transition
                         class="bg-secondary/10 border border-secondary/30 rounded-xl p-4 text-secondary text-sm">
                        <span>Message sent successfully! We'll get back to you soon.</span>
                    </div>
                    <button type="submit" 
                            class="btn-primary w-full py-3"
                            :disabled="loading">
                        <span x-show="!loading">Send Message</span>
                        <span x-show="loading" class="flex items-center justify-center">
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
    </div>
</div>

@push('scripts')
<script>
function supportFormData() {
    return {
        form: {
            subject: '',
            message: ''
        },
        loading: false,
        success: false,
        
        async submitMessage() {
            this.loading = true;
            this.success = false;
            
            try {
                const res = await fetch('/support/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    this.success = true;
                    this.form = { subject: '', message: '' };
                    setTimeout(() => this.success = false, 5000);
                }
            } catch (e) {
                alert('Failed to send message');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
