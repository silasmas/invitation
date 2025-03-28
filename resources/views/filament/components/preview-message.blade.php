<div class="mt-4 p-4 bg-gray-50 border rounded text-sm text-gray-700">
    <p class="font-semibold text-gray-800 mb-2">Aperçu du message :</p>

    @if ($this->previewMessage)
        <div>{{ $this->previewMessage }}</div>
    @else
        <div class="italic text-gray-400">Remplissez les champs requis pour voir un aperçu du message.</div>
    @endif
</div>
