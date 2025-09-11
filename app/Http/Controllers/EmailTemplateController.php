<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('email_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'template_name' => 'required|unique:email_templates,template_name',
            'subject' => 'required',
            'type' => 'required|in:email,notifications',
            'message' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $max = $request->type === 'notifications' ? 150 : 500;
                    if (strlen($value) > $max) {
                        $fail("The {$attribute} may not be greater than {$max} characters for {$request->type}.");
                    }
                }
            ]
        ]);

        EmailTemplate::create($request->all());

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email Template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email_templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'template_name' => 'required|unique:email_templates,template_name,' . $emailTemplate->id,
            'subject' => 'required',
            'type' => 'nullable|string',
            'message' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $max = $request->type === 'notifications' ? 150 : 1000;
                    if (strlen($value) > $max) {
                        $fail("The {$attribute} may not be greater than {$max} characters for {$request->type}.");
                    }
                }
            ]
        ]);

        $emailTemplate->update($request->all());

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email Template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email Template deleted successfully.');
    }
}
