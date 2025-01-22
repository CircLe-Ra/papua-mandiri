<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="Get started with a free landing page built with Tailwind CSS and the Flowbite Blocks system.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body>
<header>
    <livewire:welcome.navigation />
</header>

<section class="bg-white dark:bg-gray-900" id="home">
    <div class="grid py-8 px-4 mx-auto max-w-screen-xl lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="place-self-center mr-auto lg:col-span-7">
            <h1 class="mb-4 max-w-2xl text-4xl font-extrabold leading-none md:text-5xl xl:text-6xl dark:text-white">Membangun Masa Depan Papua yang Mandiri dan Sejahtera</h1>
            <p class="mb-6 max-w-2xl font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">Memberdayakan Generasi Papua Melalui Pendidikan dan Pelatihan Keterampilan Kerja.</p>
            <a href="#" class="inline-flex justify-center items-center py-3 px-5 mr-3 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                Daftar
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/hero/phone-mockup.png" alt="mockup">
        </div>
    </div>
</section>

<section class="bg-white dark:bg-gray-900" >
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-12">
        <h2 class="mb-8 text-3xl font-extrabold tracking-tight leading-tight text-center text-gray-900 lg:mb-16 dark:text-white md:text-4xl">Donatur Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-gray-500 dark:text-gray-400">
            <a href="https://www.kopernik.info" class="flex justify-center items-center">
                <img class="h-7 hidden dark:block" src="{{ asset('img/kopernik.png') }}" alt="kopernik" />
                <img class="h-7 dark:hidden block" src="{{ asset('img/kopernik-dark.png') }}" alt="kopernik" />
            </a>
            <a href="https://hapin.nl" class="flex justify-center items-center">
                <img class="h-16" src="{{ asset('img/hapin.png') }}" alt="hapin" />
            </a>
            <a href="https://pjns.nl" class="flex justify-center items-center">
                <img class="h-16 hidden dark:block" src="{{ asset('img/pjns.png') }}" alt="pjns" />
                <img class="h-16 block dark:hidden" src="{{ asset('img/pjns_dark.png') }}" alt="pjns" />
            </a>
        </div>
    </div>
</section>

<section class="bg-gray-50 dark:bg-gray-800" id="about">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="mb-4 max-w-screen-md lg:mb-8">
            <span class="block mb-2 text-sm font-medium text-blue-600 dark:text-blue-500">Tentang</span>
            <h2 class="mb-4 text-4xl font-extrabold text-gray-900 dark:text-white">Lembaga Pelatihan Kerja Papua Mandiri</h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">Membangun Pemuda, Membangun Papua Melalui Pendidikan dan Pelatihan Keterampilan Kerja.</p>
        </div>
        <div class="space-y-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0">
            <div class="flex flex-col gap-5">
                <p class="text-gray-500 dark:text-gray-400 text-justify">LPK Papua Mandiri berdiri tahun 2021 sebagai kelanjutan dari program “Membangun Pemuda, Membangun Papua” yang bekerjasama dengan Kopernik selesai di bulan Juni tahun yang sama. Program ini merupakan bagian dari peningkatan kapasitas anak Muda Papua di bidang keterampilan beberapa software komputer seperti MS Office, Desain Grafis, Multimedia, Website dan AutoCAD. Dengan mempertimbangkan program ini memberi manfaat yang besar bagi anak muda Papua, maka pihak Yayasan Papua Mandiri Sentosa kemudian mendaftarkan program ini ke Dinas Tenaga Kerja Kab. Merauke di bulan Agustus 2021. Akhirnya di bulan September 2021, LPK Papua Mandiri mendapat ijin operasional untuk menjalankan beberapa program pelatihan termasuk Menjahit dan Bahasa Inggris. Hingga Mei 2024, LPK Papua Mandiri telah menyelenggarakan pelatihan sebanyak 13 Batch dengan total peserta mencapai 500 orang.</p>
                <p class="text-gray-500 dark:text-gray-400 text-justify">Sebagian besar adalah mahasiswa dan siswa. Sisanya adalah pencari kerja. LPK Papua Mandiri didirikan sebagai instrumen untuk menjalankan misi Yayasan Papua Mandiri, yaitu memandirikan OAP di bidang ekonomi, pendidikan, budaya dan kesehatan. LPK dipilih sebagai instrumen untuk memperlengkapi keterampilan kerja berbasis digital agar mampu beradaptasi dengan tantangan Dunia kerja masa kini dan masa depan.
                </p>

            </div>
            <div class="">
                <img src="{{ asset('img/papua-mandiri.jpg') }}" alt="papua mandiri" />
            </div>
        </div>
    </div>
</section>

