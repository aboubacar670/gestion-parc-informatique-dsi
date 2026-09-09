<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion DSI</title>
    <!-- Google Fonts & Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            
            <div class="login-header">
                <div class="login-logo">
                    <i class="fa-solid fa-server"></i>
                </div>
                <h1 class="login-title">DSI - MEBAPLN</h1>
                <p class="login-subtitle">Gestion & Cartographie des Infrastructures</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="list-style: none; margin: 0; padding: 0;">
                        @foreach($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Adresse Email</label>
                    <div style="position: relative;">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="exemple@dsi.gov.bf" required autofocus style="padding-left: 2.5rem;">
                        <i class="fa-solid fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required style="padding-left: 2.5rem;">
                        <i class="fa-solid fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #cbd5e1; cursor: pointer;">
                        <input type="checkbox" name="remember" style="accent-color: hsl(var(--primary)); cursor: pointer;"> Se souvenir de moi
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 600;">
                    Se connecter <i class="fa-solid fa-right-to-bracket"></i>
                </button>
                
            </form>
            
        </div>
    </div>
</body>
</html>
