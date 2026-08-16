@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
<form class="inquiry-form" action="{{ route('inquiries.store', app()->getLocale()) }}" method="post">
    @csrf
    <div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label><span>{{ __('site.name') }} *</span><input name="name" value="{{ old('name') }}" required maxlength="120"></label>
    <label><span>{{ __('site.phone') }} *</span><input name="phone" value="{{ old('phone') }}" required inputmode="tel" dir="ltr" maxlength="30"></label>
    <label><span>{{ __('site.email') }}</span><input name="email" value="{{ old('email') }}" type="email" dir="ltr"></label>
    <label><span>{{ __('site.required_area') }}</span><input name="required_area" value="{{ old('required_area') }}" type="number" min="1" step="0.01" inputmode="decimal"></label>
    @isset($properties)
        <label class="form-wide"><span>{{ __('site.select_property') }}</span><select name="property_id"><option value="">{{ __('site.any_property') }}</option>@foreach($properties as $item)<option value="{{ $item->id }}" @selected(old('property_id') == $item->id)>{{ $item->code }} — {{ $item->translations->first()->name }}</option>@endforeach</select></label>
    @endisset
    @isset($property)<input type="hidden" name="property_id" value="{{ $property->id }}">@endisset
    <label class="form-wide"><span>{{ __('site.activity') }}</span><input name="activity" value="{{ old('activity') }}" maxlength="190"></label>
    <label class="form-wide"><span>{{ __('site.message') }}</span><textarea name="message" rows="5" maxlength="3000">{{ old('message') }}</textarea></label>
    <button class="button form-wide" type="submit">{{ __('site.send') }}</button>
</form>
