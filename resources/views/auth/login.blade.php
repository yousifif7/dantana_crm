<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Dantata Foods</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/app-utils.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/showcase.css') }}">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #1a3328 0%, #2d6a4f 50%, #1a3328 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-wrap { width: 100%; max-width: 420px; padding: 24px; }
        .login-brand {
            text-align: center;
            color: white;
            margin-bottom: 32px;
        }
        .login-brand h1 {
            font-size: 28px;
            letter-spacing: 3px;
            margin-bottom: 4px;
        }
        .login-brand p {
            font-size: 12px;
            letter-spacing: 2px;
            opacity: 0.7;
            text-transform: uppercase;
        }
        .login-brand .gold-ring {
            width: 72px;
            height: 72px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        .card {
            background: white;
            padding: 36px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card h2 { color: #1a3328; margin-bottom: 24px; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; color: #5c6b63; }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e8eeeb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 15px;
            transition: border-color 0.2s;
        }
        input:focus { outline: none; border-color: #4a9d7e; box-shadow: 0 0 0 3px rgba(74,157,126,0.15); }
        .btn {
            background: linear-gradient(135deg, #2d6a4f, #4a9d7e);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            transition: transform 0.15s;
        }
        .btn:hover { transform: translateY(-1px); }
        .demo-hint {
            margin-top: 20px;
            padding: 12px;
            background: #f0faf6;
            border-radius: 8px;
            font-size: 12px;
            color: #5c6b63;
            line-height: 1.6;
        }
        .demo-hint strong { color: #1a3328; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-brand">
            <div class="gold-ring">🌿</div>
            <h1>DANTATA</h1>
            <p>Foods · UBMS</p>
        </div>
        <div class="card">
            <h2>Sign in to your account</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" required placeholder="you@dantatafoods.com" />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required placeholder="••••••••" />
                </div>
                <button class="btn" type="submit">Sign in</button>
            </form>
            <div class="demo-hint">
                <strong>Demo accounts</strong> (password: <code>password</code>)<br>
                MD: md@dantatafoods.com · CFO: cfo@dantatafoods.com<br>
                GM: gm.production@dantatafoods.com · Chairman: chairman@dantatafoods.com
            </div>
            <div id="error" style="color:#c82333;margin-top:14px;display:none;font-size:14px;"></div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function submitLogin(e) {
            e.preventDefault();
            document.getElementById('error').style.display = 'none';
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();
                if (!res.ok) {
                    const err = new Error(`API /api/login returned ${res.status}: ${JSON.stringify(data)}`);
                    throw err;
                }

                const token = data.token ?? data.access_token ?? null;
                if (!token) throw new Error('Login did not return a token');

                localStorage.setItem('api_token', token);
                showToast('Signed in successfully', 'success');
                setTimeout(() => { window.location.href = '/dashboard'; }, 400);
            } catch (err) {
                const msg = typeof parseApiError === 'function' ? parseApiError(err, 'Login failed') : (err.message || String(err));
                document.getElementById('error').textContent = msg;
                document.getElementById('error').style.display = 'block';
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        }

        document.getElementById('loginForm').onsubmit = submitLogin;
    </script>
</body>
</html>