<section class="bg-white dark:bg-gray-900" id="vision_mission">
    <div class="gap-16  py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 lg:py-16 lg:px-6">
        <div class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
            <span class="block mb-2 text-sm font-medium text-blue-600 dark:text-blue-500">Visi & Misi</span>
            <h2 class="mb-4 text-4xl font-extrabold text-gray-900 dark:text-white">Visi</h2>
            <p class="mb-4 text-justify">“Menjadi Lembaga Vokasi Terunggul Dalam Memajukan Sumber Daya Manusia Di Selatan Papua”</p>
        </div>
        <div class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
            <span class="block mb-2 text-sm font-medium text-blue-600 dark:text-blue-500">&nbsp;</span>
            <h2 class="mb-4 text-4xl font-extrabold text-gray-900 dark:text-white">Misi</h2>
            <ol class="list-decimal list-outside text-justify">
                <li class="ml-4 mb-2">Mempersiapkan Generasi Usia Produktif Untuk Memiliki Kompetensi Dalam Dunia Kerja.</li>
                <li class="ml-4 mb-2">Menyediakan Fasilitas Yang Berkualitas Dan Terkini Demi Menunjang Kegiatan Pelatihan Dan Pembelajaran.</li>
                <li class="ml-4 mb-2">Menghadirkan Para Instruktur Yang Profesional Di Bidangnya, Bersertifikat Dan Diakui Di Dunia Kerja.</li>
                <li class="ml-4 mb-2">Membangun Sistem Manejemen Yang Efektif Dan Efisien Yang Mendukung Kegiatan Pelatihan Dan Pembelajaran.</li>
                <li class="ml-4 mb-2">Membangun Jaringan Dan Menjalin Kerja Sama Dengan Pemangku Kebijakan, Pelaku Usaha Serta Pemangku Kepentingan Lainnya Agar Para Lulusan LPK Papua Mandiri Bisa Terserap Ke Dalam Dunia Kerja.</li>
                <li class="ml-4 mb-2">Melakukan Riset Secara Berkala Untuk Pengembangan Kualitas Pelatihan Maupun Manejemen.</li>
            </ol>
        </div>
    </div>
</section>

<section class="bg-gray-50 dark:bg-gray-800" id="contact">
    <div class="gap-16  py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div>
            <p class="font-medium text-blue-600 dark:text-blue-500">Kontak</p>
            <h1 class="mt-2 text-2xl font-semibold text-gray-800 md:text-3xl dark:text-white">Hubungi Kami</h1>
            <p class="mt-3 text-gray-500 dark:text-gray-400">Tim kami yang ramah selalu siap mengobrol.</p>
        </div>
        <div class="grid grid-cols-1 gap-12 mt-10 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </span>

                <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Email</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Tim kami yang ramah siap membantu.</p>
                <p class="mt-2 text-blue-500 dark:text-blue-400">papuamandiri2050@gmail.com</p>
            </div>

            <div>
                <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </span>

                <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Kantor</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Mari datang dan sapa kami di kantor pusat.</p>
                <p class="mt-2 text-blue-500 dark:text-blue-400">Papua Mandiri Center, Jl. Jaya Abadi Blorep</p>
            </div>

            <div>
                <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </span>

                <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Telepon/WhatsApp</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Senin-Jumat, pukul 8 pagi hingga 5 sore.</p>
                <p class="mt-2 text-blue-500 dark:text-blue-400">+62 813-4283-6048</p>
            </div>
        </div>
    </div>
</section>


<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">

        <div class="mx-auto max-w-screen-sm text-center">
            <h2 class="mb-4 text-4xl font-extrabold leading-tight text-gray-900 dark:text-white">Menjadi Lebih Baik Setiap Hari</h2>
            <p class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg">Ilmu sebagai Kekuatan: Membangun Masa Depan Papua</p>
        </div>
    </div>
</section>

<footer class="p-4 bg-gray-50 sm:p-6 dark:bg-gray-800">
    <div class="mx-auto max-w-screen-xl">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0">
                <a href="/" class="flex items-center" wire:navigate>
                    <x-application-logo />
                </a>
            </div>
            <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-2">

                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Ikuti Kami</h2>
                    <ul class="text-gray-600 dark:text-gray-400">
                        <li class="mb-4">
                            <a href="https://www.facebook.com/paman.santos.50/?locale=id_ID" class="hover:underline ">Facebook</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://www.instagram.com/papuamandirisentosa" class="hover:underline ">Instagram</a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/@pmtv9729" class="hover:underline">YouTube</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                    <ul class="text-gray-600 dark:text-gray-400">
                        <li class="mb-4">
                            <a href="#" class="hover:underline">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="#" class="hover:underline">Terms &amp; Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a href="https://papuamandiri.org" class="hover:underline">THENEXT</a>. All Rights Reserved.
                </span>
            <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
                <a href="https://www.facebook.com/paman.santos.50/?locale=id_ID" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                </a>
                <a href="https://www.instagram.com/papuamandirisentosa" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                </a>
                <a href="https://www.youtube.com/@pmtv9729" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m10 15l5.19-3L10 9zm11.56-7.83c.13.47.22 1.1.28 1.9c.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83c-.25.9-.83 1.48-1.73 1.73c-.47.13-1.33.22-2.65.28c-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44c-.9-.25-1.48-.83-1.73-1.73c-.13-.47-.22-1.1-.28-1.9c-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83c.25-.9.83-1.48 1.73-1.73c.47-.13 1.33-.22 2.65-.28c1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44c.9.25 1.48.83 1.73 1.73"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>
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
</body>
</html>
