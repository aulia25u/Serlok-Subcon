<!-- Important Points Form -->
<div id="importantPointsContainer">
    @if (isset($salesInfo) && $salesInfo->importantPoints && $salesInfo->importantPoints->count() > 0)
        @foreach ($salesInfo->importantPoints as $index => $point)
            <div class="row important-point-row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="important_points_{{ $index }}_item">Item</label>
                        <input type="text" class="form-control" id="important_points_{{ $index }}_item"
                            name="important_points[{{ $index }}][item]"
                            value="{{ old('important_points.' . $index . '.item', $point->item) }}" placeholder="Enter item">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="important_points_{{ $index }}_note">Note</label>
                        <textarea class="form-control" id="important_points_{{ $index }}_note"
                            name="important_points[{{ $index }}][note]" rows="2" placeholder="Enter note">{{ old('important_points.' . $index . '.note', $point->note) }}</textarea>
                    </div>
                </div>
                <div class="col-md-1">
                    @if ($index > 0)
                        <button type="button" class="btn btn-danger btn-sm btn-remove-important-point"
                            style="margin-top: 32px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="row important-point-row">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="important_points_0_item">Item</label>
                    <input type="text" class="form-control" id="important_points_0_item"
                        name="important_points[0][item]" placeholder="Enter item">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="important_points_0_note">Note</label>
                    <textarea class="form-control" id="important_points_0_note" name="important_points[0][note]" rows="2"
                        placeholder="Enter note"></textarea>
                </div>
            </div>
            <div class="col-md-1">
                <!-- Empty for alignment -->
            </div>
        </div>
    @endif
</div>

<div class="row mt-3">
    <div class="col-12">
        <button type="button" class="btn btn-secondary btn-sm" id="addImportantPointBtn">
            <i class="fas fa-plus"></i> Add Important Point
        </button>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            let importantPointIndex =
                {{ isset($salesInfo) && $salesInfo->importantPoints ? $salesInfo->importantPoints->count() : 1 }};

            // Add Important Point Row
            $(document).on('click', '#addImportantPointBtn', function() {
                const newRow = `
                <div class="row important-point-row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="important_points_${importantPointIndex}_item">Item</label>
                            <input type="text"
                                   class="form-control"
                                   id="important_points_${importantPointIndex}_item"
                                   name="important_points[${importantPointIndex}][item]"
                                   placeholder="Enter item">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="important_points_${importantPointIndex}_note">Note</label>
                            <textarea class="form-control"
                                      id="important_points_${importantPointIndex}_note"
                                      name="important_points[${importantPointIndex}][note]"
                                      rows="2"
                                      placeholder="Enter note"></textarea>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-important-point" style="margin-top: 32px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

                $('#importantPointsContainer').append(newRow);
                importantPointIndex++;
            });

            // Remove Important Point Row
            $(document).on('click', '.btn-remove-important-point', function() {
                $(this).closest('.important-point-row').remove();
            });
        });
    </script>
@endpush
