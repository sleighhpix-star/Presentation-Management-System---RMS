<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – Research Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        :root { --teal:#0d9488; --teal-light:#14b8a6; --amber:#f59e0b; --ink:#0f172a; }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(30px,-20px) scale(1.05); }
            66%      { transform: translate(-20px,15px) scale(.96); }
        }
    </style>
</head>
<body>

<div style="min-height:100vh;background:var(--ink);display:flex;align-items:stretch;position:relative;overflow:hidden;">

    {{-- Animated background mesh --}}
    <div style="position:absolute;inset:0;pointer-events:none;z-index:0;">
        <div style="position:absolute;top:-20%;left:-10%;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(13,148,136,.15) 0%,transparent 70%);animation:drift 8s ease-in-out infinite;"></div>
        <div style="position:absolute;bottom:-20%;right:-10%;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.1) 0%,transparent 70%);animation:drift 10s ease-in-out infinite reverse;"></div>
        <div style="position:absolute;top:40%;left:30%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);animation:drift 12s ease-in-out infinite;"></div>
        <svg style="position:absolute;inset:0;width:100%;height:100%;opacity:.04" xmlns="http://www.w3.org/2000/svg">
            <defs><pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"><path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="1"/></pattern></defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    {{-- Left branding panel --}}
    <div style="flex:0 0 50%;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px;position:relative;z-index:1;" class="d-none d-lg-flex">
        <div style="width:100%;max-width:420px;">
            <div style="display:inline-flex;align-items:center;gap:10px;margin-bottom:48px;">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--teal),var(--teal-light));border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(13,148,136,.4);">
                    <i class="bi bi-journal-bookmark-fill" style="color:#fff;font-size:20px;"></i>
                </div>
                <span style="font-family:'Outfit',sans-serif;font-weight:800;font-size:20px;color:#fff;letter-spacing:.5px;">RESEARCH PMS</span>
            </div>

            <h1 style="font-family:'Outfit',sans-serif;font-size:64px;font-weight:800;color:#fff;line-height:1.05;letter-spacing:-2px;margin-bottom:24px;">
                Presentation<br>
                <span style="background:linear-gradient(135deg,var(--teal-light),var(--amber));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Management</span><br>
                System
            </h1>
            <p style="color:rgba(255,255,255,.45);font-size:17px;line-height:1.7;max-width:400px;">
                Track, manage and export research presentations across all colleges and campuses in one unified platform.
            </p>

            <div style="display:flex;gap:24px;margin-top:48px;">
                @foreach([['bi-journal-check','Records','Track all research'],['bi-bar-chart-fill','Analytics','Visual insights'],['bi-download','Export','Excel']] as $f)
                <div style="text-align:center;">
                    <div style="width:52px;height:52px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                        <i class="bi {{ $f[0] }}" style="color:var(--teal-light);font-size:22px;"></i>
                    </div>
                    <div style="color:#fff;font-weight:700;font-size:14px;font-family:'Outfit',sans-serif;">{{ $f[1] }}</div>
                    <div style="color:rgba(255,255,255,.3);font-size:12px;">{{ $f[2] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div style="width:1px;background:rgba(255,255,255,.06);margin:40px 0;" class="d-none d-lg-block"></div>

    {{-- Right login panel --}}
    <div style="flex:0 0 50%;display:flex;align-items:center;justify-content:center;padding:60px;position:relative;z-index:1;">
        <div style="width:100%;max-width:440px;margin:0 auto;">

            <div class="d-lg-none text-center mb-5">
                <div style="width:52px;height:52px;background:linear-gradient(135deg,var(--teal),var(--teal-light));border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 0 24px rgba(13,148,136,.4);">
                    <i class="bi bi-journal-bookmark-fill" style="color:#fff;font-size:22px;"></i>
                </div>
                <div style="font-family:'Outfit',sans-serif;font-weight:800;font-size:20px;color:#fff;">Research PMS</div>
            </div>

            <div style="margin-bottom:32px;">
                <h2 style="font-family:'Outfit',sans-serif;font-size:36px;font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:8px;">Welcome back</h2>
                <p style="color:rgba(255,255,255,.4);font-size:16px;">Sign in to your administrator account</p>
            </div>

            @if($errors->any())
            <div style="background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.3);color:#fda4af;border-radius:10px;font-size:13px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:9px;">
                <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-family:'Outfit',sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;">Email Address</label>
                    <div style="position:relative;">
                        <i class="bi bi-envelope" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.25);font-size:14px;z-index:1;"></i>
                        <input type="email" name="email" required autofocus
                               value="{{ old('email','admin@research.edu.ph') }}"
                               placeholder="admin@research.edu.ph"
                               style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:15px 14px 15px 46px;color:#fff;font-size:16px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;transition:border-color .2s,box-shadow .2s;"
                               onfocus="this.style.borderColor='var(--teal)';this.style.boxShadow='0 0 0 3px rgba(13,148,136,.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,.1)';this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block;font-family:'Outfit',sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:8px;">Password</label>
                    <div style="position:relative;">
                        <i class="bi bi-lock" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.25);font-size:14px;z-index:1;"></i>
                        <input type="password" name="password" required placeholder="••••••••"
                               style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:15px 14px 15px 46px;color:#fff;font-size:16px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;transition:border-color .2s,box-shadow .2s;"
                               onfocus="this.style.borderColor='var(--teal)';this.style.boxShadow='0 0 0 3px rgba(13,148,136,.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,.1)';this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:9px;margin-bottom:28px;">
                    <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    <label for="remember" style="color:rgba(255,255,255,.45);font-size:15px;cursor:pointer;">Keep me signed in</label>
                </div>

                <button type="submit"
                        style="width:100%;background:linear-gradient(135deg,var(--teal),var(--teal-light));color:#fff;border:none;border-radius:11px;padding:16px;font-size:17px;font-weight:800;font-family:'Outfit',sans-serif;cursor:pointer;letter-spacing:.3px;transition:all .2s;box-shadow:0 4px 20px rgba(13,148,136,.35);"
                        onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 28px rgba(13,148,136,.45)'"
                        onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(13,148,136,.35)'">
                    Sign In →
                </button>
            </form>

            <p style="text-align:center;color:rgba(255,255,255,.18);font-size:13px;margin-top:32px;">
                Research Presentation Management System &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>

</body>
</html>