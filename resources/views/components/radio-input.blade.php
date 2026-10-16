<div class="flex items-center space-x-2">
    <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        class="form-check-input"
        {{ $checked ? 'checked' : '' }}>
    <label for="{{ $id }}" class="form-check-label">{{ $label }}</label>
</div>
