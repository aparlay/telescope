<div class="row m-0">
    <div class="col-12 col-sm-10 col-lg-11">
        <input class="form-control" type="text" placeholder="Add note" wire:model="message"/>
    </div>
    <div class="col-12 col-sm-2 col-lg-1 mt-1 mt-sm-0">
        <button class="btn btn-success col-3 col-sm-12" type="button" wire:click="create()">Add</button>
    </div>

    @error('message')
        <div class="text text-danger">{{ $message }}</div>
    @enderror
</div>
