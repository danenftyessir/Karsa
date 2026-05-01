<nav style="background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: relative; z-index: 1000;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        
        <!-- back to home -->
        <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 0.5rem; color: #374151; text-decoration: none; font-weight: 600; font-size: 0.9375rem; transition: color 0.2s;">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back To Home
        </a>

        <div style="display: flex; gap: 2rem;">
            <a href="{{ route('contact') }}" style="color: #6b7280; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: color 0.2s;">
                Contact
            </a>
            <a href="{{ route('about') }}" style="color: #6b7280; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: color 0.2s;">
                About
            </a>
        </div>
    </div>
</nav>

<style>
    nav a:hover {
        color: #111827 !important;
    }
</style>