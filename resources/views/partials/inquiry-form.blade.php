@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
<form class="inquiry-form" action="{{ route('inquiries.store', app()->getLocale()) }}" method="post">
    @csrf
    <div class="form-title form-wide"><h3>{{ __('site.send') }}</h3></div>
    <div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label><span>{{ __('site.name') }} <b>*</b></span><input name="name" value="{{ old('name') }}" required maxlength="120" placeholder="{{ __('site.name') }}"></label>
    <label><span>{{ __('site.phone') }} <b>*</b></span><input name="phone" value="{{ old('phone') }}" required inputmode="tel" dir="ltr" maxlength="30" placeholder="+966 5x xxx xxxx"></label>
    <label><span>{{ __('site.email') }}</span><input name="email" value="{{ old('email') }}" type="email" dir="ltr" placeholder="name@example.com"></label>
    <label><span>{{ __('site.required_area') }}</span><input name="required_area" value="{{ old('required_area') }}" type="number" min="1" step="0.01" inputmode="decimal" placeholder="1,500"></label>
    @isset($properties)
        <label class="form-wide"><span>{{ __('site.select_property') }}</span><select name="property_id"><option value="">{{ __('site.choose_warehouse') }}</option>@foreach($properties as $item)<option value="{{ $item->id }}" @selected(old('property_id') == $item->id)>{{ $item->code }} — {{ $item->translations->first()->name }}</option>@endforeach</select></label>
    @endisset
    @isset($property)<input type="hidden" name="property_id" value="{{ $property->id }}">@endisset
    <label class="form-wide"><span>{{ __('site.activity') }}</span><input name="activity" value="{{ old('activity') }}" maxlength="190" placeholder="{{ __('site.activity') }}"></label>
    <label class="form-wide"><span>{{ __('site.message') }}</span><textarea name="message" rows="4" maxlength="3000" placeholder="{{ __('site.message') }}…"></textarea></label>
    <button class="button form-wide form-submit" type="submit">{{ __('site.send') }} <i>↗</i></button>
</form>
