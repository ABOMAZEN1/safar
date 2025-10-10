<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Jobs\SendPushBatch;
use App\Models\User;
use App\Models\PredefinedMessage;
use Filament\Forms;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use BackedEnum ;
use Filament\Schemas\Components\Actions;

class BroadcastNotifications extends Page implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $title = 'إدارة الإشعارات';
    protected static ?string $navigationLabel = 'إدارة الإشعارات';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';
    protected string $view = 'filament.pages.broadcast-notifications';

    public $titleField = '';
    public $bodyField = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    // FORM: إرسال إشعار
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('titleField')
                ->label('عنوان الإشعار')
                ->required()
                ->maxLength(200),
    
            Textarea::make('bodyField')
                ->label('نص الإشعار')
                ->required()
                ->rows(4),
    
            Actions::make([
                Action::make('random')
                    ->label('رسالة عشوائية')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->action('loadRandomMessage'),
    
                Action::make('send')
                    ->label('إرسال الإشعار')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->action('send'),
            ]),
        ];
    }
    

    // TABLE: عرض وإدارة الرسائل الجاهزة
    public function getTableQuery()
    {
        return PredefinedMessage::query()->latest();
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('title')->label('العنوان')->sortable(),
            TextColumn::make('body')->label('النص')->wrap(),
        ];
    }

    public function getTableHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('إضافة رسالة جديدة')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->form([
                    TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(200),
                    Textarea::make('body')
                        ->label('النص')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    PredefinedMessage::create($data);
                    Notification::make()->title('تم إنشاء الرسالة بنجاح')->success()->send();
                }),
        ];
    }
    public function loadRandomMessage()
    {
        $message = PredefinedMessage::inRandomOrder()->first();
    
        if (!$message) {
            Notification::make()
                ->title('لا توجد رسائل جاهزة')
                ->body('قم بإضافة بعض الرسائل أولاً لتتمكن من الاختيار العشوائي.')
                ->warning()
                ->send();
            return;
        }
    
        $this->form->fill([
            'titleField' => $message->title,
            'bodyField' => $message->body,
        ]);
    
        Notification::make()
            ->title('تم تحميل رسالة عشوائية')
            ->body($message->title)
            ->success()
            ->send();
    }
    
    public function getTableActions(): array
    {
        return [
            Action::make('load')
                ->label('تحميل للإرسال')
                ->icon('heroicon-o-arrow-down')
                ->color('info')
                ->action(fn(PredefinedMessage $record) => $this->loadMessage($record)),

            Action::make('edit')
                ->label('تعديل')
                ->icon('heroicon-o-pencil')
                ->form([
                    TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(200),
                    Textarea::make('body')
                        ->label('النص')
                        ->required()
                        ->rows(3),
                ])
                ->action(fn(PredefinedMessage $record, array $data) => $this->updateMessage($record, $data)),

            DeleteAction::make()
                ->label('حذف')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    // METHODS ------------------------------------------------

    public function send()
    {
        $title = $this->titleField;
        $body = $this->bodyField;

        if (!$title || !$body) {
            Notification::make()->title('يرجى تعبئة جميع الحقول')->danger()->send();
            return;
        }

        $tokensQuery = User::query()->whereNotNull('firebase_token')->where('firebase_token', '!=', '');
        $total = (clone $tokensQuery)->count();
        $chunkSize = 500;

        $tokensQuery->select('firebase_token')->orderBy('id')->chunk($chunkSize, function ($users) use ($title, $body) {
            $tokens = $users->pluck('firebase_token')->filter()->unique()->values()->all();
            if ($tokens) {
                SendPushBatch::dispatch($tokens, $title, $body)->onQueue('notifications');
                Log::info('تم جدولة إرسال إشعار', ['count' => count($tokens), 'title' => $title]);
            }
        });

        Notification::make()->title("تم جدولة الإرسال إلى {$total} مستخدم(ـين)")->success()->send();
    }

    public function loadMessage(PredefinedMessage $message)
    {
        $this->form->fill([
            'titleField' => $message->title,
            'bodyField' => $message->body,
        ]);
        Notification::make()->title('تم تحميل الرسالة')->success()->send();
    }

    public function updateMessage(PredefinedMessage $record, array $data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:200',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            Notification::make()
                ->title('تحقق من الحقول')
                ->danger()
                ->body(implode(' - ', $validator->errors()->all()))
                ->send();
            return;
        }

        $record->update($data);
        Notification::make()->title('تم تحديث الرسالة')->success()->send();
    }
}
