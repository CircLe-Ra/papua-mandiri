    <?php

    use App\Livewire\Actions\Logout;

    $logout = function (Logout $logout) {
        $logout();

        $this->redirect('/', navigate: true);
    };

    ?>

    <div>
        <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start rtl:justify-end">
                        <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="inline-flex items-center p-2 mt-2 text-sm text-gray-500 rounded-lg ms-3 sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 z-[100]">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                            </svg>
                        </button>
                    <a href="https://flowbite.com" class="flex ms-2 md:me-24">
                            <x-application-logo />
                    </a>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-white lg:flex dark:bg-gray-800 md:hidden" sidebar-bottom-menu="" bis_skin_checked="1"  x-cloak>
                            <label class="inline-flex items-center cursor-pointer w-full">
                                <input type="checkbox" id="theme-toggle" class="sr-only peer" onclick="toggleDarkMode()" />
                                <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">
                                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                                    </span>
                            </label>
                        </div>
                        <div class="flex items-center ms-3">
                            <div>
                                <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <img x-cloak class="w-8 h-8 rounded-full object-cover"  x-data="{{ json_encode(['profile_photo_path' => auth()->user()->profile_photo_path, 'name' => auth()->user()->name]) }}" x-on:refresh-image.window="profile_photo_path = $event.detail.path"
                                     alt="Tailwind CSS Navbar component"
                                     x-bind:src="profile_photo_path ? '/storage/' + profile_photo_path : 'https://ui-avatars.com/api/?name=' + name" />
                                </button>
                            </div>
                            <div class="z-50 hidden my-2 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                                <div class="px-4 py-3" role="none">
                                    <div class="text-sm text-gray-900 dark:text-white" role="none"
                                         x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                         x-text="name"
                                         x-on:profile-updated.window="name = $event.detail.name">
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-white" role="none"
                                         x-data="{{ json_encode(['email' => auth()->user()->email]) }}"
                                         x-text="email"
                                         x-on:profile-updated.window="email = $event.detail.email">
                                    </div>
                                </div>
                                <ul class="py-1" role="none">
                                    <li>
                                        <a wire:navigate href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Dashboard</a>
                                    </li>
                                    <li>
                                        <a wire:navigate href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Profil</a>
                                    </li>
                                    <li>
                                        <a href="#" wire:click="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Keluar</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <aside id="sidebar-multi-level-sidebar"  class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
            <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{  route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}" wire:navigate>
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd" d="M19 11a2 2 0 0 1 1.995 1.85L21 13v6a2 2 0 0 1-1.85 1.995L19 21h-4a2 2 0 0 1-1.995-1.85L13 19v-6a2 2 0 0 1 1.85-1.995L15 11zm0-8a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" class="duoicon-secondary-layer" opacity="0.3" />
                            <path fill="currentColor" fill-rule="evenodd" d="M9 3a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" class="duoicon-primary-layer" />
                            <path fill="currentColor" fill-rule="evenodd" d="M9 15a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2z" class="duoicon-secondary-layer" opacity="0.3" />
                        </svg>
                    <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.master-data*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 10c4.418 0 8-1.79 8-4s-3.582-4-8-4s-8 1.79-8 4s3.582 4 8 4"/><path fill="currentColor" d="M4 12v6c0 2.21 3.582 4 8 4s8-1.79 8-4v-6c0 2.21-3.582 4-8 4s-8-1.79-8-4" opacity="0.5"/><path fill="currentColor" d="M4 6v6c0 2.21 3.582 4 8 4s8-1.79 8-4V6c0 2.21-3.582 4-8 4S4 8.21 4 6" opacity="0.7"/></svg>
                        <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Master Data</span>
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-example" class=" py-2 space-y-2 {{ request()->routeIs('admin.master-data*') ? '' : 'hidden' }}">
                        <li>
                            <a wire:navigate href="{{ route('admin.master-data.program') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.master-data.program') ? 'font-bold' : '' }}" >Data Program</a>
                        </li>
                        <li>
                            <a wire:navigate href="{{ route('admin.master-data.religion') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.master-data.religion') ? 'font-bold' : '' }}">Data Agama</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a  wire:navigate href="{{ route('admin.reception') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.reception*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}" >
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" fill-opacity="0.25" d="M5 17h4a3 3 0 0 1 3 3V10c0-2.828 0-4.243-.879-5.121C10.243 4 8.828 4 6 4H5c-.943 0-1.414 0-1.707.293S3 5.057 3 6v9c0 .943 0 1.414.293 1.707S4.057 17 5 17"/><path fill="currentColor" d="M19 17h-3a3 3 0 0 0-3 3V10c0-2.828 0-4.243.879-5.121C14.757 4 16.172 4 19 4c.943 0 1.414 0 1.707.293S21 5.057 21 6v9c0 .943 0 1.414-.293 1.707S19.943 17 19 17M5 4.15A.15.15 0 0 1 5.15 4h2.7a.15.15 0 0 1 .15.15v4.246a.25.25 0 0 1-.427.177l-.896-.896a.25.25 0 0 0-.354 0l-.896.896A.25.25 0 0 1 5 8.396z"/></svg>

                    <span class="flex-1 ms-3 whitespace-nowrap">Pembukaan</span>
                    </a>
                </li>
                <li>
                    <a  wire:navigate href="{{ route('participant.registration') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('participant.registration*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">

                        <svg  class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
                            <path fill="currentColor" d="m168.8 32.89l-32.6 32.53l21.3 21.17L190 54.08zm33.9 33.96l-9.9 9.91l123 123.04l9.9-9.9zm159.4 18.06c-3.7 0-7.4.1-10.9.3c-31.9 1.78-56.7 11.76-78.3 26.39l65.5 65.6c3.5 7.3 52 96.2 65.5 123.3c-9.7-6.4-123.4-65.4-123.4-65.4l-15.3-15.2v140.3c23.9-14.6 50.1-27.7 83.6-31.2c37.5-4 83.5 4.3 144.2 33.1V118.7c-51.7-22.99-93.3-32.89-127.2-33.69c-1.3 0-2.5-.11-3.7-.1m-230.8 1.03C100.4 88.93 63.44 99 19.05 118.7v243.4C79.85 333.3 125.8 325 163.3 329c33 5.2 58.1 15.8 83.6 31.2V201.6c-38.6-38.5-77.1-77.1-115.6-115.66m48.8 3.55l-9.9 9.89l123 123.02l9.9-9.9zM336 205.1l-27.5 27.5l55.1 27.6zM143.8 346.7c-32 .3-71.85 9.8-124.75 36v42.5c60.8-28.8 106.75-37.1 144.25-33.1c18.6 2 34.9 6.9 49.8 13.3c-4.7 6.1-9.3 13.3-13.9 21.7h117.2c-6-8.2-11.8-15.4-17.7-21.6c15-6.5 31.4-11.4 50.1-13.4c37.5-4 83.5 4.3 144.2 33.1v-42.5c-53.1-26.3-93.1-35.9-125.2-36h-3.1c-4.8.1-9.4.4-13.9.9c-34 3.6-59.6 18-85.6 34.4c-5.7-.8-13-1.8-18.3-.9c-27.2-16.2-58.2-30.4-85.5-33.5c-5.6-.6-11.5-.9-17.6-.9" />
                        </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Pendaftaran</span>
                    </a>
                </li>
                <li>
                    <a  wire:navigate href="{{ route('admin.participants') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.participants*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 256 256"><path fill="currentColor" d="m226.53 56.41l-96-32a8 8 0 0 0-5.06 0l-96 32A8 8 0 0 0 24 64v80a8 8 0 0 0 16 0V75.1l33.59 11.19a64 64 0 0 0 20.65 88.05c-18 7.06-33.56 19.83-44.94 37.29a8 8 0 1 0 13.4 8.74C77.77 197.25 101.57 184 128 184s50.23 13.25 65.3 36.37a8 8 0 0 0 13.4-8.74c-11.38-17.46-27-30.23-44.94-37.29a64 64 0 0 0 20.65-88l44.12-14.7a8 8 0 0 0 0-15.18ZM176 120a48 48 0 1 1-86.65-28.45l36.12 12a8 8 0 0 0 5.06 0l36.12-12A47.9 47.9 0 0 1 176 120"/></svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Peserta Kursus</span>
                    </a>
                </li>
                <li>
                    <a wire:navigate href="{{ route('admin.participant.absenteeism') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.participant.absenteeism*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2s7.071 0 8.535 1.464C22 4.93 22 7.286 22 12s0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12" opacity="0.5"/><path fill="currentColor" d="M10.543 7.517a.75.75 0 1 0-1.086-1.034l-2.314 2.43l-.6-.63a.75.75 0 1 0-1.086 1.034l1.143 1.2a.75.75 0 0 0 1.086 0zM13 8.25a.75.75 0 0 0 0 1.5h5a.75.75 0 0 0 0-1.5zm-2.457 6.267a.75.75 0 1 0-1.086-1.034l-2.314 2.43l-.6-.63a.75.75 0 1 0-1.086 1.034l1.143 1.2a.75.75 0 0 0 1.086 0zM13 15.25a.75.75 0 0 0 0 1.5h5a.75.75 0 0 0 0-1.5z"/></svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Absensi</span>
                    </a>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('report*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}" aria-controls="dropdown-example-1" data-collapse-toggle="dropdown-example-1">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><defs><mask id="ipTTableReport1"><g fill="none" stroke="#fff" stroke-linejoin="round" stroke-width="4"><path fill="#555555" d="M5 7a3 3 0 0 1 3-3h24a3 3 0 0 1 3 3v37H8a3 3 0 0 1-3-3z"/><path d="M35 24a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v17a3 3 0 0 1-3 3h-5z"/><path stroke-linecap="round" d="M11 12h8m-8 7h12"/></g></mask></defs><path fill="currentColor" d="M0 0h48v48H0z" mask="url(#ipTTableReport1)"/></svg>
                        <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Laporan</span>
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-example-1" class=" py-2 space-y-2 {{ request()->routeIs('report*') ? '' : 'hidden' }}">
                        <li>
                            <a wire:navigate href="{{ route('report.absenteeism') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('report.absenteeism') ? 'font-bold' : '' }}" >Absensi</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a  wire:navigate href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M19 11V8.2c0-1.12 0-1.68-.218-2.108a2 2 0 0 0-.874-.874C17.48 5 16.92 5 15.8 5H7.2c-1.12 0-1.68 0-2.108.218a2 2 0 0 0-.874.874C4 6.52 4 7.08 4 8.2v5.6c0 1.12 0 1.68.218 2.108a2 2 0 0 0 .874.874C5.52 17 6.08 17 7.2 17H14m-6-4h4M8 9h7"/><circle cx="18" cy="15" r="1"/><path stroke-linecap="round" d="M20 20s-.5-1-2-1s-2 1-2 1"/></g></svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Sertifikat</span>
                    </a>
                </li>
            </ul>

        </div>
        </aside>
    </div>

