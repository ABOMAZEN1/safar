<?php

namespace App\Http\Controllers;

use App\Models\PredefinedMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PredefinedMessageController extends Controller
{
    /**
     * عرض كل الرسائل الجاهزة
     */
    public function index()
    {
        $messages = PredefinedMessage::latest()->get();
        return response()->json($messages);
    }

    /**
     * إنشاء رسالة جديدة
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = PredefinedMessage::create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json(['message' => 'تم إنشاء الرسالة بنجاح', 'data' => $message]);
    }

    /**
     * تعديل رسالة
     */
    public function update(Request $request, PredefinedMessage $predefinedMessage)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $predefinedMessage->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json(['message' => 'تم التعديل بنجاح', 'data' => $predefinedMessage]);
    }

    /**
     * حذف رسالة
     */
    public function destroy(PredefinedMessage $predefinedMessage)
    {
        $predefinedMessage->delete();

        return response()->json(['message' => 'تم حذف الرسالة بنجاح']);
    }
}
