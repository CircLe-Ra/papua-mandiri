<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('3party/filepond/filepond.css') }}" rel="stylesheet">
    <link href="{{ asset('3party/filepond/filepond-plugin-image-preview.css') }}" rel="stylesheet">
    <style>
        .dark .filepond--panel-root {
            background-color: #333;
            /* Dark background for the panel */
            /* ... other dark styles ... */
        }

        .dark .filepond--drop-label {
            color: #ddd;
            /* Lighter text color for dark mode */
        }

        .dark .filepond--item-panel {
            background-color: #444;
            /* Dark background for file items */
        }

        .dark .filepond--drip-blob {
            background-color: #555;
            /* Dark background for the drop circle */
        }

        .dark .filepond--file-action-button {
            background-color: rgba(255, 255, 255, 0.5);
            /* Lighter background for action buttons */
            color: black;
            /* Dark text/icon color for action buttons */
        }

        /* ... other dark theme styles ... */

        /* You can also customize the colors for error and success states in dark mode */
        .dark [data-filepond-item-state*='error'] .filepond--item-panel {
            background-color: #ff5555;
            /* Darker red for errors */
        }

        .dark [data-filepond-item-state='processing-complete'] .filepond--item-panel {
            background-color: #55ff55;
            /* Darker green for success */
        }
    </style>

    <style>
        [x-cloak] { display: none !important; }
    </style>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @stack('script')
</head>
<body class="bg-gray-100 dark:bg-gray-900">
        <livewire:layout.navigation />
        <div class="py-4 px-2  sm:ml-64">
            <div class="mt-12 rounded-lg dark:border-gray-700">
                {{ $slot }}
            </div>
        </div>

        <script>

            document.addEventListener('livewire:navigated', function () {
                let themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
                let themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
                let themeToggleCheckbox = document.getElementById('theme-toggle');
                if (!themeToggleDarkIcon || !themeToggleLightIcon || !themeToggleCheckbox) {
                    return;
                }
                if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    themeToggleCheckbox.checked = true;
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                } else {
                    themeToggleDarkIcon.classList.add('hidden');
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleCheckbox.checked = false;
                }
                const toggleDarkMode = () => {
                    if (themeToggleCheckbox.checked) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                        themeToggleDarkIcon.classList.remove('hidden');
                        themeToggleLightIcon.classList.add('hidden');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                        themeToggleDarkIcon.classList.add('hidden');
                        themeToggleLightIcon.classList.remove('hidden');
                    }
                };
                themeToggleCheckbox.addEventListener('change', toggleDarkMode);
            }, { once: true });
        </script>

        <x-toaster-hub />
    <script src="{{ asset('3party/filepond/filepond-plugin-file-validate-type.js') }}"></script>
    <script src="{{ asset('3party/filepond/filepond-plugin-file-validate-size.js') }}"></script>
    <script src="{{ asset('3party/filepond/filepond-plugin-image-preview.js') }}"></script>
    <script src="{{ asset('3party/filepond/filepond.js') }}"></script>
    @stack('scripts')
</body>
</html>
