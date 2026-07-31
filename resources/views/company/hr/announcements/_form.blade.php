@php($announcement = $announcement ?? null)

<div class="announcement-field">
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', optional($announcement)->title) }}" required>
    @error('title')<div class="announcement-error">{{ $message }}</div>@enderror
</div>

<div class="announcement-field">
    <label>Message</label>
    <textarea name="body" required>{{ old('body', optional($announcement)->body) }}</textarea>
    @error('body')<div class="announcement-error">{{ $message }}</div>@enderror
</div>

<div class="announcement-field">
    <label>Publish date &amp; time (leave blank to save as draft)</label>
    <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($announcement)->published_at?->format('Y-m-d\TH:i')) }}">
    @error('published_at')<div class="announcement-error">{{ $message }}</div>@enderror
</div>

<div class="announcement-field">
    <label class="announcement-checkbox">
        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', optional($announcement)->is_urgent) ? 'checked' : '' }}>
        Mark as urgent
    </label>
</div>
