<!-- Enhanced Notes Component with Markdown Support -->
@props([
    'notes' => '',
    'title' => 'Notes',
    'showIcon' => true,
    'class' => '',
    'maxHeight' => null, // Optional max height for scrollable notes
])

<div class="enhanced-notes-section {{ $class }}" style="{{ $maxHeight ? 'max-height: ' . $maxHeight . '; overflow-y: auto;' : '' }}">
    @if($title || $showIcon)
        <div class="notes-header" style="font-weight: bold; color: #8d2d36; margin-bottom: 8px; font-size: 12px; border-bottom: 1px solid #8d2d36; padding-bottom: 8px;">
            @if($showIcon)
                <i class="fas fa-sticky-note" style="margin-right: 8px;"></i>
            @endif
            @if($title)
                {{ $title }}
            @endif
        </div>
    @endif
    
    <div class="notes-content" style="font-size: 12px; color: #333; line-height: 1.5; white-space: normal;">
        @if(!empty(trim($notes)))
            {!! \App\Helpers\MarkdownRenderer::render($notes) !!}
        @else
            <em style="color: #6c757d; font-style: italic;">No notes available</em>
        @endif
    </div>
</div>

<!-- Alternative compact version for smaller spaces -->
@props([
    'compactNotes' => '',
    'compactTitle' => 'Notes'
])

@if(isset($compactNotes))
<div class="compact-notes-section">
    @if(!empty(trim($compactNotes)))
        <small class="text-muted">
            <i class="fas fa-sticky-note mr-1"></i>
            {!! \App\Helpers\MarkdownRenderer::render($compactNotes) !!}
        </small>
    @endif
</div>
@endif

<!-- Modal for viewing full notes (optional) -->
@props([
    'modalNotes' => '',
    'modalId' => 'notesModal',
    'modalTitle' => 'Notes Details'
])

@if(!empty($modalNotes) && isset($modalId))
<!-- Button to trigger modal -->
<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
    <i class="fas fa-expand-alt"></i> View Full Notes
</button>

<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="fas fa-sticky-note mr-2"></i>{{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="notes-content" style="font-size: 14px; line-height: 1.6; max-height: 400px; overflow-y: auto;">
                    {!! \App\Helpers\MarkdownRenderer::render($modalNotes) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif