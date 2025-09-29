@extends('layouts.app')
@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #337354;
            --white: #ffffff;
            --border-color-semitransparent: rgba(0, 0, 0, 0.1);
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: url('./image/green_bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* HEADER */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 40px;
            height: 80px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        /* LOGIN WRAPPER */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px;
        }

        .login-box {
            background: #fff;
            border-radius: 16px;
            padding: 50px 60px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 8px;
            font-size: 25px;
            font-weight: 700;
            color: #333;
        }

        .login-box p {
            margin-bottom: 28px;
            font-size: 18px;
            color: #555;
        }

        .login-box label {
            display: block;
            text-align: left;
            margin-bottom: 6px;
            font-weight: 550;
            font-size: 17px;
            color: #333;
        }

        /* Input & Button seragam panjangnya */
        .login-box input,
        .login-box button {
            width: 100%;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            border-radius: 6px;
        }

        .login-box input {
            padding: 13px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            outline: none;
            transition: border 0.2s;
        }

        .login-box input:focus {
            border-color: var(--primary-color);
        }

        .login-box button {
            padding: 14px;
            background: var(--primary-color);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 19px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-box button:hover {
            background: #286145;
        }

        .forgot-password {
            margin-top: 14px;
            font-size: 16px;
            color: #555;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <p>Silahkan masukkan username dan password yang sesuai</p>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                <button type="submit">Login</button>
            </form>
            @if($errors->has('login'))
                <div class="error-message">{{ $errors->first('login') }}</div>
            @endif
            <div class="forgot-password">
                Lupa Password? <a href="#">Hubungi Admin</a>
            </div>
        </div>
    </div>
@endsection