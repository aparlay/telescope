<div class="row m-0">
    <div class="col-10 col-lg-11">
        <input class="form-control" type="text" placeholder="Add note" wire:model="message"/>
    </div>
    <div class="col-2 col-lg-1 p-2 p-sm-0">
        <button class="btn btn-success w-100" type="button" wire:click="create()">Add</button>
    </div>

    @error('message')
        <div class="text text-danger">{{ $message }}</div>
    @enderror
</div>
