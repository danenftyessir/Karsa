@extends('layouts.app')

@section('title', 'Contact Us - ' . config('app.name'))

@push('styles')
<style>
    /* smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* team member cards */
    .team-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 2.5rem 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .team-card:hover {
        border-color: #3B82F6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .team-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .team-card:hover .team-photo {
        border-color: #3B82F6;
    }

    /* contact buttons - MINIMALIS */
    .contact-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        width: 100%;
    }

    .contact-btn:hover {
        transform: translateY(-2px);
    }

    .btn-email {
        background: #3B82F6;
        color: white;
        border: 2px solid #3B82F6;
    }

    .btn-email:hover {
        background: #2563EB;
        border-color: #2563EB;
    }

    .btn-whatsapp {
        background: white;
        color: #3B82F6;
        border: 2px solid #3B82F6;
    }

    .btn-whatsapp:hover {
        background: #3B82F6;
        color: white;
    }

    /* stagger animation */
    .team-card {
        opacity: 0;
        animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .team-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .team-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .team-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* section header animation */
    .section-header {
        opacity: 0;
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.2s forwards;
    }

    /* info cards - MINIMALIS */
    .info-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-card:hover {
        border-color: #3B82F6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .info-icon {
        width: 56px;
        height: 56px;
        background: #3B82F6;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    /* accessibility - reduced motion */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }

        .team-card:hover,
        .contact-btn:hover,
        .info-card:hover {
            transform: none;
        }
    }

    /* responsive adjustments */
    @media (max-width: 768px) {
        .team-card {
            margin-bottom: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">
    
    {{-- hero section - PERSIS seperti about us --}}
    <section class="relative h-screen min-h-[600px] overflow-hidden">
        {{-- background image --}}
        <div class="absolute inset-0">
            <img src="{{ asset('contact.jpg') }}"
                 alt="Contact Us Karsa"
                 class="w-full h-full object-cover">
            {{-- overlay gradient - lebih gelap di bawah --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/40 to-black/70"></div>
        </div>

        {{-- content - text di kiri bawah --}}
        <div class="relative h-full">
            <div class="container mx-auto px-6 h-full flex items-end pb-20">
                <div class="max-w-4xl">
                    <h1 class="text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tight drop-shadow-2xl mb-4" style="font-family: 'Montserrat', 'Poppins', 'Space Grotesk', sans-serif; font-weight: 900; color: #FFFFFF; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);">
                        Contact Us
                    </h1>
                    <p class="text-xl md:text-2xl font-medium drop-shadow-lg" style="color: #FFFFFF;">
                        Ingin Berkolaborasi Dengan Karsa? Hubungi Kami Di Sini.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- team section - MINIMALIS --}}
    <section class="py-24" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
        <div class="container mx-auto px-6">
            
            {{-- section header --}}
            <div class="text-center mb-16 section-header">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Tim Kami Siap Membantu</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Hubungi tim kami untuk informasi lebih lanjut mengenai platform Karsa dan layanan yang tersedia.
                </p>
            </div>

            {{-- team members grid - SEJAJAR --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($teamMembers as $member)
                <div class="team-card">
                    {{-- photo --}}
                    <div class="flex justify-center mb-6">
                        <img src="{{ asset($member['photo']) }}" 
                             alt="{{ $member['name'] }}"
                             class="team-photo"
                             loading="lazy">
                    </div>

                    {{-- info - SEJAJAR --}}
                    <div class="text-center mb-6 flex-grow">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $member['name'] }}</h3>
                        <p class="text-base font-semibold text-blue-600 mb-4">{{ $member['role'] }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $member['description'] }}</p>
                    </div>

                    {{-- contact buttons - SEJAJAR --}}
                    <div class="space-y-3">
                        <a href="mailto:{{ $member['email'] }}" 
                           class="contact-btn btn-email">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Kirim Email
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member['whatsapp']) }}" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="contact-btn btn-whatsapp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection