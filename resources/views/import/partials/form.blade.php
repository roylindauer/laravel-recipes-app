
<div>
    <x-input-label for="import_url" :value="__('URL')"/>
    <x-text-input id="import_url" name="import_url" type="text" class="mt-1 block w-full"
                  :value="old('import_url', $recipe->import_url)" required autofocus autocomplete="url"/>
    <x-input-error class="mt-2" :messages="$errors->get('import_url')"/>
</div>
