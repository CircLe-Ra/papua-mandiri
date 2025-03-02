import "./bootstrap";
import "flowbite";
import '../../vendor/masmerise/livewire-toaster/resources/js';
import {initFlowbite} from "flowbite";

document.addEventListener('livewire:navigated', () => {
    initFlowbite();

    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar-portal');
        if (window.scrollY > 0) {
            navbar.classList.add('border-b', 'border-gray-200', 'dark:border-gray-600');
        } else {
            navbar.classList.remove('border-b', 'border-gray-200', 'dark:border-gray-600');
        }
    });
});
