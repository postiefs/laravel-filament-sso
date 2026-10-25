@props(['label' => null])

<div class="fi-sso-login" style="margin-top: 1rem; text-align: center;">
    <div style="margin-bottom: 0.75rem; font-size: 0.875rem; opacity: 0.7;">{{ trans('postiefs-sso::messages.or') }}</div>

    <x-filament::button tag="a" :href="route('sso.login')" color="gray" icon="heroicon-o-building-office" style="width: 100%;">
        {{ $label ?? trans('postiefs-sso::messages.sign_in_with_microsoft') }}
    </x-filament::button>
</div>
