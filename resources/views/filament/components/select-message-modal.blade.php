<x-filament::modal id="modal-select-message" width="2xl">
    <x-slot name="heading">
        <h2 class="text-lg font-semibold">Choisissez un message</h2>
    </x-slot>

    <div class="space-y-2">
        @foreach ($messagesDisponibles as $msg)
            <div class="p-3 border rounded cursor-pointer hover:bg-gray-100"
                wire:click="remplirMessage('{{ addslashes($msg['contenu']) }}')">
                {!! nl2br(e(Str::limit($msg['contenu'], 100))) !!}
            </div>
        @endforeach
    </div>
</x-filament::modal>
<script>
    window.addEventListener('openModal', e => {
        const modal = document.getElementById(e.detail.id);
        if (modal) modal.showModal();
    });

    window.addEventListener('closeModal', e => {
        const modal = document.getElementById(e.detail.id);
        if (modal) modal.close();
    });
</script>
