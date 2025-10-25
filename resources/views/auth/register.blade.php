<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Dantata Foods</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; display:flex; align-items:center; justify-content:center; height:100vh}
        .card {background:white; padding:24px; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.08); width:420px}
        .form-group {margin-bottom:12px}
        label{display:block;margin-bottom:6px}
        input{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px}
        .btn {background:#4a7cf6;color:white;padding:10px 14px;border:none;border-radius:6px;cursor:pointer;width:100%}
        .link {margin-top:12px;display:block;text-align:center}
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-bottom:12px">Create an account</h2>
        <form id="registerForm">
            <div class="form-group">
                <label>First name</label>
                <input id="first_name" name="first_name" required />
            </div>
            <div class="form-group">
                <label>Last name</label>
                <input id="last_name" name="last_name" required />
            </div>
            <div class="form-group">
                <label>Email</label>
                <input id="email" type="email" name="email" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input id="password" type="password" name="password" required />
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required />
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
        <a class="link" href="{{ route('login') }}">Already have an account? Sign in</a>
        <div id="error" style="color:#c82333;margin-top:10px;display:none"></div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function submitRegister(e) {
            e.preventDefault();
            document.getElementById('error').style.display = 'none';
            const payload = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            try {
                const res = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || JSON.stringify(data));

                // After successful register, redirect to login
                window.location.href = '{{ route('login') }}';
            } catch (err) {
                document.getElementById('error').textContent = err.message || String(err);
                document.getElementById('error').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function(){
            document.getElementById('registerForm').onsubmit = submitRegister;
        });
    </script>
</body>
</html>
