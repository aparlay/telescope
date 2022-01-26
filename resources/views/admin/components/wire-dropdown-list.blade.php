<select class="form-control" wire:model="{{ $wireModel }}">
    @if ($showAny)
        <option value="">Any</option>
    @endif
    @foreach($options as $value => $label)
        <option value="{{$value}}">{{$label}}</option>
    @endforeach
</select>
