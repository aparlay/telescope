<select class="form-control" wire:model="{{ $wireModel }}">
    <option value="">Any</option>
    @foreach($options as $value => $label)
        <option value="{{$value}}">{{$label}}</option>
    @endforeach
</select>
