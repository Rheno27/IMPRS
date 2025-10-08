@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #337354;
            --secondary-color: #ffbb00;
            --text-dark: #2d2d2d;
            --text-light: #ffffff;
            --bg-light: #fcfcfc;
            --bg-table-header: #d6e3dd;
            --border-color-light: #77a28d;
            --border-color-dark: #337354;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* HEADER */
        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--text-light);
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 32px;
            height: 65px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 55px;
            width: auto;
            object-fit: contain;
        }

        /* HERO SECTION */
        .hero-section {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 150px 32px 80px;
            text-align: center;
            overflow: hidden;
            min-height: 60vh;
            background-image: url('{{ asset('image/background.png') }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            color: var(--primary-color);
        }

        .hero-title {
            font-weight: 750;
            font-size: 46px;
            line-height: 54px;
            color: var(--primary-color);
            margin: 0;
            max-width: 1000px;
        }

        .hero-subtitle {
            font-weight: 400;
            font-size: 18px;
            line-height: 30px;
            color: var(--primary-color);
            margin: 0;
            max-width: 800px;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--secondary-color);
            color: var(--text-dark);
            font-weight: 600;
            font-size: 18px;
            line-height: 22px;
            padding: 12px 40px;
            border-radius: 35px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .cta-button:hover {
            background-color: #e0a800;
        }

        .cta-button svg {
            width: 20px;
            height: 20px;
        }

        /* === RESPONSIVE DESIGN === */

        /* Tablet (≤1024px) */
        @media (max-width: 1024px) {
            .site-header {
                padding: 0 24px;
                height: 60px;
            }

            .logo-image {
                height: 48px;
            }

            .hero-section {
                padding: 130px 24px 60px;
                min-height: 55vh;
            }

            .hero-title {
                font-size: 38px;
                line-height: 46px;
                max-width: 800px;
            }

            .hero-subtitle {
                font-size: 16px;
                line-height: 26px;
                max-width: 700px;
            }

            .cta-button {
                font-size: 16px;
                padding: 10px 32px;
            }
        }

        /* Mobile (≤600px) */
        @media (max-width: 600px) {
            .site-header {
                flex-direction: column;
                height: auto;
                padding: 10px 16px;
                text-align: center;
            }

            .logo-image {
                height: 45px;
            }

            .hero-section {
                padding: 120px 20px 40px;
                min-height: 50vh;
            }

            .hero-content {
                gap: 18px;
            }

            .hero-title {
                font-size: 30px;
                line-height: 38px;
                max-width: 90%;
            }

            .hero-subtitle {
                font-size: 14px;
                line-height: 22px;
                max-width: 90%;
            }

            .cta-button {
                font-size: 15px;
                padding: 10px 28px;
                border-radius: 30px;
            }

            .cta-button svg {
                width: 18px;
                height: 18px;
            }
        }
    </style>
@endsection


@section('content')
    <section id="hero" class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">SURVEI KEPUASAN MASYARAKAT</h1>
            <p class="hero-subtitle">
                Bapak/Ibu/Saudara yang terhormat,<br>
                Dalam rangka survey kepuasan masyarakat terhadap pelayanan publik,
                kami mohon partisipasi anda secara sukarela untuk menjawab pertanyaan berikut ini secara jujur.
            </p>
            <a href="{{ route('guest.data_responden') }}" class="cta-button">
                <span>Isi Survei</span>
                <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16.7003 31.1333L22.0503 25.7833L25.3336 22.5166C26.7169 21.1333 26.7169 18.8833 25.3336 17.5L16.7003 8.86662C15.5669 7.73329 13.6336 8.54996 13.6336 10.1333V19.4833V29.8666C13.6336 31.4666 15.5669 32.2666 16.7003 31.1333Z"
                        fill="#292D32" />
                </svg>
            </a>
        </div>
    </section>
@endsection