<?php

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomersResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditCustomers extends EditRecord
{
    protected static string $resource = CustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
  

  
    protected function hasRelationManagers(): bool
    {
        return false;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['user'])) {
            $userData = $data['user'];
            $user = $this->record->user;

            if (empty($userData['phone_number'])) {
                throw ValidationException::withMessages([
                    'user.phone_number' => 'رقم الهاتف لا يمكن أن يكون فارغاً.',
                ]);
            }

            $user->update([
                'name' => $userData['name'] ?? $user->name,
                'phone_number' => $userData['phone_number'] ?? $user->phone_number,
            ]);
        }

        unset($data['user']); // منع Filament من محاولة حفظ علاقة user مباشرة

        return $data;
    }
}
