<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request, string $locale): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['nullable', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'required_area' => ['nullable', 'numeric', 'min:1'],
            'activity' => ['nullable', 'string', 'max:190'],
            'message' => ['nullable', 'string', 'max:3000'],
            'website' => ['nullable', 'max:0'],
        ]);

        unset($validated['website']);
        $validated['locale'] = $locale;
        Inquiry::create($validated);

        return back()->with('success', __('site.form_success'));
    }
}
