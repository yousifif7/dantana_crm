<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Dantata Foods</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; display:flex; align-items:center; justify-content:center; height:100vh}
        .card {background:white; padding:24px; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.08); width:360px}
        .form-group {margin-bottom:12px}
        label{display:block;margin-bottom:6px}
        input{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px}
        .btn {background:#4a7cf6;color:white;padding:10px 14px;border:none;border-radius:6px;cursor:pointer;width:100%}
        .link {margin-top:12px;display:block;text-align:center}
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-bottom:12px">Sign in</h2>
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required />
            </div>
            <button class="btn" type="submit">Sign in</button>
        </form>
        {{-- <a class="link" href="{{ route('register') }}">Create an account</a> --}}
        <div id="error" style="color:#c82333;margin-top:10px;display:none"></div>
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
                if (!res.ok) throw new Error(data.message || JSON.stringify(data));

                const token = data.token ?? data.access_token ?? null;
                const user = data.user ?? data;
                if (!token) throw new Error('Login did not return a token');

                localStorage.setItem('api_token', token);
                // redirect to dashboard
                window.location.href = '/dashboard';
            } catch (err) {
                document.getElementById('error').textContent = err.message || String(err);
                document.getElementById('error').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function(){
            document.getElementById('loginForm').onsubmit = submitLogin;
        });
    </script>
</body>
</html>
