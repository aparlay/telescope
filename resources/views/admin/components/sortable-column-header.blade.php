<label
    @class([
        'col sort-col',
         'sort-asc' => Arr::get($sort, $fieldName ) === 1,
          'sort-desc' => Arr::get($sort, $fieldName) === -1])
    wire:model="sort.{{ $fieldName }}"
    wire:click="sort('{{ $fieldName }}')">

    <a href="#" onclick="return false;" class="text-primary">{{ $fieldLabel }}</a>

</label>
