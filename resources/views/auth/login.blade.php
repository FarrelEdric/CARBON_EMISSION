<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — ACE AirNav Carbon Emission</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        /* Ambient 3D Deep Blue Canvas Background */
        body {
            margin: 0;
            padding: 1.5rem;
            background-color: #0243b8;
            background-image: 
                radial-gradient(circle at 50% 30%, rgba(3, 102, 255, 0.45) 0%, rgba(1, 58, 168, 0.7) 50%, #012b7a 100%),
                url("{{ asset('images/login-bg.jpg') }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle glowing light accents */
        .ambient-glow {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 195, 255, 0.22) 0%, rgba(1, 80, 220, 0.08) 50%, transparent 70%);
            filter: blur(50px);
            pointer-events: none;
            z-index: 1;
        }

        /* Presentation Wrapper */
        .login-wrapper {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        /* Big Translucent Outer Frame (matching the wide rounded backdrop plate in the image) */
        .backdrop-frame {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 36px;
            box-shadow: 
                0 25px 60px -15px rgba(0, 15, 60, 0.35),
                inset 0 1px 1px 0 rgba(255, 255, 255, 0.2);
            min-height: 520px;
            padding: 3rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        /* Frosted Glassmorphism Login Card (Compact, neat vertical box) */
        .glass-card {
            width: 100% !important;
            max-width: 350px !important;
            margin: 0 auto !important;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(28px) saturate(175%);
            -webkit-backdrop-filter: blur(28px) saturate(175%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 26px;
            box-shadow: 
                0 30px 65px -10px rgba(0, 15, 60, 0.45),
                0 10px 25px -5px rgba(0, 0, 0, 0.15),
                inset 0 1px 1px 0 rgba(255, 255, 255, 0.45),
                inset 0 -1px 1px 0 rgba(0, 0, 0, 0.12);
            padding: 2.25rem 2rem;
            box-sizing: border-box;
        }

        /* Clean White Form Input */
        .white-input {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
            line-height: 1.25rem !important;
            padding: 0.65rem 0.85rem !important;
            width: 100% !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            display: block;
            box-sizing: border-box;
        }
        .white-input::placeholder {
            color: #94a3b8 !important;
            font-size: 0.85rem !important;
        }
        .white-input:focus {
            outline: none !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.35) !important;
        }

        /* Dark Navy Action Button */
        .btn-signin {
            background-color: #08213b;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.68rem 1rem;
            border-radius: 8px;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(8, 33, 59, 0.35);
            display: block;
            text-align: center;
        }
        .btn-signin:hover {
            background-color: #0c2d50;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(8, 33, 59, 0.45);
        }
        .btn-signin:active {
            transform: translateY(0);
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            body {
                padding: 1rem !important;
            }
            .backdrop-frame {
                border-radius: 24px;
                padding: 2rem 1rem;
                min-height: auto;
            }
            .glass-card {
                max-width: 340px !important;
                padding: 1.75rem 1.5rem;
                border-radius: 22px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.75rem !important;
            }
            .backdrop-frame {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                padding: 0 !important;
                min-height: auto;
            }
            .glass-card {
                max-width: 100% !important;
                padding: 1.75rem 1.25rem;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient glowing light accents -->
    <div class="ambient-glow -top-24 -left-24"></div>
    <div class="ambient-glow -bottom-32 -right-32" style="background: radial-gradient(circle, rgba(2, 132, 199, 0.28) 0%, transparent 70%);"></div>

    <!-- Outer Presentation Container (with the layered backdrop plate) -->
    <div class="login-wrapper">
        <div class="backdrop-frame">

            <!-- Center Frosted Glassmorphism Card (Compact Box) -->
            <div class="glass-card">
                
                <!-- Top "Your logo" Area (clean typography like the reference) -->
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <span style="color: #ffffff; font-size: 1rem; font-weight: 600; letter-spacing: 0.025em;">
                        Your logo
                    </span>
                </div>

                <!-- Login Heading -->
                <h1 style="color: #ffffff; font-size: 1.45rem; font-weight: 700; letter-spacing: -0.025em; margin: 0 0 1.25rem 0; text-align: left;">
                    Login
                </h1>

                <!-- Error Alerts (if any) -->
                @if ($errors->any())
                <div style="margin-bottom: 1rem; background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(248, 113, 113, 0.4); color: #ffffff; font-size: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 8px;">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" style="display: flex; flex-direction: column; gap: 0.95rem;">
                    @csrf

                    <!-- Email / Username Input -->
                    <div>
                        <label for="username" style="display: block; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.95); margin-bottom: 0.35rem; text-align: left; text-transform: uppercase; letter-spacing: 0.04em;">
                            USERNAME
                        </label>
                        <input id="username"
                               type="text"
                               name="username"
                               value="{{ old('username') ?? old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="username"
                               class="white-input">
                    </div>

                    <!-- Password Input -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" style="display: block; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.95); margin-bottom: 0.35rem; text-align: left; letter-spacing: 0.02em;">
                            Password
                        </label>
                        <div style="position: relative; width: 100%;">
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Password"
                                   class="white-input"
                                   style="padding-right: 2.5rem !important;">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    tabindex="-1"
                                    style="position: absolute; top: 0; bottom: 0; right: 0; padding-right: 0.75rem; display: flex; align-items: center; background: none; border: none; cursor: pointer; color: #94a3b8;"
                                    title="Lihat password">
                                <!-- Eye closed -->
                                <svg x-show="!showPassword" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <!-- Eye open -->
                                <svg x-show="showPassword" style="width: 1rem; height: 1rem; display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me (Hidden) -->
                    <input type="hidden" name="remember" value="1">

                    <!-- Sign in Button -->
                    <div style="margin-top: 0.5rem;">
                        <button type="submit" id="signin-button" class="btn-signin">
                            Sign in
                        </button>
                    </div>
                </form>

                <!-- Subtitle info -->
                <p style="text-align: center; font-size: 0.65rem; color: rgba(255,255,255,0.65); letter-spacing: 0.08em; margin: 1.5rem 0 0 0; text-transform: uppercase;">
                    AIRNAV CARBON EMISSION &bull; ACE V1.0
                </p>

            </div>
        </div>
    </div>

</body>
</html>
