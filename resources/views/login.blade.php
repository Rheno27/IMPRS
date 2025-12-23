@extends('layouts.app')
@section('body-class', 'no-scroll-page')
@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif !important;
            background: url('./image/green_bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        @media (max-width: 600px) {
            body {
                background-size: cover;
                background-attachment: scroll;
            }
        }
    </style>
@endsection

@section('content')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const header = document.querySelector('.site-header');
            if (header) header.classList.add('login-header-override');
        });
    </script>

    <div class="login-wrapper">
        <div class="login-card">
            <h2 class="login-title">Login</h2>
            <p class="login-subtitle">Silahkan masukkan username dan password yang sesuai</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="login-form-group">
                    <label for="username" class="login-label">Username</label>
                    <input type="text" id="username" name="username" class="login-input" placeholder="Masukkan username"
                        required>
                </div>

                <div class="login-form-group">
                    <label for="password" class="login-label">Password</label>
                    <input type="password" id="password" name="password" class="login-input" placeholder="Masukkan password"
                        required>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>

            @if($errors->has('login'))
                <div class="login-error-msg">{{ $errors->first('login') }}</div>
            @endif

            <div class="login-forgot-pass">
                Lupa Password? <a href="#">Hubungi Admin</a>
            </div>
        </div>
    </div>
@endsection