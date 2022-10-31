<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Команда для создания пользователя администратора.
 */
class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать пользователя администратора';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        do {
            $login = $this->ask('Имя (логин): ', 'admin');
            if (User::whereLogin($login)->count()) {
                $this->error('Такой пользователь уже существует');
                continue;
            }
            break;
        } while(true);
        
        $password = $this->secret('Пароль: ');

        User::create([
            'login' => $login,
            'password' => Hash::make($password),
            'role_id' => Role::whereId(Role::SUPER_ADMIN_ID)->first()?->id
        ]);

        return $this->info('Пользователь успешно создан!');
    }
}
