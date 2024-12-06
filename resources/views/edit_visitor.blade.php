@extends('layout')

@section('content')

<h2 class="mt-3">Visitor Management</h2>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/visitor">Visitor Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Visitor</li>
    </ol>
</nav>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Edit Visitor</div>
            <div class="card-body">
                <form method="POST" action="{{ route('visitor.edit_validation') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="visitor_name"><b>Visitor Name</b></label>
                        <input type="text" id="visitor_name" name="visitor_name" class="form-control" value="{{ old('visitor_name', $data->visitor_name) }}" readonly />
                        @error('visitor_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="visitor_status"><b>Visitor Status</b></label>
                        <select id="visitor_status" name="visitor_status" class="form-control">
                            <option value="In" {{ old('visitor_status', $data->visitor_status) == 'In' ? 'selected' : '' }}>In</option>
                            <option value="Out" {{ old('visitor_status', $data->visitor_status) == 'Out' ? 'selected' : '' }}>Out</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="out_time_div" style="{{ old('visitor_status', $data->visitor_status) == 'Out' ? 'display: block;' : 'display: none;' }}">
                        <label for="visitor_out_time"><b>Out Time</b></label>
                        <input type="text" id="visitor_out_time" name="visitor_out_time" class="form-control" value="{{ old('visitor_out_time', $data->visitor_out_time) }}" readonly />
                        @error('visitor_out_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="hidden" name="row_id" value="{{ $data->id }}" />
                    
                    <div class="form-group mb-3">
                        <button type="submit" class="btn btn-primary w-100">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.getElementById('visitor_status');
    const outTimeDiv = document.getElementById('out_time_div');
    const exitTimeField = document.getElementById('visitor_out_time');

    const formatTimeWithAMPM = (hours, minutes) => {
        const period = hours >= 12 ? 'PM' : 'AM';
        const adjustedHours = hours % 12 || 12;
        return `${String(adjustedHours).padStart(2, '0')}:${String(minutes).padStart(2, '0')} ${period}`;
    };

    const getCurrentDateTime = () => {
        const now = new Date();
        const date = [
            String(now.getMonth() + 1).padStart(2, '0'),
            String(now.getDate()).padStart(2, '0'),
            now.getFullYear()
        ].join('/');

        const hours = now.getHours();
        const minutes = now.getMinutes();
        const time = formatTimeWithAMPM(hours, minutes);

        return `${date} ${time}`;
    };

    const toggleOutTimeField = () => {
        if (statusSelect.value === 'Out') {
            exitTimeField.value = getCurrentDateTime();
            outTimeDiv.style.display = 'block';
        } else {
            exitTimeField.value = '';
            outTimeDiv.style.display = 'none';
        }
    };

    statusSelect.addEventListener('change', toggleOutTimeField);
    toggleOutTimeField();
});
</script>

@endsection
