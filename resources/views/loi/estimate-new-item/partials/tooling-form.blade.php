<!-- Tooling Form -->
<div class="row">
    @php
        $toolingNames = ['Dies', 'Mandrel', 'Mall Cutting', 'Mall Checking'];
        $existingToolings = [];

        if (isset($salesInfo) && $salesInfo->toolings) {
            foreach ($salesInfo->toolings as $tooling) {
                $existingToolings[$tooling->tooling] = $tooling;
            }
        }
    @endphp

    @foreach ($toolingNames as $index => $toolingName)
        @php
            $toolingData = $existingToolings[$toolingName] ?? null;
        @endphp
        <div class="col-md-6">
            <div class="form-group">
                <label for="tooling_{{ $index }}_name">{{ $toolingName }}</label>
                <input type="hidden" name="tooling[{{ $index }}][name]" value="{{ $toolingName }}">

                <div class="row">
                    <div class="col-md-6">
                        <input type="number" class="form-control" id="tooling_{{ $index }}_cavity"
                            name="tooling[{{ $index }}][cavity]"
                            value="{{ old('tooling.' . $index . '.cavity', $toolingData ? $toolingData->cavity : '') }}"
                            placeholder="Cavity" min="0">
                    </div>
                    <div class="col-md-6">
                        <input type="number" class="form-control" id="tooling_{{ $index }}_quantity"
                            name="tooling[{{ $index }}][quantity]"
                            value="{{ old('tooling.' . $index . '.quantity', $toolingData ? $toolingData->quantity : '') }}"
                            placeholder="Quantity" min="0">
                    </div>
                </div>
                <small class="text-muted">Cavity | Quantity</small>
            </div>
        </div>
    @endforeach
</div>
