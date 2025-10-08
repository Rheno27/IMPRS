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
            padding: 0 32px;
            height: 65px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        /* LOGIN WRAPPER */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
        }

        .login-box {
            background: #fff;
            border-radius: 14px;
            padding: 40px 45px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 6px;
            font-size: 21px;
            font-weight: 700;
            color: #333;
        }

        .login-box p {
            margin-bottom: 22px;
            font-size: 15px;
            color: #555;
        }

        .login-box label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 550;
            font-size: 15px;
            color: #333;
        }

        /* Input & Button seragam panjangnya */
        .login-box input,
        .login-box button {
            width: 100%;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            border-radius: 6px;
        }

        .login-box input {
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            outline: none;
            transition: border 0.2s;
        }

        .login-box input:focus {
            border-color: var(--primary-color);
        }

        .login-box button {
            padding: 11px;
            background: var(--primary-color);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-box button:hover {
            background: #286145;
        }

        .forgot-password {
            margin-top: 10px;
            font-size: 14px;
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
            margin-top: 8px;
            font-size: 13px;
        }

        /* === RESPONSIVE === */

        /* Tablet */
        @media (max-width: 1024px) {
            .site-header {
                padding: 0 24px;
                height: 60px;
            }

            .logo-image {
                height: 48px;
            }

            .login-container {
                padding: 40px;
            }

            .login-box {
                max-width: 380px;
                padding: 35px 40px;
            }

            .login-box h2 {
                font-size: 20px;
            }

            .login-box p {
                font-size: 14px;
            }

            .login-box label {
                font-size: 14px;
            }

            .login-box input,
            .login-box button {
                font-size: 13px;
            }
        }

        /* Mobile */
        @media (max-width: 600px) {
            body {
                background-size: cover;
                background-attachment: scroll;
            }

            .site-header {
                flex-direction: column;
                height: auto;
                padding: 10px 16px;
                text-align: center;
            }

            .logo-image {
                height: 45px;
            }

            .login-container {
                padding: 20px;
            }

            .login-box {
                padding: 30px 25px;
                max-width: 95%;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            }

            .login-box h2 {
                font-size: 18px;
            }

            .login-box p {
                font-size: 13px;
            }

            .login-box label {
                font-size: 13px;
            }

            .login-box input {
                padding: 9px;
                font-size: 13px;
            }

            .login-box button {
                padding: 10px;
                font-size: 15px;
            }

            .forgot-password {
                font-size: 13px;
            }
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