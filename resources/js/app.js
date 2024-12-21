import "./bootstrap";
import "flowbite";
import '../../vendor/masmerise/livewire-toaster/resources/js';
import {initFlowbite} from "flowbite";

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
});
