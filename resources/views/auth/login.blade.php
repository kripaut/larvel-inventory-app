<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Login</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body.login-bg {
      min-height: 100vh;
      background: linear-gradient(135deg, #2563EB 0%, #1E293B 100%);
      animation: gradientBG 8s ease-in-out infinite alternate;
    }
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }
    .login-card {
      background: rgba(255,255,255,0.85);
      box-shadow: 0 8px 32px rgba(30,41,59,0.18);
      border-radius: 1.5rem;
      padding: 2.5rem 2rem;
      max-width: 400px;
      margin: auto;
      margin-top: 7vh;
      backdrop-filter: blur(8px);
    }
    .login-card .form-control {
      border-radius: 0.75rem;
    }
    .login-card .btn-primary {
      width: 100%;
      font-weight: 600;
      font-size: 1.1rem;
      padding: 0.75rem;
      margin-top: 1rem;
    }
    .login-card .form-check-label {
      font-size: 0.95rem;
    }
    .login-card .forgot-link {
      font-size: 0.95rem;
      color: #2563EB;
      text-decoration: none;
      transition: color 0.2s;
    }
    .login-card .forgot-link:hover {
      color: #1746a2;
      text-decoration: underline;
    }
    .login-logo {
      font-size: 2.2rem;
      font-weight: 700;
      color: #2563EB;
      letter-spacing: 0.03em;
      margin-bottom: 1.5rem;
      text-align: center;
    }
  </style>
</head>
<body class="login-bg fade-in">
  <div class="d-flex flex-column justify-content-center align-items-center min-vh-100">
    <div class="login-card glass-card">
      <div class="login-logo">
        <i class="fa-solid fa-boxes-stacked me-2"></i>InventoryPro
      </div>
      <form id="loginForm" action="{{ url('/login') }}" method="POST" autocomplete="off">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
         @endif
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required autofocus>
        </div>
        <div class="mb-2">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary">Login <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i></button>
      </form>
    </div>
    <footer class="mt-4 text-white-50 small">&copy; 2026 InventoryPro. All rights reserved.</footer>
  </div>
  <div id="globalLoader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.78);z-index:9999;display:none;">
    <div class="loader"></div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
