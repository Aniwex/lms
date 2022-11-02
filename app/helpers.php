<?php
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

if (!function_exists('user')) {
    /**
     * Метод для получения текущего пользователя.
     * @return User Текущий авторизованный пользователь.
     */
    function user() {
        return auth()->user();
    }
}


if (!function_exists('current_project')) {

    /**
     * Метод для получения активного проекта.
     * @return Project|null
     */
    function current_project() : ?Project 
    {
        // return optional(Project::find(
        //     session()->get('project', null)
        // ))->id;
        
        $project = request()->route()->parameter('project');
        if (!$project instanceof Project) {
            return Project::whereId($project)->first();
        }
        return $project ?? null;
    }
}

if (!function_exists('projects')) {
    /**
     * Метод для получения всех доступных проектов для текущего пользователя.
     * @return Collection Проекты, доступные текущему пользователю.
     */
    function projects() {
        return Project::my()->get();
    }
}


if (!function_exists('userSelectProject')) {
    /**
     * Выбран ли проект.
     * @return bool Выбрал ли текущий пользователь активный проект.
     */
    function userSelectProject() {
        $project = current_project();
        return !is_null($project) && !empty($project);
    }
}

if (!function_exists('phone')) {
    /**
     * Форматирование номера телефона. Пример: 79198799966 = +7 (919) 879-99-66
     * @param mixed $unformatted
     * @return string
     */
    function phone($unformatted) {
        return sprintf("+7 (%s) %s-%s-%s",
            substr($unformatted, 1, 3),
            substr($unformatted, 4, 3),
            substr($unformatted, 7, 2),
            substr($unformatted, 9),
        );
    }
}

if (!function_exists('duration')) {

    /**
     * Форматирование значения продолжительность. Пример: 4 = 4 сек, 125 = 2 мин 5 сек
     *
     * @param int $seconds Время в секундах.
     * @return string
     */
    function duration(int $seconds) {
        return $seconds < 60 ? "$seconds сек" : gmdate("i мин s сек", $seconds);
    }
}
