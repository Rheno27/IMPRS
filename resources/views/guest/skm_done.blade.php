@extends('layouts.app')

@section('styles')
    <style>
        .hero-section {
            background-image: url('{{ asset('image/background.png') }}');
        }
    </style>
@endsection

@section('content')
    <section id="hero" class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">TERIMA KASIH ATAS PARTISIPASI ANDA</h1>
            <p class="hero-subtitle">
                Kami sangat menghargai waktu dan jawaban yang telah Anda berikan. Masukan dari Anda akan menjadi dasar
                penting bagi kami untuk terus meningkatkan kualitas pelayanan publik.
            </p>
            <a href="{{ route('guest.dashboard') }}" class="cta-button">
                <span>Kembali</span>
                <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16.7003 31.1333L22.0503 25.7833L25.3336 22.5166C26.7169 21.1333 26.7169 18.8833 25.3336 17.5L16.7003 8.86662C15.5669 7.73329 13.6336 8.54996 13.6336 10.1333V19.4833V29.8666C13.6336 31.4666 15.5669 32.2666 16.7003 31.1333Z"
                        fill="#292D32" />
                </svg>
            </a>
        </div>
    </section>
@endsection