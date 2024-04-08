
<div>
    <x-input-label for="name" :value="__('Name')"/>
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                  :value="old('name', $recipe->name)" required autofocus autocomplete="name"/>
    <x-input-error class="mt-2" :messages="$errors->get('name')"/>
</div>

<div>
    <x-input-label for="featured_image" :value="__('Featured Image')"/>
    <input type="file" id="featured_image" name="featured_image" class="mt-1 block w-full"
           accept="image/*" autocomplete="featured_image"/>
    <x-input-error class="mt-2" :messages="$errors->get('featured_image')"/>

    @if($recipe->featured_image)
        <div class="mt-2">
            <img src="{{ asset($recipe->featured_image) }}" alt="{{ $recipe->name }}" class="w-32 h-32 object-cover">
        </div>
    @endif
</div>

<div>
    <x-input-label for="description" :value="__('Description')"/>
    <x-trix-input id="description" name="description" value="{!! $recipe->description->toTrixHtml() !!} " />
    <x-input-error class="mt-1 block w-full" :messages="$errors->get('description')"/>
</div>
<div>
    <x-input-label for="ingredients" :value="__('Ingredients')"/>
    <x-trix-input id="ingredients" name="ingredients" value="{!! $recipe->ingredients->toTrixHtml() !!}" />
    <x-input-error class="mt-2" :messages="$errors->get('ingredients')"/>
</div>
<div>
    <x-input-label for="instructions" :value="__('Instructions')"/>
    <x-trix-input id="instructions" name="instructions" value="{!! $recipe->instructions->toTrixHtml() !!}" />
    <x-input-error class="mt-2" :messages="$errors->get('instructions')"/>
</div>
<div>
    <x-input-label for="tags" :value="__('Tags')"/>
    <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full"
                  :value="old('tags', implode(',', $recipe->tags ?? []))" autocomplete="tags"/>
    <x-input-error class="mt-2" :messages="$errors->get('tags')"/>
</div>
