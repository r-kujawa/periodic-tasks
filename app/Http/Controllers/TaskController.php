<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
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
                $query->where('start_date', '<=', $current)
                    ->where('end_date', '>=', $current);
            })
            ->orWhere(function ($query) use ($max) {
                $query->where('start_date', '<=', $max)
                    ->where('end_date', '>=', $max);
            })
            ->orWhere(function ($query) use ($current, $max) {
                $query->whereNull('end_date')
                    ->where(function ($query) use ($current, $max) {
                        $query->where('start_date', '<=', $current)
                            ->orWhere('start_date', '<=', $max);
                    });
            })
            ->get();

        $dates = [];

        while ($max->greaterThanOrEqualTo($current)) {
            $currentTasks = $tasks->reduce(function ($tasks, $task) use ($current, $showCompleted) {
                if ($task->start_date->greaterThan($current) || (!is_null($task->end_date) && $task->end_date->lessThan($current))) {
                    return $tasks;
                }

                $taskCompleted = $task->completed->contains(function ($completedTask) use ($current) {
                    return $completedTask->completion_date->equalTo($current);
                });

                if ((!$showCompleted) && $taskCompleted) {
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

                return [...$tasks, [
                    'id' => $task->id,
                    'name' => $task->name,
                    'completed' => $taskCompleted,
                ]];
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

    /**
     * @param \App\Models\Task $task
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completed(Task $task, Request $request)
    {
        $task->completed()->create([
            'completion_date' => $request->input('completion_date'),
        ]);

        return response()->json()->setStatusCode(201);
    }
}
