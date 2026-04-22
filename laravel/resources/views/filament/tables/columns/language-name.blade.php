@php
    /** @var \App\Models\Language $record */
    $record = $getRecord();
@endphp

<div class="inline-flex items-center gap-3">
    {!! svg($record->flagIcon(), 'h-5 w-5 rounded-sm ring-1 ring-black/10', ['data-language-flag' => $record->code])->toHtml() !!}

    <span>{{ $getState() }}</span>
</div>
