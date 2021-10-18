<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $current = Carbon::parse($request->query('from'));
        $max = Carbon::parse($request->query('to'));
        $showCompleted = $request->query('show_completed', false);

        $tasks = Auth::user()->tasks()->with(['completed'])
            ->where(function ($query) use ($current) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', $current);
            })
            ->get();

        $dates = [];

        while ($max->greaterThanOrEqualTo($current)) {
            $currentTasks = $tasks->reduce(function ($tasks, $task) use ($current, $showCompleted) {
                if (! $showCompleted && $task->completed->contains(function ($completedTask) use ($current) {
                    return $completedTask->completion_date->equalTo($current);
                })) {
                    return $tasks;
                }

                if ($task->repeat === 'never' && $task->start_date->notEqualTo($current)) {
                    return $tasks;
                }

                if ($task->repeat === 'week' && ! in_array(strtolower($current->englishDayOfWeek), $task->week_days)) {
                    return $tasks;
                }

                if ($task->repeat === 'month' && $task->start_date->day !== $current->day) {
                    return $tasks;
                }

                if ($task->repeat === 'year' && ($task->start_date->month !== $current->month || $task->start_date->day !== $current->day)) {
                    return $tasks;
                }

                return [...$tasks, $task];
            }, []);

            if (count($currentTasks)) {
                $dates[] = [
                    'date' => $current->format('Y-m-d'),
                    'tasks' => $currentTasks,
                ];
            }

            $current->addDay();
        }

        return response()->json($dates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $request->only(['name', 'start_date', 'repeat']);

        if ($task['repeat'] !== 'never') {
            if ($task['repeat'] === 'week') {
                $task['week_days'] = join(',', $request->input('week_days'));
            }

            if ($request->input('ends') === 'on') {
                $task['end_date'] = $request->input('end_date');
            }
        }

        $task = Auth::user()->tasks()->create($task);

        return response()->json($task->toArray())->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
