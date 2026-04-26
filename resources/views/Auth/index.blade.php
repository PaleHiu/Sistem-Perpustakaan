<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - SIPUS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('ui_auth/style.css') }}">
</head>
<body>

    <div class="container">
        <div class="brand-section">
            <h1 class="logo-title">SIPUS</h1>
            <img src="{{ asset('ui_auth/wosh-logo.svg') }}" alt="Swoosh Logo" class="swoosh-line">
            <h2 class="logo-subtitle">Sistem Perpustakaan</h2>
            <p class="logo-group">Kelompok 2</p>
        </div>

        <div class="login-section">
            
            <div class="glass-card" id="loginCard">
                <h2 class="form-title">Login</h2>
                
                <div class="welcome-text">
                    <h3>Welcome to SIPUS</h3>
                    <p>The Digital Library Information System</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    @error('email')
                        <div style="color: #ff5f5f; font-size: 0.85rem; margin-bottom: 15px; text-align: center; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 10px;">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="input-group">
                        <img src="{{ asset('ui_auth/email.svg') }}" alt="Email Icon" class="input-icon">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <img src="{{ asset('ui_auth/gembok.svg') }}" alt="Password Icon" class="input-icon">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="remember-me">
                        <label>
                            <input type="checkbox" name="remember"> 
                            <span class="checkmark"></span>
                            Remember Me
                        </label>
                    </div>

                    <button type="submit" class="login-btn">Log In</button>
                </form>

                <div class="card-footer-left">
                    <p><i>Forgot your password?</i> <strong>Contact the library administrator.</strong></p>
                    <p><i>Need help?</i> <strong>Click here</strong> or email us at <strong>admin@sipus.id</strong></p>
                </div>
                
                <div class="card-footer-center">
                    <p>Don't have an account? <a href="#" id="btnShowRegister">Sign Up</a></p>
                </div>
            </div>

            <div class="glass-card hidden" id="registerCard">
                <h2 class="form-title register-title">Create An Account</h2>
                
                <div class="welcome-text">
                    <h3>Welcome to SIPUS</h3>
                    <p>The Digital Library Information System</p>
                </div>

                <form action="#">
                    <div class="input-row">
                        <div class="input-group">
                            <input type="text" placeholder="First Name" class="no-icon" required>
                        </div>
                        <div class="input-group">
                            <input type="text" placeholder="Last Name" class="no-icon" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <img src="{{ asset('ui_auth/email.svg') }}" alt="Email Icon" class="input-icon">
                        <input type="email" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <img src="{{ asset('ui_auth/gembok.svg') }}" alt="Password Icon" class="input-icon">
                        <input type="password" placeholder="Create Password" required>
                    </div>

                    <div class="remember-me">
                        <label>
                            <input type="checkbox" required> 
                            <span class="checkmark"></span>
                            I agree to the Terms & Conditions
                        </label>
                    </div>

                    <button type="submit" class="login-btn">Create Account</button>
                </form>
                
                <div class="card-footer-center" style="margin-top: 45px;">
                    <p>Already have an account? <a href="#" id="btnShowLogin">Log In</a></p>
                </div>
            </div>

        </div>
    </div>

    <script>
        const loginCard = document.getElementById('loginCard');
        const registerCard = document.getElementById('registerCard');
        const btnShowRegister = document.getElementById('btnShowRegister');
        const btnShowLogin = document.getElementById('btnShowLogin');

        function resetAnimation(element) {
            element.style.animation = 'none';
            element.offsetHeight;
            element.style.animation = null; 
        }

        btnShowRegister.addEventListener('click', function(e) {
            e.preventDefault();
            loginCard.classList.add('hidden');
            registerCard.classList.remove('hidden');
            resetAnimation(registerCard);
        });

        btnShowLogin.addEventListener('click', function(e) {
            e.preventDefault();
            registerCard.classList.add('hidden');
            loginCard.classList.remove('hidden');
            resetAnimation(loginCard);
        });
    </script>
</body>
</html>