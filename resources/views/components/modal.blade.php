@props(['id'])
<div id="{{ $id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                    Small modal
                </h3>
                <button @click="$dispatch('close-modal', { id :@js($id)})" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
                </p>
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
                </p>
            </div>
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
            </div>
        </div>
    </div>
</div>

@pushonce('scripts')
    @script
        <script>
            window.addEventListener('livewire:navigated' , () => {
                function openModal(id, options = {}) {
                    const $targetEl = document.getElementById(id);
                    if (!$targetEl) return;
                    const defaultOptions = {
                        placement: 'center',
                        backdrop: 'dynamic',
                        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
                        closable: true,
                    };
                    const mergedOptions = { ...defaultOptions, ...options };
                    const instanceOptions = {
                        id: id,
                        override: true,
                    };
                    const modal = new window.Modal($targetEl, mergedOptions, instanceOptions);
                    modal.show();
                }

                function closeModal(id, options = {}) {
                    const $targetEl = document.getElementById(id);
                    if (!$targetEl) return;
                    const modal = new window.Modal($targetEl);
                    modal.hide();
                    if (options.onHide) {
                        options.onHide();
                    }
                }

                $wire.on('open-modal', (event) => {
                    openModal(event.id, {
                        onShow: () => {
                            console.log(`Modal ${event.id} is now shown`);
                        },
                    });
                });

                $wire.on('close-modal', (event) => {
                    closeModal(event.id, {
                        onHide: () => {
                            console.log(`Modal ${event.id} has been closed`);
                        },
                    });
                });
            }, { once: true });
        </script>
    @endscript
@endpushonce
