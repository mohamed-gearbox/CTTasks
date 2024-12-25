<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TaskManagerController extends Controller
{
    /**
     * Task Manager home page
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
    {
        $projects = collect();
        $db_init = true;
        try {
            $projects = Project::with([
                'tasks' => fn ($query) => $query->orderBy('priority', 'asc')
            ])->get();
        } catch (\Exception $e) {
            $db_init = false;
        }
        return view('main', ['projects' => $projects, 'db_init' => $db_init]);
    }

    /**
     * Create or Update a project task
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $result = new \stdClass();
        $result->success = true;
        $result->message = __('Changes saved!');

        $validator =  Validator::make($request->all(), [
            'project_id' => ['required', 'numeric'],
            'action' => ['required', 'string', 'max:6'],
            'id' => ['required', 'numeric'],
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'numeric', 'min:1']
        ]);

        if ($validator->fails())
        {
            $result->success = false;
            $result->message = implode(",",$validator->messages()->all());
            return Response::json($result);
        }

        if ($request->action==='create')
        {
            $taskNameExists = Task::where([
                'project_id' => $request->project_id,
                'name' => $request->name,
            ])->first();

            if($taskNameExists)
            {
                $result->success = false;
                $result->message = __('Task with same name already exists!');
                return Response::json($result);
            }

            try {
                $project = Project::find($request->project_id);
                //Handle new tasks with higher priority by injection
                if ($request->priority <= $project->getMaxTaskPriority())
                {
                    Task::where([
                        ['project_id','=', $project->id],
                        ['priority', '>=', $request->priority]
                    ])->increment('priority');
                }
                Task::create([
                    'project_id' => $request->project_id,
                    'name' => $request->name,
                    'priority' => $request->priority
                ]);
            } catch (\Exception $e) {
                $result->success = false;
                $result->message = __('Error occurred:' . $e->getMessage());
                return Response::json($result);
            }
        } else {
            $taskNameExists = Task::where([
                ['id','!=', $request->id],
                ['project_id','=', $request->project_id],
                ['name','=',$request->name],
            ])->first();

            if($taskNameExists)
            {
                $result->success = false;
                $result->message = __('Task with same name already exists!');
                return Response::json($result);
            }

            try {
                $project = Project::find($request->project_id);
                $task = Task::find($request->id);
                //Handle new tasks with higher priority by bubble down/up
                if($task->priority < $request->priority)
                {
                    $depth = $request->priority - $task->priority;
                    Task::where('project_id',$task->project_id)
                        ->whereBetween('priority', [$task->priority+1, $task->priority+$depth])
                        ->decrement('priority');
                } else if ($task->priority > $request->priority) {
                    $depth = $task->priority - $request->priority;
                    Task::where('project_id',$project->id)
                        ->whereBetween('priority', [$task->priority-$depth, $task->priority-1])
                        ->increment('priority');
                }
                Task::where([
                    'id' => $request->id,
                ])->update([
                    'name' => $request->name,
                    'priority' => $request->priority
                ]);
            } catch (\Exception $e) {
                $result->success = false;
                $result->message = __('Error occurred:' . $e->getMessage());
                return Response::json($result);
            }
        }

        $project = Project::where('id', $request->project_id)->with([
            'tasks' => fn ($query) => $query->orderBy('priority', 'asc')
        ])->first();

        $result->project = $project;
        return Response::json($result);
    }

    /**
     * Change task priority, drag and drop handler
     * @param Request $request
     * @return JsonResponse
     */
    public function changePriority(Request $request): JsonResponse
    {
        $result = new \stdClass();
        $result->success = true;
        $result->message = __('Saved!');

        $validator =  Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'current' => ['required', 'numeric', 'min:1'],
            'future' => ['required', 'numeric', 'min:1']
        ]);

        if ($validator->fails())
        {
            $result->success = false;
            $result->message = implode(",",$validator->messages()->all());
            return Response::json($result);
        }

        try {
            $task = Task::find($request->id);
            //Handle new tasks with higher priority by bubble down/up
            if($request->current < $request->future)
            {
                $depth = $request->future - $request->current;
                Task::where('project_id',$task->project_id)
                    ->whereBetween('priority', [$task->priority+1, $task->priority+$depth])
                    ->decrement('priority');
            } else if ($request->current > $request->future) {
                $depth = $request->current - $request->future;
                Task::where('project_id',$task->project_id)
                    ->whereBetween('priority', [$task->priority-$depth, $task->priority-1])
                    ->increment('priority');
            }
            $task->priority = $request->future;
            $task->save();
        } catch (\Exception $e) {
            $result->success = false;
            $result->message = __('Error occurred:' . $e->getMessage());
            return Response::json($result);
        }
        return Response::json($result);
    }

    /**
     * Remove project task (soft delete)
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $result = new \stdClass();
        $result->success = true;
        $result->message = __('Changes saved!');

        $validator =  Validator::make($request->all(), [
            'id' => ['required', 'numeric']
        ]);

        if ($validator->fails())
        {
            $result->success = false;
            $result->message = implode(",",$validator->messages()->all());
            return Response::json($result);
        }

        $task = Task::find($request->id);
        if ($task)
        {
            //Handle up shift
            Task::where('project_id',$task->project_id)
                ->where('priority', '>' , $task->priority)
                ->decrement('priority');

            $project_id = $task->project_id;
            $task->delete();

            $project = Project::where('id', $project_id)->with([
                'tasks' => fn ($query) => $query->orderBy('priority', 'asc')
            ])->first();

            $result->project = $project;
            return Response::json($result);
        }

        $result->success = false;
        $result->message = __('Undefined task!');
        return Response::json($result);
    }
}
