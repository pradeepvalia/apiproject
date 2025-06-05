<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    public function show()
    {
        $template = EmailTemplate::where('type', 'donation')->first();
        return response()->json($template);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldTemplate = EmailTemplate::where('type', 'donation')->first();
        $oldData = $oldTemplate ? $oldTemplate->toArray() : null;

        $template = EmailTemplate::updateOrCreate(
            ['type' => 'donation'],
            $request->all()
        );

        // Log the activity
        if ($oldTemplate) {
            ActivityLogService::logUpdate(
                'email_template',
                "Updated email template: {$template->name}",
                $oldData,
                $template->toArray()
            );
        } else {
            ActivityLogService::logCreate(
                'email_template',
                "Created new email template: {$template->name}",
                $template->toArray()
            );
        }

        return response()->json([
            'message' => 'Email template updated successfully',
            'template' => $template
        ]);
    }
}
