{{-- <div class="text-sm mt-2" :class="smsCount > 3 ? 'text-red-600' : 'text-gray-600'">
    <span x-text="messageLength"></span> caractères |
    <span x-text="smsCount"></span> SMS estimé(s)

    <template x-if="smsCount > 3">
        <div class="text-xs text-red-500 mt-1">
            ⚠️ Attention : Le message dépasse 3 SMS (480 caractères). Cela peut générer des frais supplémentaires.
        </div>
    </template>
</div> --}}
<div class="text-sm text-gray-600">
    {{ $this->cleanMessage ? strlen($this->cleanMessage) : 0 }} caractères |
    {{ $this->smsCount }} SMS estimé(s)
</div>
