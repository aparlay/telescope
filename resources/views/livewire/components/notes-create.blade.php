<div class="row m-0">
    <div class="col-md-11">
        <input class="form-control" type="text" placeholder="Add note" wire:model="message"/>
    </div>
    <div class="col-md-1">
        <button class="btn btn-success ml-1 w-10" type="button" wire:click="create()">Add</button>
    </div>

    @error('message')
        <div class="text text-danger">{{ $message }}</div>
    @enderror
</div>
