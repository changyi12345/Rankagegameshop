<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Admin Login - RanKage Game Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Myanmar:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-dark-bg text-light-text font-sans antialiased relative">
    <!-- Animated Network Background -->
    <canvas id="networkCanvas" class="fixed inset-0 w-full h-full pointer-events-none" style="z-index: 0;"></canvas>
    
    <div class="min-h-screen flex items-center justify-center px-4 py-8 relative z-10">
        <div class="max-w-md w-full my-8">
            <!-- Logo Card -->
            <div class="card mb-6 text-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <span class="text-5xl">🎮</span>
                </div>
                <h1 class="text-3xl font-bold text-light-text mb-2">RanKage</h1>
                <p class="text-gray-400 mb-1">Admin Panel</p>
                <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>
            
            <!-- Login Card -->
            <div class="card">
                <h2 class="text-xl font-bold text-light-text mb-6">Sign In</h2>
                
                <div x-data="adminLoginData()">
                    <form @submit.prevent="login">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Email or Phone</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           x-model="form.login" 
                                           required 
                                           class="input-field pl-12" 
                                           placeholder="admin@rankage.com or 09123456789"
                                           autofocus>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input type="password" 
                                           x-model="form.password" 
                                           required 
                                           class="input-field pl-12" 
                                           placeholder="Enter your password">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.remember" id="remember" class="w-4 h-4 rounded border-gray-700 bg-dark-base text-primary focus:ring-2 focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-400">Remember me</span>
                                </label>
                                <a href="#" class="text-sm text-primary hover:text-blue-400 transition-colors">Forgot password?</a>
                            </div>

                            <div x-show="error" 
                                 x-transition
                                 class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm flex items-center space-x-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="error"></span>
                            </div>

                            <button type="submit" 
                                    class="btn-primary w-full py-4 text-base font-semibold"
                                    :disabled="loading">
                                <span x-show="!loading" class="flex items-center justify-center">
                                    <span>Sign In</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                                <span x-show="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Logging in...
                                </span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-800 text-center">
                        <a href="/login" class="text-sm text-gray-400 hover:text-primary transition-colors">
                            ← Back to User Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 text-center mb-8">
                <p class="text-xs text-gray-500">
                    🔒 Secure admin access only. Unauthorized access is prohibited.
                </p>
            </div>
        </div>
    </div>

<script>
// Animated Network Background
(function() {
    const canvas = document.getElementById('networkCanvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let animationId;
    
    // Set canvas size
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    
    // Network nodes
    const nodes = [];
    const nodeCount = 50;
    const connectionDistance = 150;
    const nodeSpeed = 0.5;
    
    // Create nodes
    class Node {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.vx = (Math.random() - 0.5) * nodeSpeed;
            this.vy = (Math.random() - 0.5) * nodeSpeed;
            this.radius = Math.random() * 2 + 1;
        }
        
        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            // Bounce off edges
            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
            
            // Keep in bounds
            this.x = Math.max(0, Math.min(canvas.width, this.x));
            this.y = Math.max(0, Math.min(canvas.height, this.y));
        }
        
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = '#00C897'; // Green color
            ctx.fill();
        }
    }
    
    // Initialize nodes
    for (let i = 0; i < nodeCount; i++) {
        nodes.push(new Node());
    }
    
    // Draw connections
    function drawConnections() {
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < connectionDistance) {
                    const opacity = 1 - (distance / connectionDistance);
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.strokeStyle = `rgba(0, 200, 151, ${opacity * 0.3})`; // Green with opacity
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }
    }
    
    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Update and draw nodes
        nodes.forEach(node => {
            node.update();
            node.draw();
        });
        
        // Draw connections
        drawConnections();
        
        animationId = requestAnimationFrame(animate);
    }
    
    // Start animation
    animate();
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (animationId) {
            cancelAnimationFrame(animationId);
        }
    });
})();

function adminLoginData() {
    return {
        form: {
            login: '',
            password: '',
            remember: false
        },
        loading: false,
        error: '',
        async login() {
            this.loading = true;
            this.error = '';
            
            try {
                const res = await fetch('/admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = '/admin/dashboard';
                } else {
                    this.error = data.message || 'Invalid credentials';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\RanKeagegmshop\resources\views/admin/login.blade.php ENDPATH**/ ?>