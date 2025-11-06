<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    /**
     * Display a listing of the resource for administrators.
     */
    public function index(): View
    {
        $events = Event::query()
            ->orderBy('start_date')
            ->orderBy('title')
            ->get()
            ->groupBy(fn (Event $event) => $event->status);

        return view('admin.events.index', [
            'eventsByStatus' => $events,
            'statuses' => Event::statusLabels(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.events.create', [
            'event' => new Event(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['image_path'] = $this->handleImageUpload($request, null);

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): View
    {
        $backUrl = url()->previous() === url()->current() ? route('soon') : url()->previous();

        return view('events.show', [
            'event' => $event,
            'backUrl' => $backUrl,
            'canManage' => auth()->check() && auth()->user()->is_admin,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $data['image_path'] = $this->handleImageUpload($request, $event->image_path);

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->deleteImage($event->image_path);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso.');
    }

    public function soon(): View
    {
        return $this->renderPublicListing(Event::STATUS_SOON, 'soon', 'Eventos em breve');
    }

    public function inProgress(): View
    {
        return $this->renderPublicListing(Event::STATUS_IN_PROGRESS, 'inprogress', 'Eventos em andamento');
    }

    public function finished(): View
    {
        return $this->renderPublicListing(Event::STATUS_FINISHED, 'finished', 'Eventos finalizados');
    }

    protected function renderPublicListing(string $status, string $viewName, string $heading): View
    {
        $events = Event::query()
            ->orderBy('start_date')
            ->orderBy('title')
            ->get()
            ->filter(fn (Event $event) => $event->status === $status)
            ->values();

        return view($viewName, [
            'events' => $events,
            'heading' => $heading,
        ]);
    }

    protected function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function handleImageUpload(Request $request, ?string $currentPath): ?string
    {
        if (! $request->hasFile('image')) {
            return $currentPath;
        }

        $directory = public_path('uploads/events');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $image = $request->file('image');
        $filename = uniqid('event_') . '.' . $image->getClientOriginalExtension();
        $image->move($directory, $filename);

        if ($currentPath) {
            $this->deleteImage($currentPath);
        }

        return 'uploads/events/' . $filename;
    }
}