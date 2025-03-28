{{-- <div class="flex justify-end mt-6">
    <button
        type="button"
        wire:click="submit"
        class="px-6 py-2 bg-primary-600 text-white font-semibold rounded-lg shadow hover:bg-primary-700 transition disabled:opacity-50"
        :disabled="@js($this->activeChannel) === 'sms' && @js($this->smsCount) > 3"
    >
        Envoyer
    </button>
</div> --}}
<div class="flex justify-end mt-6">
    <button
        type="button"
        wire:click="submit"
        wire:loading.attr="disabled"
        class="px-6 py-2 bg-primary-600 text-white font-semibold rounded-lg shadow hover:bg-primary-700 transition disabled:opacity-50 flex items-center gap-2"
    >
        <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span wire:loading.remove>Envoyer</span>
        <span wire:loading>Envoi…</span>
    </button>
</div>
