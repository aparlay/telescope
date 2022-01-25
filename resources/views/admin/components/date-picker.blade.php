<div class="picker-day">
    <input
        x-data
        x-on:change="value = $event.target.value; console.log($event.target.value)"
        x-ref="input"
        class="form-control"

        x-init="new Pikaday({ field: $refs.input, format: 'DD/MM/YYYY' })"
        type="text"
        {{ $attributes }}
    >
</div>
